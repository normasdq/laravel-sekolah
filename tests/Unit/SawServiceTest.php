<?php

namespace Tests\Unit;

use App\Models\ExamResult;
use App\Models\Registration;
use App\Models\SawCriterion;
use App\Models\SawScore;
use App\Models\User;
use App\Services\SawService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SawServiceTest extends TestCase
{
    use RefreshDatabase;

    private SawService $sawService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sawService = new SawService();
    }

    private function seedDefaultCriteria(): array
    {
        $c1 = SawCriterion::create([
            'code' => 'C1',
            'name' => 'Nilai Ujian CBT',
            'type' => 'benefit',
            'weight' => 35.00,
            'source' => 'cbt_score',
            'min_value' => 0,
            'max_value' => 100,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $c2 = SawCriterion::create([
            'code' => 'C2',
            'name' => 'Rata-rata Nilai Rapor',
            'type' => 'benefit',
            'weight' => 30.00,
            'source' => 'manual',
            'min_value' => 0,
            'max_value' => 100,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $c3 = SawCriterion::create([
            'code' => 'C3',
            'name' => 'Nilai Wawancara & BTQ',
            'type' => 'benefit',
            'weight' => 20.00,
            'source' => 'manual',
            'min_value' => 0,
            'max_value' => 100,
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $c4 = SawCriterion::create([
            'code' => 'C4',
            'name' => 'Prestasi Akademik / Non-Akademik',
            'type' => 'benefit',
            'weight' => 15.00,
            'source' => 'manual',
            'min_value' => 1,
            'max_value' => 5,
            'sort_order' => 4,
            'is_active' => true,
        ]);

        return [$c1, $c2, $c3, $c4];
    }

    private function createVerifiedRegistration(string $name, string $regNumber, ?float $cbtScore = null): Registration
    {
        $user = User::factory()->create([
            'name' => $name,
            'role' => 'user',
        ]);

        if ($cbtScore !== null) {
            ExamResult::create([
                'user_id' => $user->id,
                'score' => $cbtScore,
                'start_time' => now()->subMinutes(30),
                'end_time' => now(),
                'answers' => [],
                'question_ids' => [],
            ]);
        }

        return Registration::create([
            'user_id' => $user->id,
            'registration_number' => $regNumber,
            'name' => $name,
            'nisn' => (string) random_int(1000000000, 9999999999),
            'school_origin' => 'SDN 1 Jakarta',
            'birth_place' => 'Jakarta',
            'birth_date' => '2012-05-10',
            'gender' => 'L',
            'address' => 'Jl. Merdeka No. 1',
            'phone' => '081234567890',
            'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            'parent_name' => 'Orang Tua ' . $name,
            'parent_phone' => '081234567891',
            'status' => 'verified',
        ]);
    }

    public function test_saw_calculation_matches_manual_formula(): void
    {
        [$c1, $c2, $c3, $c4] = $this->seedDefaultCriteria();

        // A1: Ahmad (C1=85, C2=88, C3=90, C4=3)
        $a1 = $this->createVerifiedRegistration('Ahmad', 'REG-2026-0001', 85);
        SawScore::create(['registration_id' => $a1->id, 'saw_criterion_id' => $c2->id, 'value' => 88]);
        SawScore::create(['registration_id' => $a1->id, 'saw_criterion_id' => $c3->id, 'value' => 90]);
        SawScore::create(['registration_id' => $a1->id, 'saw_criterion_id' => $c4->id, 'value' => 3]);

        // A2: Budi (C1=90, C2=82, C3=85, C4=4)
        $a2 = $this->createVerifiedRegistration('Budi', 'REG-2026-0002', 90);
        SawScore::create(['registration_id' => $a2->id, 'saw_criterion_id' => $c2->id, 'value' => 82]);
        SawScore::create(['registration_id' => $a2->id, 'saw_criterion_id' => $c3->id, 'value' => 85]);
        SawScore::create(['registration_id' => $a2->id, 'saw_criterion_id' => $c4->id, 'value' => 4]);

        // A3: Citra (C1=75, C2=90, C3=80, C4=2)
        $a3 = $this->createVerifiedRegistration('Citra', 'REG-2026-0003', 75);
        SawScore::create(['registration_id' => $a3->id, 'saw_criterion_id' => $c2->id, 'value' => 90]);
        SawScore::create(['registration_id' => $a3->id, 'saw_criterion_id' => $c3->id, 'value' => 80]);
        SawScore::create(['registration_id' => $a3->id, 'saw_criterion_id' => $c4->id, 'value' => 2]);

        $result = $this->sawService->calculate(0.70);

        $this->assertCount(3, $result['ranking']);
        $this->assertCount(0, $result['incomplete']);

        // Max ideal per column: C1=100, C2=100, C3=100, C4=5
        // Rank 1: Budi (A2) -> V2 = 0.35*(90/100) + 0.30*(82/100) + 0.20*(85/100) + 0.15*(4/5) = 0.315 + 0.246 + 0.170 + 0.120 = 0.8510
        $this->assertSame($a2->id, $result['ranking'][0]['registration']->id);
        $this->assertSame(1, $result['ranking'][0]['rank']);
        $this->assertEqualsWithDelta(0.8510, $result['ranking'][0]['preference_score'], 0.0005);
        $this->assertSame('lulus', $result['ranking'][0]['recommended_status']);

        // Rank 2: Ahmad (A1) -> V1 = 0.35*(85/100) + 0.30*(88/100) + 0.20*(90/100) + 0.15*(3/5) = 0.2975 + 0.264 + 0.180 + 0.090 = 0.8315
        $this->assertSame($a1->id, $result['ranking'][1]['registration']->id);
        $this->assertSame(2, $result['ranking'][1]['rank']);
        $this->assertEqualsWithDelta(0.8315, $result['ranking'][1]['preference_score'], 0.0005);
        $this->assertSame('lulus', $result['ranking'][1]['recommended_status']);

        // Rank 3: Citra (A3) -> V3 = 0.35*(75/100) + 0.30*(90/100) + 0.20*(80/100) + 0.15*(2/5) = 0.2625 + 0.270 + 0.160 + 0.060 = 0.7525
        $this->assertSame($a3->id, $result['ranking'][2]['registration']->id);
        $this->assertSame(3, $result['ranking'][2]['rank']);
        $this->assertEqualsWithDelta(0.7525, $result['ranking'][2]['preference_score'], 0.0005);
        $this->assertSame('lulus', $result['ranking'][2]['recommended_status']);
    }

    public function test_single_candidate_score_uses_criterion_max_scale(): void
    {
        [$c1, $c2, $c3, $c4] = $this->seedDefaultCriteria();

        // Single candidate: C1=60, C2=85, C3=90, C4=1 (Opsi 1)
        $candidate = $this->createVerifiedRegistration('Siswa Tunggal', 'REG-2026-0099', 60);
        SawScore::create(['registration_id' => $candidate->id, 'saw_criterion_id' => $c2->id, 'value' => 85]);
        SawScore::create(['registration_id' => $candidate->id, 'saw_criterion_id' => $c3->id, 'value' => 90]);
        SawScore::create(['registration_id' => $candidate->id, 'saw_criterion_id' => $c4->id, 'value' => 1]);

        $result = $this->sawService->calculate(70);

        $this->assertCount(1, $result['ranking']);
        // V = 0.35*(60/100) + 0.30*(85/100) + 0.20*(90/100) + 0.15*(1/5) = 0.21 + 0.255 + 0.18 + 0.03 = 0.675 (68)
        $this->assertEqualsWithDelta(0.675, $result['ranking'][0]['preference_score'], 0.0001);
        $this->assertSame(68, (int) round($result['ranking'][0]['preference_score'] * 100));
        $this->assertFalse($result['ranking'][0]['is_passed']);
        $this->assertSame('tidak_lulus', $result['ranking'][0]['recommended_status']);
    }

    public function test_cost_attribute_normalization_and_weight_auto_normalization(): void
    {
        $c1 = SawCriterion::create([
            'code' => 'C1',
            'name' => 'Nilai Tes',
            'type' => 'benefit',
            'weight' => 60, // total weight = 60 + 60 = 120 (0.5 each)
            'source' => 'manual',
            'min_value' => 0,
            'max_value' => 100,
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $c2 = SawCriterion::create([
            'code' => 'C2',
            'name' => 'Jarak Rumah (km)',
            'type' => 'cost',
            'weight' => 60,
            'source' => 'manual',
            'min_value' => 1,
            'max_value' => 50,
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $a1 = $this->createVerifiedRegistration('Siswa Dekat', 'REG-2026-0010');
        SawScore::create(['registration_id' => $a1->id, 'saw_criterion_id' => $c1->id, 'value' => 80]);
        SawScore::create(['registration_id' => $a1->id, 'saw_criterion_id' => $c2->id, 'value' => 2]); // min cost = 2 -> r = 2/2 = 1.0

        $a2 = $this->createVerifiedRegistration('Siswa Jauh', 'REG-2026-0011');
        SawScore::create(['registration_id' => $a2->id, 'saw_criterion_id' => $c1->id, 'value' => 100]); // max benefit = 100 -> r = 1.0
        SawScore::create(['registration_id' => $a2->id, 'saw_criterion_id' => $c2->id, 'value' => 10]); // cost = 10 -> r = 2/10 = 0.2

        $result = $this->sawService->calculate(0.75);

        $this->assertEqualsWithDelta(0.5, $result['weights'][$c1->id], 0.0001);
        $this->assertEqualsWithDelta(0.5, $result['weights'][$c2->id], 0.0001);

        // A1: 0.5 * (80/100) + 0.5 * (2/2) = 0.4 + 0.5 = 0.90 (lulus)
        // A2: 0.5 * (100/100) + 0.5 * (2/10) = 0.5 + 0.1 = 0.60 (tidak_lulus)
        $this->assertSame($a1->id, $result['ranking'][0]['registration']->id);
        $this->assertEqualsWithDelta(0.90, $result['ranking'][0]['preference_score'], 0.0001);
        $this->assertSame('lulus', $result['ranking'][0]['recommended_status']);

        $this->assertSame($a2->id, $result['ranking'][1]['registration']->id);
        $this->assertEqualsWithDelta(0.60, $result['ranking'][1]['preference_score'], 0.0001);
        $this->assertSame('tidak_lulus', $result['ranking'][1]['recommended_status']);
    }

    public function test_incomplete_scores_are_separated_from_ranking(): void
    {
        [$c1, $c2] = array_slice($this->seedDefaultCriteria(), 0, 2);
        SawCriterion::whereNotIn('id', [$c1->id, $c2->id])->update(['is_active' => false]);

        // Complete candidate
        $complete = $this->createVerifiedRegistration('Lengkap', 'REG-2026-0020', 80);
        SawScore::create(['registration_id' => $complete->id, 'saw_criterion_id' => $c2->id, 'value' => 85]);

        // Incomplete candidate (missing CBT score)
        $incomplete = $this->createVerifiedRegistration('Belum CBT', 'REG-2026-0021', null);
        SawScore::create(['registration_id' => $incomplete->id, 'saw_criterion_id' => $c2->id, 'value' => 90]);

        $result = $this->sawService->calculate(0.70);

        $this->assertCount(1, $result['ranking']);
        $this->assertSame($complete->id, $result['ranking'][0]['registration']->id);

        $this->assertCount(1, $result['incomplete']);
        $this->assertSame($incomplete->id, $result['incomplete'][0]['registration']->id);
        $this->assertContains('C1 (Nilai Ujian CBT)', $result['incomplete'][0]['missing_criteria']);
    }
}
