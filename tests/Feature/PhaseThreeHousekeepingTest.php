<?php

namespace Tests\Feature;

use App\Models\ExamResult;
use App\Models\Question;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseThreeHousekeepingTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_properly_applied(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Header deprecated X-XSS-Protection harus sudah dihilangkan
        $this->assertFalse($response->headers->has('X-XSS-Protection'));

        // Akses via HTTPS harus menyertakan HSTS
        $secureResponse = $this->get('https://localhost/');
        $secureResponse->assertHeader('Strict-Transport-Security');
    }

    public function test_registration_model_uses_soft_deletes(): void
    {
        $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);

        $reg = Registration::create([
            'user_id' => $user->id,
            'name' => 'Siswa Soft Delete',
            'nisn' => '1234567899',
            'school_origin' => 'SD Negeri 1',
            'phone' => '08123456789',
            'status' => 'pending',
        ]);

        $this->assertEquals(1, Registration::count());

        $reg->delete();

        // Tersembunyi dari query normal
        $this->assertEquals(0, Registration::count());

        // Masih ada di database dengan deleted_at
        $this->assertEquals(1, Registration::withTrashed()->count());
        $this->assertNotNull($reg->fresh()->deleted_at);
    }

    public function test_setting_msvalidate_01_can_be_saved(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'school_name' => 'SMP Tunas Harapan',
            'school_short_name' => 'SMPTH',
            'msvalidate_01' => 'BING_VERIFY_TOKEN_999',
            'google_site_verification' => 'GOOGLE_VERIFY_TOKEN_888',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('site_settings', [
            'key' => 'msvalidate_01',
            'value' => 'BING_VERIFY_TOKEN_999',
        ]);
    }

    public function test_examp_results_page_executes_successfully(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $student = User::factory()->create(['name' => 'Peserta CBT', 'role' => 'user', 'email_verified_at' => now()]);

        $reg = Registration::create([
            'user_id' => $student->id,
            'registration_number' => 'REG-2026-0001',
            'name' => 'Peserta CBT',
            'nisn' => '3333333333',
            'school_origin' => 'SD 3',
            'phone' => '0833333333',
            'status' => 'verified',
        ]);

        $question = Question::create([
            'question_text' => 'Soal 1',
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
            'correct_answer' => 'A',
            'points' => 10,
        ]);

        ExamResult::create([
            'user_id' => $student->id,
            'score' => 100,
            'start_time' => now()->subMinutes(30),
            'end_time' => now(),
            'question_ids' => [$question->id],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.examp.results'));

        $response->assertStatus(200);
        $response->assertSee('REG-2026-0001');
        $response->assertSee('Peserta CBT');
        $response->assertSee('100');
    }
}
