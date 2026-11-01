<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\SawCriterion;
use Illuminate\Support\Facades\DB;

class SawService
{
    /**
     * Ambil ambang batas kelulusan dari pengaturan situs (default: 0.70 / 70).
     */
    public function getThreshold(): float
    {
        $val = app(SettingService::class)->get('saw_threshold', '70');

        if (! is_numeric($val)) {
            return 0.70;
        }

        $num = (float) $val;

        return $num > 1.0 ? ($num / 100.0) : $num;
    }

    /**
     * Ambil nominal biaya daftar ulang default dari pengaturan situs (default: 150000).
     */
    public function getDefaultRegistrationFee(): float
    {
        $val = app(SettingService::class)->get('spmb_re_registration_fee', '150000');

        return is_numeric($val) && (float) $val >= 0 ? (float) $val : 150000.0;
    }

    /**
     * Jalankan seluruh tahapan perhitungan metode SAW (Simple Additive Weighting).
     */
    public function calculate(?float $threshold = null): array
    {
        $threshold = $threshold ?? $this->getThreshold();
        if ($threshold > 1.0) {
            $threshold = $threshold / 100.0;
        }
        $criteria = SawCriterion::active()->get();

        $totalWeight = (float) $criteria->sum('weight');
        $weights = [];
        foreach ($criteria as $criterion) {
            $weights[$criterion->id] = $totalWeight > 0
                ? ((float) $criterion->weight / $totalWeight)
                : 0.0;
        }

        // Ambil pendaftar yang sudah terverifikasi berkasnya (atau sudah dinilai lulus/tidak_lulus)
        $alternatives = Registration::whereIn('status', ['verified', 'lulus', 'tidak_lulus'])
            ->with(['sawScores', 'examResult'])
            ->orderBy('name')
            ->get();

        $matrix = [];
        $completeRegistrations = [];
        $incomplete = [];

        foreach ($alternatives as $registration) {
            if ($criteria->isEmpty()) {
                $incomplete[] = [
                    'registration' => $registration,
                    'missing_criteria' => ['Belum ada kriteria aktif'],
                    'values' => [],
                ];

                continue;
            }

            $rowValues = [];
            $missingCodes = [];
            $scoresByCriterion = $registration->sawScores->keyBy('saw_criterion_id');

            foreach ($criteria as $criterion) {
                $val = null;

                if ($criterion->source === 'cbt_score') {
                    if ($registration->examResult && $registration->examResult->score !== null) {
                        $val = (float) $registration->examResult->score;
                    }
                } else {
                    $scoreModel = $scoresByCriterion->get($criterion->id);
                    if ($scoreModel !== null) {
                        $val = (float) $scoreModel->value;
                    }
                }

                $rowValues[$criterion->id] = $val;

                if ($val === null) {
                    $missingCodes[] = $criterion->code.' ('.$criterion->name.')';
                }
            }

            if (empty($missingCodes)) {
                $matrix[$registration->id] = $rowValues;
                $completeRegistrations[$registration->id] = $registration;
            } else {
                $incomplete[] = [
                    'registration' => $registration,
                    'missing_criteria' => $missingCodes,
                    'values' => $rowValues,
                ];
            }
        }

        // Gunakan batas skala nilai kriteria (max_value / min_value) sebagai pembagi normalisasi
        $columnStats = [];
        foreach ($criteria as $criterion) {
            $colValues = [];
            foreach ($matrix as $regId => $row) {
                $colValues[] = (float) $row[$criterion->id];
            }

            $criterionMax = (float) $criterion->max_value;
            $criterionMin = (float) $criterion->min_value;

            $candidateMax = ! empty($colValues) ? max($colValues) : 0.0;
            $candidateMin = ! empty($colValues) ? min($colValues) : 0.0;

            $columnStats[$criterion->id] = [
                'max' => $criterionMax > 0 ? max($criterionMax, $candidateMax) : $candidateMax,
                'min' => $criterion->type === 'cost'
                    ? ($candidateMin > 0 ? $candidateMin : ($criterionMin > 0 ? $criterionMin : 0.0))
                    : $criterionMin,
            ];
        }

        // Tahap Normalisasi (R) dan Pembobotan (W * R)
        $normalized = [];
        $weighted = [];
        $ranking = [];

        foreach ($matrix as $regId => $row) {
            $preferenceScore = 0.0;

            foreach ($criteria as $criterion) {
                $x = (float) $row[$criterion->id];
                $max = $columnStats[$criterion->id]['max'];
                $min = $columnStats[$criterion->id]['min'];

                if ($criterion->type === 'benefit') {
                    $r = $max > 0 ? ($x / $max) : 0.0;
                } else {
                    // Cost: min / x
                    $r = $x > 0 ? ($min / $x) : ($min == 0.0 ? 1.0 : 0.0);
                }

                $w = $weights[$criterion->id] ?? 0.0;
                $wr = $w * $r;

                $normalized[$regId][$criterion->id] = $r;
                $weighted[$regId][$criterion->id] = $wr;
                $preferenceScore += $wr;
            }

            $roundedV = round($preferenceScore, 4);
            $isPassed = $roundedV >= round($threshold, 4);

            $ranking[] = [
                'rank' => 0,
                'registration' => $completeRegistrations[$regId],
                'raw_scores' => $row,
                'normalized_scores' => $normalized[$regId],
                'weighted_scores' => $weighted[$regId],
                'preference_score' => $roundedV,
                'preference_score_raw' => $preferenceScore,
                'is_passed' => $isPassed,
                'recommended_status' => $isPassed ? 'lulus' : 'tidak_lulus',
            ];
        }

        // Urutkan berdasarkan nilai preferensi V tertinggi
        usort($ranking, function (array $a, array $b) {
            $cmp = $b['preference_score_raw'] <=> $a['preference_score_raw'];
            if ($cmp !== 0) {
                return $cmp;
            }

            return strcmp((string) $a['registration']->registration_number, (string) $b['registration']->registration_number);
        });

        foreach ($ranking as $idx => &$item) {
            $item['rank'] = $idx + 1;
        }
        unset($item);

        return [
            'criteria' => $criteria,
            'total_weight' => $totalWeight,
            'weights' => $weights,
            'column_stats' => $columnStats,
            'matrix' => $matrix,
            'normalized' => $normalized,
            'weighted' => $weighted,
            'ranking' => $ranking,
            'incomplete' => $incomplete,
            'threshold' => $threshold,
            'default_fee' => $this->getDefaultRegistrationFee(),
            'summary' => [
                'total_eligible' => $alternatives->count(),
                'total_complete' => count($ranking),
                'total_incomplete' => count($incomplete),
                'passed_count' => collect($ranking)->where('is_passed', true)->count(),
                'failed_count' => collect($ranking)->where('is_passed', false)->count(),
            ],
        ];
    }

