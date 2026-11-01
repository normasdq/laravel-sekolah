<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\SettingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PublicRegistrationController extends Controller
{
    public function store(Request $request)
    {
        // 0. CEK APAKAH SPMB SEDANG DITUTUP
        $settings = app(SettingService::class);
        if ($settings->get('spmb_disabled') === '1') {
            return redirect()->route('dashboard')->with('error', 'Pendaftaran SPMB saat ini sedang ditutup.');
        }

        // 1. CEK APAKAH USER SUDAH PERNAH MENDAFTAR
        $existing = Registration::where('user_id', Auth::id())->first();
        if ($existing) {
            return redirect()->route('dashboard')->with('error', 'Anda sudah melakukan pendaftaran dengan nomor: '.$existing->registration_number);
        }

        // 2. VALIDASI DATA
        $validated = $request->validate([
            // Data Pribadi
            'name' => 'required|string|max:255',
            'nisn' => [
                'required',
                'numeric',
                'digits:10',
                Rule::unique('registrations', 'nisn')->withoutTrashed(),
            ],
            'school_origin' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date|before:today',
            'birth_place' => 'required|string|max:255',
            'address' => 'required|string|max:1000',

            // Data Orang Tua
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',

            // Dokumen (Wajib, batas 10MB mendukung foto HP resolusi tinggi)
            'pas_foto' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
            'kartu_keluarga' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'ijazah' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'akte_kelahiran' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
        ], [
            'name.required' => 'Nama lengkap wajib diisi sesuai ijazah/akta.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.numeric' => 'NISN harus berupa angka.',
            'nisn.digits' => 'NISN harus tepat berjumlah 10 digit angka.',
            'nisn.unique' => 'NISN ini sudah pernah didaftarkan dalam sistem.',
            'school_origin.required' => 'Asal sekolah (SD/MI) wajib diisi.',
            'phone.required' => 'Nomor WhatsApp aktif calon siswa/wali wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Pilihan jenis kelamin tidak valid.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
            'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
            'birth_place.required' => 'Tempat lahir (kota/kabupaten) wajib diisi.',
            'address.required' => 'Alamat lengkap tempat tinggal wajib diisi.',
            'parent_name.required' => 'Nama lengkap orang tua/wali wajib diisi.',
            'parent_phone.required' => 'Nomor WhatsApp orang tua/wali wajib diisi.',
            'pas_foto.required' => 'File pas foto digital calon siswa (3x4 atau 4x6) wajib diunggah.',
            'pas_foto.max' => 'Ukuran file pas foto maksimal 10MB.',
            'pas_foto.mimes' => 'Format pas foto harus berupa file gambar JPG, JPEG, PNG, atau WEBP.',
            'kartu_keluarga.required' => 'File scan Kartu Keluarga (KK) wajib diunggah.',
            'kartu_keluarga.max' => 'Ukuran file Kartu Keluarga maksimal 10MB.',
            'kartu_keluarga.mimes' => 'Format Kartu Keluarga harus berupa PDF, JPG, JPEG, PNG, atau WEBP.',
            'ijazah.required' => 'File scan Ijazah atau Surat Keterangan Lulus (SKL) wajib diunggah.',
            'ijazah.max' => 'Ukuran file Ijazah/SKL maksimal 10MB.',
            'ijazah.mimes' => 'Format Ijazah/SKL harus berupa PDF, JPG, JPEG, PNG, atau WEBP.',
            'akte_kelahiran.required' => 'File scan Akte Kelahiran wajib diunggah.',
            'akte_kelahiran.max' => 'Ukuran file Akte Kelahiran maksimal 10MB.',
            'akte_kelahiran.mimes' => 'Format Akte Kelahiran harus berupa PDF, JPG, JPEG, PNG, atau WEBP.',
        ]);

        // 3. PROSES UPLOAD FILE (Disimpan di disk privat 'local')
        $filePaths = [];
        $documents = ['pas_foto', 'kartu_keluarga', 'ijazah', 'akte_kelahiran'];

        foreach ($documents as $doc) {
            if ($request->hasFile($doc)) {
                $filePaths[$doc] = $request->file($doc)->store('documents/spmb', 'local');
            }
        }

        // 4. SIMPAN KE DATABASE SECARA ATOMIK DENGAN LOCK UNTUK MENCEGAH RACE CONDITION
        $registrationNumber = DB::transaction(function () use ($validated, $filePaths) {
            $year = date('Y');
            $lastReg = Registration::whereYear('created_at', $year)
                ->lockForUpdate()
                ->latest('id')
                ->first();

            $nextNumber = $lastReg ? ((int) substr($lastReg->registration_number, -4) + 1) : 1;
            $regNumber = 'REG-'.$year.'-'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            Registration::create(array_merge($validated, $filePaths, [
                'user_id' => Auth::id(),
                'registration_number' => $regNumber,
                'status' => 'pending', // Default status
            ]));

            // Sinkronkan nama akun user dengan nama resmi calon siswa yang didaftarkan
            if (Auth::user() && Auth::user()->name !== $validated['name']) {
                Auth::user()->update(['name' => $validated['name']]);
            }

            return $regNumber;
        });

        // 5. REDIRECT KE DASHBOARD
        return redirect()->route('dashboard')->with('success', 'Pendaftaran Berhasil! Nomor Anda: '.$registrationNumber);
    }

    public function showDocument(string $type)
    {
        if (! in_array($type, ['kartu_keluarga', 'ijazah', 'akte_kelahiran', 'pas_foto'])) {
            abort(404);
        }

        $registration = Registration::where('user_id', Auth::id())->firstOrFail();
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

    public function updatePhoto(Request $request)
    {
        $registration = Registration::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'pas_foto' => 'required|file|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'pas_foto.required' => 'Pilih file pas foto yang ingin diunggah.',
            'pas_foto.mimes' => 'Format file pas foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'pas_foto.max' => 'Ukuran file pas foto maksimal 10MB.',
        ]);

        if ($registration->pas_foto && Storage::disk('local')->exists($registration->pas_foto)) {
            Storage::disk('local')->delete($registration->pas_foto);
        }

        $path = $request->file('pas_foto')->store('documents/spmb', 'local');
        $registration->update(['pas_foto' => $path]);

        return back()->with('success', 'Pas foto digital berhasil diunggah!');
    }

    public function downloadBukti()
    {
        // 1. Ambil data pendaftaran milik user yang sedang login
        $registration = Registration::where('user_id', Auth::id())->first();

        // 2. Jika data tidak ditemukan, kembalikan dengan pesan error
        if (! $registration) {
            return redirect()->route('dashboard')->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        // 3. Ambil setting sekolah (menggunakan service yang Anda punya)
        $settings = app(SettingService::class);

        // 4. Load view khusus PDF dan lempar datanya
        // Pastikan file view ini ada di: resources/views/pages/pendaftar/bukti-pdf.blade.php
        $pdf = Pdf::loadView('pages.pendaftar.pdf.bukti-pendaftaran', compact('registration', 'settings'))
            ->setPaper('a4', 'portrait');

        // 5. Download file PDF-nya
        return $pdf->download('Bukti_Pendaftaran_'.$registration->registration_number.'.pdf');
    }

    public function downloadKartu()
    {
        $registration = Registration::where('user_id', Auth::id())->first();

        if (! $registration) {
            return redirect()->route('dashboard')->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        $settings = app(SettingService::class);

        // Siapkan Pas Foto dalam format Base64 agar dapat di-render Dompdf tanpa error
        $photoDataUri = null;
        if ($registration->pas_foto) {
            $path = storage_path('app/private/'.$registration->pas_foto);
            if (! file_exists($path)) {
                $path = storage_path('app/'.$registration->pas_foto);
            }
            if (file_exists($path)) {
                $mime = mime_content_type($path) ?: 'image/jpeg';
                $photoDataUri = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
            }
        }

        $pdf = Pdf::loadView('pages.pendaftar.pdf.kartu-peserta', compact('registration', 'settings', 'photoDataUri'))
            ->setPaper([0, 0, 520, 720], 'portrait');

        return $pdf->download('Kartu_Peserta_'.$registration->registration_number.'.pdf');
    }
}
