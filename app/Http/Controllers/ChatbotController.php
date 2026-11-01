<?php

namespace App\Http\Controllers;

use App\Models\ExamResult;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Registration;
use App\Models\SawCriterion;
use App\Services\GeminiAiService;
use App\Services\SawService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Memproses pesan dari widget AI ChatBot (Tunas AI Assistant).
     */
    public function sendMessage(
        Request $request,
        GeminiAiService $aiService,
        SettingService $settingService,
        SawService $sawService
    ): JsonResponse {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'history' => 'nullable|array|max:10',
            'history.*.role' => 'nullable|string|in:user,assistant',
            'history.*.content' => 'nullable|string|max:1000',
        ]);

        // Ambil data publik sekolah secara aman (cache 10 menit untuk respons cepat)
        try {
            $publicContext = Cache::remember('chatbot_public_context_v2', 600, function () use ($settingService, $sawService) {
                $programs = [];
                try {
                    $programs = Program::active()
                        ->ordered()
                        ->take(6)
                        ->get(['title', 'description'])
                        ->map(fn ($p) => [
                            'title' => $p->title,
                            'description' => $p->description,
                        ])
                        ->toArray();
                } catch (\Throwable $e) {
                    Log::warning('Chatbot: gagal memuat program unggulan: '.$e->getMessage());
                }

                $criteria = [];
                try {
                    $criteria = SawCriterion::active()
                        ->get(['code', 'name', 'weight', 'type', 'max_value'])
                        ->map(fn ($c) => [
                            'code' => $c->code,
                            'name' => $c->name,
                            'weight' => (int) round((float) $c->weight),
                            'type' => ucfirst($c->type),
                            'max_value' => (int) round((float) $c->max_value),
                        ])
                        ->toArray();
                } catch (\Throwable $e) {
                    Log::warning('Chatbot: gagal memuat kriteria SAW: '.$e->getMessage());
                }

                $feeRaw = $settingService->get('registration_fee', '250000');
                $feeNumeric = (int) preg_replace('/[^0-9]/', '', (string) $feeRaw) ?: 250000;

                return [
                    'school_name' => $settingService->get('school_name', 'SMP Tunas Harapan Bekasi'),
                    'school_address' => $settingService->get('school_address', 'Jl. Harapan Baru, Kota Bekasi, Jawa Barat'),
                    'school_phone' => $settingService->get('school_phone', '(021) 8888-9999'),
                    'school_email' => $settingService->get('school_email', 'info@smptunasharapan.sch.id'),
                    'spmb_closed' => $settingService->get('spmb_disabled') === '1',
                    'registration_fee' => 'Rp '.number_format($feeNumeric, 0, ',', '.'),
                    'saw_threshold' => (int) round($sawService->getPassingThreshold() * 100),
                    'programs' => $programs,
                    'saw_criteria' => $criteria,
                ];
            });
        } catch (\Throwable $e) {
            Log::warning('Chatbot: fallback public context: '.$e->getMessage());
            $publicContext = [
                'school_name' => 'SMP Tunas Harapan Bekasi',
                'school_address' => 'Bekasi, Jawa Barat',
                'school_phone' => '(021) 8888-9999',
                'school_email' => 'info@smptunasharapan.sch.id',
                'spmb_closed' => false,
                'registration_fee' => 'Rp 250.000',
                'saw_threshold' => 70,
                'programs' => [],
                'saw_criteria' => [],
            ];
        }

        // Konteks personal apabila pendaftar sedang login
        $applicantContext = null;
        try {
            if (Auth::check() && Auth::user()->role === 'user') {
                $user = Auth::user();
                $registration = Registration::where('user_id', $user->id)->first();

                if ($registration) {
                    $paid = Payment::where('registration_id', $registration->id)
                        ->where('status', 'success')
                        ->exists();

                    $examResult = ExamResult::where('user_id', $user->id)->first();
                    $sawCandidate = $sawService->getCandidateResult($registration->id);

                    $applicantContext = [
                        'user_name' => $user->name,
                        'has_registration' => true,
                        'registration_number' => $registration->registration_number,
                        'status_label' => $registration->status_label,
                        'payment_status' => $paid ? 'Lunas (Terverifikasi)' : 'Belum Lunas',
                        'cbt_score' => $examResult && $examResult->score !== null
                            ? ((int) round((float) $examResult->score)).' / 100'
                            : 'Belum Mengikuti Ujian CBT',
                        'saw_score' => $sawCandidate
                            ? ((int) round($sawCandidate['preference_score'] * 100)).' / 100 (Peringkat #'.$sawCandidate['rank'].')'
                            : 'Menunggu Kelengkapan Nilai Seleksi',
                    ];
                } else {
                    $applicantContext = [
                        'user_name' => $user->name,
                        'has_registration' => false,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Chatbot: gagal memuat konteks pendaftar: '.$e->getMessage());
        }

        $context = array_merge($publicContext, [
            'applicant' => $applicantContext,
        ]);

        $result = $aiService->chatWithSchoolAssistant(
            $validated['message'],
            $validated['history'] ?? [],
            $context
        );

        return response()->json($result);
    }
}
