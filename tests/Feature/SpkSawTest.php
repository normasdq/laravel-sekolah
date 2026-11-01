<?php

namespace Tests\Feature;

use App\Models\ExamResult;
use App\Models\Registration;
use App\Models\SawCriterion;
use App\Models\SawScore;
use App\Models\User;
use Database\Seeders\SawCriteriaSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpkSawTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $spmb;
    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->spmb = User::factory()->create(['role' => 'spmb']);
        $this->student = User::factory()->create(['role' => 'user']);

        $this->seed(SawCriteriaSeeder::class);
    }

    private function createCandidate(string $name, string $regNumber, string $status = 'verified', ?float $cbtScore = 85): Registration
    {
        $user = User::factory()->create(['name' => $name, 'role' => 'user']);

        if ($cbtScore !== null) {
            ExamResult::create([
                'user_id' => $user->id,
                'score' => $cbtScore,
                'start_time' => now()->subMinutes(20),
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
            'school_origin' => 'SDN 2 Bandung',
            'birth_place' => 'Bandung',
            'birth_date' => '2012-01-15',
            'gender' => 'P',
            'address' => 'Jl. Asia Afrika No. 10',
            'phone' => '081299998888',
            'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            'parent_name' => 'Orang Tua ' . $name,
            'parent_phone' => '081277776666',
            'status' => $status,
        ]);
    }

    public function test_access_control_for_spk_routes(): void
    {
        // Admin & SPMB can access SPK pages
        $this->actingAs($this->admin)->get(route('admin.spk.index'))->assertOk();
        $this->actingAs($this->spmb)->get(route('admin.spk.index'))->assertOk();
        $this->actingAs($this->spmb)->get(route('admin.spk.criteria.index'))->assertOk();
        $this->actingAs($this->spmb)->get(route('admin.spk.scores'))->assertOk();

        // Regular user is forbidden/redirected
        $this->actingAs($this->student)->get(route('admin.spk.index'))->assertStatus(403);
    }

    public function test_criteria_crud_operations(): void
    {
        // Store new criterion
        $response = $this->actingAs($this->admin)->post(route('admin.spk.criteria.store'), [
            'code' => 'c5',
            'name' => 'Jarak Tempuh',
            'type' => 'cost',
            'weight' => 10,
            'source' => 'manual',
            'min_value' => 1,
            'max_value' => 50,
            'sort_order' => 5,
            'is_active' => 1,
            'description' => 'Jarak dari rumah ke sekolah',
        ]);

        $response->assertRedirect(route('admin.spk.criteria.index'));
        $this->assertDatabaseHas('saw_criteria', [
            'code' => 'C5',
            'name' => 'Jarak Tempuh',
            'type' => 'cost',
        ]);

        $criterion = SawCriterion::where('code', 'C5')->firstOrFail();

        // Update criterion
        $this->actingAs($this->spmb)->put(route('admin.spk.criteria.update', $criterion), [
            'code' => 'C5',
            'name' => 'Jarak Rumah Update',
            'type' => 'cost',
            'weight' => 15,
            'source' => 'manual',
            'min_value' => 1,
            'max_value' => 40,
            'sort_order' => 5,
            'is_active' => 1,
        ])->assertRedirect(route('admin.spk.criteria.index'));

        $this->assertDatabaseHas('saw_criteria', [
            'id' => $criterion->id,
            'name' => 'Jarak Rumah Update',
        ]);

        // Delete criterion
        $this->actingAs($this->admin)->delete(route('admin.spk.criteria.destroy', $criterion))
            ->assertRedirect(route('admin.spk.criteria.index'));

        $this->assertDatabaseMissing('saw_criteria', ['id' => $criterion->id]);
    }

    public function test_save_scores_and_scale_validation(): void
    {
        $candidate = $this->createCandidate('Rina', 'REG-2026-0100');
        $c2 = SawCriterion::where('code', 'C2')->firstOrFail();
        $c3 = SawCriterion::where('code', 'C3')->firstOrFail();
        $c4 = SawCriterion::where('code', 'C4')->firstOrFail(); // scale 1-5

        // Invalid score out of range (C4 = 10 > max_value 5)
        $this->actingAs($this->spmb)->post(route('admin.spk.scores.save'), [
            'scores' => [
                $candidate->id => [
                    $c2->id => 85,
                    $c3->id => 90,
                    $c4->id => 10,
                ],
            ],
        ])->assertSessionHasErrors();

        // Valid scores
        $this->actingAs($this->spmb)->post(route('admin.spk.scores.save'), [
            'scores' => [
                $candidate->id => [
                    $c2->id => 85,
                    $c3->id => 90,
                    $c4->id => 4,
                ],
            ],
        ])->assertRedirect(route('admin.spk.scores'));

        $this->assertDatabaseHas('saw_scores', [
            'registration_id' => $candidate->id,
            'saw_criterion_id' => $c4->id,
            'value' => 4,
        ]);
    }

    public function test_apply_results_updates_only_complete_verified_registrations_and_requires_admin(): void
    {
        $c2 = SawCriterion::where('code', 'C2')->firstOrFail();
        $c3 = SawCriterion::where('code', 'C3')->firstOrFail();
        $c4 = SawCriterion::where('code', 'C4')->firstOrFail();

        // High score candidate (should become lulus)
        $high = $this->createCandidate('Siswa Lulus', 'REG-2026-0201', 'verified', 90);
        SawScore::create(['registration_id' => $high->id, 'saw_criterion_id' => $c2->id, 'value' => 90]);
        SawScore::create(['registration_id' => $high->id, 'saw_criterion_id' => $c3->id, 'value' => 90]);
        SawScore::create(['registration_id' => $high->id, 'saw_criterion_id' => $c4->id, 'value' => 5]);

        // Low score candidate (should become tidak_lulus against 0.70 threshold)
        $low = $this->createCandidate('Siswa Tidak Lulus', 'REG-2026-0202', 'verified', 40);
        SawScore::create(['registration_id' => $low->id, 'saw_criterion_id' => $c2->id, 'value' => 45]);
        SawScore::create(['registration_id' => $low->id, 'saw_criterion_id' => $c3->id, 'value' => 50]);
        SawScore::create(['registration_id' => $low->id, 'saw_criterion_id' => $c4->id, 'value' => 1]);

        // Incomplete verified candidate (should stay verified)
        $incomplete = $this->createCandidate('Siswa Belum Lengkap', 'REG-2026-0203', 'verified', 80);

        // Pending candidate (should stay pending)
        $pending = $this->createCandidate('Siswa Pending', 'REG-2026-0204', 'pending', 95);

        // SPMB staff cannot apply graduation decision (Admin only)
        $this->actingAs($this->spmb)->post(route('admin.spk.apply'))->assertStatus(403);

        // Admin applies graduation decision with default re-registration fee
        $this->actingAs($this->admin)->post(route('admin.spk.apply'), [
            'threshold' => 0.70,
            'default_fee' => 175000,
        ])->assertRedirect(route('admin.spk.index'));

        $this->assertSame('lulus', $high->fresh()->status);
        $this->assertEquals(175000.0, (float) $high->fresh()->payment_amount);
        $this->assertSame('unpaid', $high->fresh()->payment_status);

        $this->assertSame('tidak_lulus', $low->fresh()->status);
        $this->assertSame('verified', $incomplete->fresh()->status);
        $this->assertSame('pending', $pending->fresh()->status);
    }

    public function test_cek_status_displays_saw_result_for_graduated_student(): void
    {
        $c2 = SawCriterion::where('code', 'C2')->firstOrFail();
        $c3 = SawCriterion::where('code', 'C3')->firstOrFail();
        $c4 = SawCriterion::where('code', 'C4')->firstOrFail();

        $candidate = $this->createCandidate('Aulia Lulus', 'REG-2026-0301', 'lulus', 92);
        $candidate->update(['payment_amount' => 150000, 'payment_status' => 'unpaid']);

        SawScore::create(['registration_id' => $candidate->id, 'saw_criterion_id' => $c2->id, 'value' => 88]);
        SawScore::create(['registration_id' => $candidate->id, 'saw_criterion_id' => $c3->id, 'value' => 90]);
        SawScore::create(['registration_id' => $candidate->id, 'saw_criterion_id' => $c4->id, 'value' => 4]);

        $studentUser = $candidate->user;

        // Post NISN check
        $this->actingAs($studentUser)
            ->post(route('pendaftar.cek-status.check'), ['nisn' => $candidate->nisn])
            ->assertRedirect(route('pendaftar.cek-status'));

        // Follow redirect to cek-status view
        $response = $this->actingAs($studentUser)
            ->withSession(['registration' => $candidate])
            ->get(route('pendaftar.cek-status'));

        $response->assertOk();
        $response->assertSee('SELAMAT! ANDA DINYATAKAN LULUS SELEKSI');
        $response->assertSee('Hasil Evaluasi SPK Metode SAW');
        $response->assertSee('Bayar Daftar Ulang');
    }

    public function test_cbt_submit_normalizes_score_to_100_scale(): void
    {
        $candidate = $this->createCandidate('Peserta CBT', 'REG-2026-0302', 'verified', null);
        $studentUser = $candidate->user;

        $q1 = \App\Models\Question::create([
            'question_text' => 'Berapa 2 + 2?',
            'option_a' => '3',
            'option_b' => '4',
            'option_c' => '5',
            'option_d' => '6',
            'correct_answer' => 'B',
            'points' => 5,
        ]);

        $q2 = \App\Models\Question::create([
            'question_text' => 'Ibu kota Indonesia?',
            'option_a' => 'Jakarta',
            'option_b' => 'Bandung',
            'option_c' => 'Surabaya',
            'option_d' => 'Medan',
            'correct_answer' => 'A',
            'points' => 15,
        ]);

        \App\Models\ExamResult::create([
            'user_id' => $studentUser->id,
            'start_time' => now()->subMinutes(10),
            'question_ids' => [$q1->id, $q2->id],
        ]);

        // Answer q2 right (15 pts) and q1 wrong (0 pts) out of 20 max points -> 75.00
        $this->actingAs($studentUser)->post(route('pendaftar.ujian.submit'), [
            'answers' => [
                $q1->id => 'A',
                $q2->id => 'A',
            ],
        ])->assertRedirect(route('dashboard'));

        $examResult = \App\Models\ExamResult::where('user_id', $studentUser->id)->firstOrFail();
        $this->assertEqualsWithDelta(75.0, (float) $examResult->score, 0.01);
    }

    public function test_export_pdf_returns_pdf_stream(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.spk.export-pdf'));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }
}
