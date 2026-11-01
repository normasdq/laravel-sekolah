<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\SawService;
use App\Services\SettingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStatusController extends Controller
{
    public function show(SawService $sawService)
    {
        $registration = Registration::where('user_id', Auth::id())->first();

        if (! $registration) {
            return redirect()->route('spmb')->with('error', 'Anda belum mengisi formulir pendaftaran.');
        }

        $checkedRegistration = session('registration');
        $sawResult = null;

        if ($checkedRegistration) {
            $checkedRegistration = $registration->fresh();
            if (in_array($checkedRegistration->status, ['verified', 'lulus', 'tidak_lulus'])) {
                $sawResult = $sawService->getCandidateResult($checkedRegistration->id);
            }
        }

        return view('pages.pendaftar.cek-status', compact('registration', 'checkedRegistration', 'sawResult'));
    }

    public function check(Request $request)
    {
        $request->validate([
            'nisn' => 'required|digits:10',
        ]);

        // Cegah IDOR: batasi pengecekan hanya pada data pendaftaran milik user yang login
        $registration = Registration::where('user_id', Auth::id())
            ->where('nisn', $request->nisn)
            ->first();

        if (! $registration) {
            return redirect()->route('pendaftar.cek-status')->with('not_found', true)->withInput();
        }

        return redirect()->route('pendaftar.cek-status')->with('registration', $registration);
    }

    public function downloadPdf(Request $request)
    {
        // Cegah IDOR: hanya unduh bukti pendaftaran milik user yang sedang login
        $registration = Registration::where('user_id', Auth::id())->firstOrFail();

        $settings = app(SettingService::class);

        $pdf = Pdf::loadView('pages.pendaftar.pdf.bukti-pendaftaran', compact('registration', 'settings'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Bukti-Pendaftaran-'.$registration->registration_number.'.pdf');
    }
}
