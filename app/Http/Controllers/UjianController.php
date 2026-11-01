<?php

namespace App\Http\Controllers;

use App\Models\ExamResult;
use App\Models\Question;
use App\Models\Registration;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UjianController extends Controller
{
    private function checkVerified(): ?Registration
    {
        $registration = Registration::where('user_id', Auth::id())->where('status', 'verified')->first();
        if (! $registration) {
            return null;
        }

        return $registration;
    }

    // Halaman Instruksi
    public function index()
    {
        $registration = $this->checkVerified();
        if (! $registration) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda belum terverifikasi. Silakan tunggu konfirmasi admin.');
        }

        $countQuestions = Question::count();

        $result = ExamResult::where('user_id', Auth::id())->first();
        if ($result && $result->end_time) {
            return redirect()->route('dashboard')->with('error', 'Anda sudah menyelesaikan ujian.');
        }

        $settings = app(SettingService::class);
        $examDate = $settings->get('spmb_exam_date');
        $examTime = $settings->get('spmb_exam_time');
        $examLocation = $settings->get('spmb_exam_location');

        $examStart = null;
        $examCanStart = true;

        if ($examDate && $examTime && $examDate !== '-' && $examTime !== '-') {
            try {
                $examStart = Carbon::parse($examDate.' '.$examTime);
                $examCanStart = now()->greaterThanOrEqualTo($examStart);
            } catch (\Exception $e) {
                $examStart = null;
            }
        }

        return view('pages.pendaftar.ujian.index', compact(
            'countQuestions', 'examStart', 'examCanStart', 'examLocation'
        ));
    }

    public function start()
    {
        $registration = $this->checkVerified();
        if (! $registration) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda belum terverifikasi. Silakan tunggu konfirmasi admin.');
        }

        $settings = app(SettingService::class);
        $examDate = $settings->get('spmb_exam_date');
        $examTime = $settings->get('spmb_exam_time');

        if ($examDate && $examTime && $examDate !== '-' && $examTime !== '-') {
            try {
                $examStart = Carbon::parse($examDate.' '.$examTime);
                if (now()->lessThan($examStart)) {
                    return redirect()->route('pendaftar.ujian.index')->with('error', 'Ujian belum dimulai. Silakan tunggu hingga waktu yang ditentukan.');
                }
            } catch (\Exception $e) {
                // abaikan jika format tanggal tidak valid
            }
        }

        $result = ExamResult::where('user_id', Auth::id())->first();

        // Jika sudah pernah menyelesaikan ujian, jangan izinkan mulai lagi
        if ($result && $result->end_time) {
            return redirect()->route('dashboard')->with('error', 'Anda sudah menyelesaikan ujian.');
        }

        // Jika belum pernah mulai, pilih 50 soal acak lalu KUNCI ID soalnya
        if (! $result) {
            $questionIds = Question::inRandomOrder()->take(50)->pluck('id')->toArray();

            if (empty($questionIds)) {
                return redirect()->route('dashboard')->with('error', 'Bank soal ujian belum tersedia. Silakan hubungi admin.');
            }

            $result = ExamResult::create([
                'user_id' => Auth::id(),
                'start_time' => now(),
                'question_ids' => $questionIds,
            ]);
        } elseif (empty($result->question_ids)) {
            // Backup jika record lama belum memiliki question_ids
            $questionIds = Question::inRandomOrder()->take(50)->pluck('id')->toArray();
            $result->update(['question_ids' => $questionIds]);
        }

        $durationMinutes = 60; // Durasi standar 60 menit
        $endTime = $result->start_time->copy()->addMinutes($durationMinutes);

        // Jika waktu pengerjaan sudah melewati batas (dengan toleransi 2 menit)
        if (now()->greaterThanOrEqualTo($endTime->copy()->addMinutes(2))) {
            if (! $result->end_time) {
                $result->update(['end_time' => $endTime]);
            }

            return redirect()->route('dashboard')->with('error', 'Waktu ujian telah habis.');
        }

        // Ambil soal persis sesuai urutan question_ids yang terkunci
        $questionIds = $result->question_ids;
        $questions = Question::whereIn('id', $questionIds)
            ->get()
            ->sortBy(fn ($q) => array_search($q->id, $questionIds))
            ->values();

        return view('pages.pendaftar.ujian.start', compact('questions', 'endTime'));
    }

    // Simpan Jawaban
    public function submit(Request $request)
    {
        $registration = $this->checkVerified();
        if (! $registration) {
            return redirect()->route('dashboard')->with('error', 'Akun Anda belum terverifikasi.');
        }

        return DB::transaction(function () use ($request) {
            $result = ExamResult::where('user_id', Auth::id())->lockForUpdate()->first();

            if (! $result) {
                return redirect()->route('dashboard')->with('error', 'Sesi ujian tidak ditemukan. Silakan mulai ujian terlebih dahulu.');
            }

            if ($result->end_time) {
                return redirect()->route('dashboard')->with('error', 'Ujian ini sudah pernah diselesaikan. Hasil tidak dapat diubah.');
            }

            $userAnswers = $request->input('answers', []);
            if (! is_array($userAnswers)) {
                $userAnswers = [];
            }

            $assignedIds = $result->question_ids ?? [];
            if (empty($assignedIds)) {
                $assignedIds = array_keys($userAnswers);
            }

            $questions = Question::whereIn('id', $assignedIds)->get();

            $earnedPoints = 0;
            $maxPoints = 0;

            foreach ($questions as $q) {
                $qPoints = max(1, (int) $q->points);
                $maxPoints += $qPoints;

                if (isset($userAnswers[$q->id]) && strtoupper((string) $userAnswers[$q->id]) === strtoupper((string) $q->correct_answer)) {
                    $earnedPoints += $qPoints;
                }
            }

            // Normalisasi ke skala 0 - 100 agar konsisten dengan Kriteria C1 SPK SAW
            $normalizedScore = $maxPoints > 0
                ? round(($earnedPoints / $maxPoints) * 100, 2)
                : 0;

            $result->update([
                'score' => $normalizedScore,
                'end_time' => Carbon::now(),
                'answers' => $userAnswers,
            ]);

            return redirect()->route('dashboard')->with('success', 'Ujian telah selesai. Nilai Anda berhasil direkam.');
        });
    }
}