    /**
     * Ambil hasil perhitungan SAW untuk satu pendaftar spesifik.
     */
    public function getCandidateResult(int $registrationId): ?array
    {
        $calculation = $this->calculate();

        foreach ($calculation['ranking'] as $item) {
            if ($item['registration']->id === $registrationId) {
                return [
                    'is_complete' => true,
                    'rank' => $item['rank'],
                    'total_ranked' => count($calculation['ranking']),
                    'preference_score' => $item['preference_score'],
                    'threshold' => $calculation['threshold'],
                    'is_passed' => $item['is_passed'],
                    'recommended_status' => $item['recommended_status'],
                    'raw_scores' => $item['raw_scores'],
                    'normalized_scores' => $item['normalized_scores'],
                    'weighted_scores' => $item['weighted_scores'],
                    'criteria' => $calculation['criteria'],
                ];
            }
        }

        foreach ($calculation['incomplete'] as $inc) {
            if ($inc['registration']->id === $registrationId) {
                return [
                    'is_complete' => false,
                    'missing_criteria' => $inc['missing_criteria'],
                    'values' => $inc['values'],
                    'threshold' => $calculation['threshold'],
                    'criteria' => $calculation['criteria'],
                ];
            }
        }

        return null;
    }

    /**
     * Terapkan hasil rekomendasi SAW ke status kelulusan calon siswa secara atomik.
     */
    public function applyResults(?float $threshold = null, ?float $defaultFee = null): array
    {
        $threshold = $threshold ?? $this->getThreshold();
        $defaultFee = $defaultFee ?? $this->getDefaultRegistrationFee();
        $calculation = $this->calculate($threshold);

        $passedCount = 0;
        $failedCount = 0;

        DB::transaction(function () use ($calculation, $defaultFee, &$passedCount, &$failedCount) {
            foreach ($calculation['ranking'] as $row) {
                /** @var Registration $registration */
                $registration = $row['registration'];
                $newStatus = $row['recommended_status'];

                $updateData = [
                    'status' => $newStatus,
                ];

                if ($newStatus === 'lulus') {
                    // Tetapkan biaya daftar ulang default jika belum ditentukan
                    if ((! $registration->payment_amount || (float) $registration->payment_amount <= 0) && $defaultFee > 0) {
                        $updateData['payment_amount'] = $defaultFee;
                    }
                    if (! $registration->payment_status) {
                        $updateData['payment_status'] = 'unpaid';
                    }
                    $passedCount++;
                } else {
                    $failedCount++;
                }

                $registration->update($updateData);
            }
        });

        return [
            'passed' => $passedCount,
            'failed' => $failedCount,
            'total' => $passedCount + $failedCount,
            'threshold' => $threshold,
            'default_fee' => $defaultFee,
        ];
    }
}

