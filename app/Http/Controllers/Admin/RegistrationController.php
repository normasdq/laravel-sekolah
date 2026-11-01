<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateRegistrationRequest;
use App\Models\Registration;
use App\Services\GeminiAiService;
use App\Services\SettingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    public function index(Request $request, \App\Services\SawService $sawService)
    {
        $query = Registration::latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $registrations = $query->paginate(15)->withQueryString();

        $sawCalc = $sawService->calculate();
        $sawMap = collect($sawCalc['ranking'])->keyBy(fn ($item) => $item['registration']->id);

        $statusCounts = [
            'total' => Registration::count(),
            'pending' => Registration::pending()->count(),
            'verified' => Registration::verified()->count(),
            'lulus' => Registration::lulus()->count(),
            'tidak_lulus' => Registration::where('status', 'tidak_lulus')->count(),
            'rejected' => Registration::rejected()->count(),
        ];

        return view('admin.registrations.index', compact('registrations', 'statusCounts', 'sawMap'));
    }

    public function show(Registration $registration, \App\Services\SawService $sawService)
    {
        $registration->load(['sawScores', 'examResult']);
        $sawCandidate = $sawService->getCandidateResult($registration->id);
        $criteria = \App\Models\SawCriterion::active()->get();

        return view('admin.registrations.show', compact('registration', 'sawCandidate', 'criteria'));
    }

    public function update(UpdateRegistrationRequest $request, Registration $registration)
    {
        $data = $request->validated();

        // Jika status diubah manual menjadi lulus dan biaya daftar ulang masih kosong, isi dengan biaya default
        if (($data['status'] ?? '') === 'lulus' && (! $registration->payment_amount || (float) $registration->payment_amount <= 0)) {
            $defaultFee = (float) app(SettingService::class)->get('spmb_re_registration_fee', 150000);
            if ($defaultFee > 0) {
                $data['payment_amount'] = $defaultFee;
                $data['payment_status'] = $registration->payment_status ?: 'unpaid';
            }
        }

        $registration->update($data);

        return redirect()
            ->route('admin.registrations.show', $registration)
            ->with('success', 'Status pendaftaran berhasil diperbarui.');
    }

    public function destroy(Registration $registration)
    {
        $registration->delete();

        return redirect()->route('admin.registrations.index')->with('success', 'Data pendaftar berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }
        Registration::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', count($ids).' data pendaftar berhasil dihapus.');
    }

    /**
     * Export data ke CSV
     */
    public function export()
    {
        $registrations = Registration::latest()->get();

        // Siapkan header CSV
        $csvData = [];
        $csvData[] = ['ID', 'Nama', 'NISN', 'Asal Sekolah', 'Telepon', 'Email', 'Jenis Kelamin', 'Tanggal Lahir', 'Tempat Lahir', 'Alamat', 'Nama Orang Tua', 'Telepon Orang Tua', 'Status', 'Tanggal Daftar'];

        // Loop data dan masukkan ke array
        foreach ($registrations as $reg) {
            $csvData[] = [
                $reg->id,
                $reg->name,
                $reg->nisn,
                $reg->school_origin,
                $reg->phone,
                $reg->email,
                $reg->gender_label, // Pastikan ada accessor di model
                $reg->birth_date?->format('d/m/Y'),
                $reg->birth_place,
                $reg->address,
                $reg->parent_name,
                $reg->parent_phone,
                $reg->status_label, // Pastikan ada accessor di model
                $reg->created_at->format('d/m/Y H:i'),
            ];
        }

        // Download File
        $filename = 'spmb_data_'.date('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($csvData) {
            $handle = fopen('php://output', 'w');
            // Tambahkan BOM agar UTF-8 terbaca dengan benar di Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Export data ke PDF
     */
    public function exportPdf()
    {
        // Ambil data pendaftar, misal urutkan berdasarkan tanggal terbaru
        $registrations = Registration::latest()->get();

        // Ambil setting nama sekolah (opsional, untuk judul PDF)
        $schoolName = app(SettingService::class)->get('school_name', 'Sekolah');

        // Load view PDF dan kirim datanya
        // Pastikan Anda sudah membuat view 'admin.registrations.pdf'
        $pdf = Pdf::loadView('admin.registrations.pdf', compact('registrations', 'schoolName'));

        // Download file PDF
        return $pdf->download('data-pendaftar-'.date('d-m-Y').'.pdf');
    }

    public function verifyDocuments($id)
    {
        $registration = Registration::findOrFail($id);

        $registration->update([
            'status' => 'verified',
            'documents_verified' => true,
        ]);

        // Ambil Data Jadwal dari Settings
        $examDate = app(SettingService::class)->get('spmb_exam_date', '-');
        $examLocation = app(SettingService::class)->get('spmb_exam_location', '-');
        $examTime = app(SettingService::class)->get('spmb_exam_time', '-');

        // Buat Pesan Sukses
        $message = "Selamat, data Anda terverifikasi lengkap! Silahkan mengikuti ujian seleksi online (gratis).\n\n";
        $message .= "📅 Tanggal: $examDate\n";
        $message .= "⏰ Waktu: $examTime\n";
        $message .= "📍 Lokasi: $examLocation";

        // Simpan ke notes
        $registration->update(['notes' => $message]);

        return redirect()->back()->with('success', 'Data siswa diverifikasi dan jadwal tes diberikan.');
    }

    public function updatePayment(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'payment_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:unpaid,paid',
        ]);

        $registration->update([
            'payment_amount' => $validated['payment_amount'] ?? null,
            'payment_status' => $validated['payment_status'],
            'paid_at' => $validated['payment_status'] === 'paid' ? now() : null,
        ]);

        return redirect()->back()->with('success', 'Biaya pendaftaran berhasil diperbarui.');
    }

    public function showDocument(Registration $registration, string $type)
    {
        if (! in_array($type, ['kartu_keluarga', 'ijazah', 'akte_kelahiran', 'pas_foto'])) {
            abort(404);
        }

        $path = $registration->{$type};
        if (! $path) {
            abort(404, 'Dokumen belum diunggah.');
        }

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->response($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path);
        }

        abort(404, 'File dokumen tidak ditemukan.');
    }

    /**
     * Audit berkas pendaftar (KK, Akte, Ijazah) menggunakan AI OCR Multimodal.
     */
    public function aiAudit(Registration $registration, GeminiAiService $aiService)
    {
        $result = $aiService->auditRegistrationDocuments($registration);

        return response()->json($result);
    }
}

