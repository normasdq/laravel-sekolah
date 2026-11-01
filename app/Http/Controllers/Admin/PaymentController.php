<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\SettingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('registration.user')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('registration', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $payments = $query->paginate(15)->withQueryString();

        $statusCounts = [
            'total' => Payment::count(),
            'success' => Payment::success()->count(),
            'pending' => Payment::pending()->count(),
            'expired' => Payment::where('status', 'expired')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
        ];

        return view('admin.payments.index', compact('payments', 'statusCounts'));
    }

    public function show($id)
    {
        $payment = Payment::with('registration.user')->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }

    public function exportCsv()
    {
        $payments = Payment::with('registration')->latest()->get();

        $csvData = [];
        $csvData[] = [
            'ID', 'Nama Pendaftar', 'No. Pendaftaran', 'NISN',
            'Tagihan', 'Status', 'Metode', 'ID Transaksi',
            'Dibayar Pada', 'Dibuat',
        ];

        foreach ($payments as $p) {
            $csvData[] = [
                $p->id,
                $p->registration->name ?? '-',
                $p->registration->registration_number ?? '-',
                $p->registration->nisn ?? '-',
                $p->amount,
                $p->status_label,
                $p->method ?? '-',
                $p->transaction_id,
                $p->paid_at?->format('d/m/Y H:i') ?? '-',
                $p->created_at->format('d/m/Y H:i'),
            ];
        }

        $filename = 'data_pembayaran_'.date('Y-m-d_His').'.csv';

        return response()->streamDownload(function () use ($csvData) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            foreach ($csvData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportPdf()
    {
        $payments = Payment::with('registration')->latest()->get();
        $schoolName = app(SettingService::class)->get('school_name', 'Sekolah');

        $pdf = Pdf::loadView('admin.payments.pdf', compact('payments', 'schoolName'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('data-pembayaran-'.date('d-m-Y').'.pdf');
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return redirect()->back()->with('success', 'Data pembayaran berhasil dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dipilih.');
        }
        Payment::whereIn('id', $ids)->delete();

        return redirect()->back()->with('success', count($ids).' data pembayaran berhasil dihapus.');
    }
}
