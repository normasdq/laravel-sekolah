<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\SawCriterion;
use App\Models\SawScore;
use App\Models\SiteSetting;
use App\Services\SawService;
use App\Services\SettingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SawController extends Controller
{
    public function index(SawService $sawService)
    {
        $data = $sawService->calculate();

        return view('admin.spk.index', $data);
    }

    public function scores(Request $request, SawService $sawService)
    {
        $criteria = SawCriterion::active()->get();

        $query = Registration::whereIn('status', ['verified', 'lulus', 'tidak_lulus'])
            ->with(['sawScores', 'examResult'])
            ->orderBy('name');

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        $registrations = $query->get();
        $calculation = $sawService->calculate();
        $rankingByRegId = collect($calculation['ranking'])->keyBy(fn ($item) => $item['registration']->id);
        $threshold = $calculation['threshold'];

        return view('admin.spk.scores', compact('criteria', 'registrations', 'rankingByRegId', 'threshold'));
    }

    public function saveScores(Request $request)
    {
        $scoresInput = $request->input('scores', []);
        if (! is_array($scoresInput)) {
            return redirect()->back()->with('error', 'Format input nilai tidak valid.');
        }

        $manualCriteria = SawCriterion::active()
            ->where('source', 'manual')
            ->get()
            ->keyBy('id');

        $errors = [];
        $upsertRows = [];
        $userId = Auth::id();
        $now = now();

        foreach ($scoresInput as $regId => $criterionValues) {
            if (! is_array($criterionValues)) {
                continue;
            }

            foreach ($criterionValues as $criterionId => $rawValue) {
                if ($rawValue === null || $rawValue === '') {
                    continue;
                }

                $criterion = $manualCriteria->get((int) $criterionId);
                if (! $criterion) {
                    continue;
                }

                if (! is_numeric($rawValue)) {
                    $errors["scores.{$regId}.{$criterionId}"] = "Nilai {$criterion->code} harus berupa angka.";

                    continue;
                }

                $numValue = (float) $rawValue;
                if ($numValue < $criterion->min_value || $numValue > $criterion->max_value) {
                    $errors["scores.{$regId}.{$criterionId}"] = "Nilai {$criterion->code} harus berada di antara {$criterion->min_value} dan {$criterion->max_value}.";

                    continue;
                }

                $upsertRows[] = [
                    'registration_id' => (int) $regId,
                    'saw_criterion_id' => (int) $criterion->id,
                    'value' => $numValue,
                    'updated_by' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($upsertRows) {
            if (! empty($upsertRows)) {
                SawScore::upsert(
                    $upsertRows,
                    ['registration_id', 'saw_criterion_id'],
                    ['value', 'updated_by', 'updated_at']
                );
            }
        });

        return redirect()
            ->route('admin.spk.scores')
            ->with('success', 'Data nilai kriteria siswa berhasil disimpan.');
    }

    public function updateThreshold(Request $request, SettingService $settingService)
    {
        $validated = $request->validate([
            'threshold' => ['required', 'numeric', 'min:1', 'max:100'],
        ], [
            'threshold.required' => 'Nilai ambang batas wajib diisi.',
            'threshold.min' => 'Nilai ambang batas minimal 1.',
            'threshold.max' => 'Nilai ambang batas maksimal 100.',
        ]);

        $intThreshold = (int) round((float) $validated['threshold']);
        $settingService->set('saw_threshold', (string) $intThreshold);

        return redirect()
            ->route('admin.spk.index')
            ->with('success', "Ambang batas kelulusan SAW berhasil diperbarui menjadi {$intThreshold}.");
    }

    public function apply(Request $request, SawService $sawService, SettingService $settingService)
    {
        $threshold = $request->filled('threshold')
            ? (float) $request->input('threshold')
            : $sawService->getThreshold();

        $defaultFee = $request->filled('default_fee')
            ? max(0.0, (float) $request->input('default_fee'))
            : $sawService->getDefaultRegistrationFee();

        if ($request->filled('default_fee')) {
            $settingService->set('spmb_re_registration_fee', (string) (int) round($defaultFee));
        }

        $result = $sawService->applyResults($threshold, $defaultFee);

        if ($result['total'] === 0) {
            return redirect()
                ->route('admin.spk.index')
                ->with('error', 'Belum ada pendaftar dengan nilai kriteria lengkap yang dapat diterapkan.');
        }

        return redirect()
            ->route('admin.spk.index')
            ->with('success', "Keputusan SPK SAW berhasil diterapkan ke {$result['total']} calon siswa ({$result['passed']} Lulus, {$result['failed']} Tidak Lulus).");
    }

    public function exportPdf(SawService $sawService, SettingService $settingService)
    {
        $data = $sawService->calculate();
        $data['schoolName'] = $settingService->get('school_name', 'SMP Tunas Harapan');
        $data['printedAt'] = now()->translatedFormat('d F Y, H:i');

        $pdf = Pdf::loadView('admin.spk.pdf', $data)->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_SPK_SAW_'.date('Y-m-d_His').'.pdf');
    }
}

