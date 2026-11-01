<?php

namespace Tests\Feature;

use App\Models\ExamResult;
use App\Models\Payment;
use App\Models\Question;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseOneSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_midtrans_callback_rejects_invalid_signature(): void
    {
        $response = $this->postJson('/payment/callback', [
            'order_id' => 'REG-2026-0001-12345',
            'status_code' => '200',
            'gross_amount' => '1500000',
            'signature_key' => 'invalid-signature-hash',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(403);
    }

    public function test_payment_success_page_does_not_falsely_mark_paid(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'name' => 'Calon Siswa',
            'nisn' => '1234567890',
            'school_origin' => 'SD Negeri 1',
            'phone' => '08123456789',
            'status' => 'lulus',
            'payment_amount' => 1500000,
            'payment_status' => 'unpaid',
        ]);

        $payment = Payment::create([
            'registration_id' => $registration->id,
            'user_id' => $user->id,
            'amount' => 1500000,
            'status' => 'pending',
            'transaction_id' => 'REG-12345',
            'snap_token' => 'dummy-token',
        ]);

        $response = $this->actingAs($user)->get('/spmb/payment/success');

        $response->assertRedirect(route('dashboard'));

        // Pastikan status TIDAK menjadi paid karena tidak ada konfirmasi riil dari Midtrans
        $registration->refresh();
        $this->assertNotEquals('paid', $registration->payment_status);
    }

    public function test_unauthenticated_user_cannot_access_private_documents(): void
    {
        $response = $this->get('/spmb/dokumen/kartu_keluarga');
        $response->assertRedirect('/login');
    }

    public function test_cbt_locks_questions_and_retains_them_on_refresh(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        Registration::create([
            'user_id' => $user->id,
            'name' => 'Peserta Ujian',
            'nisn' => '9876543210',
            'school_origin' => 'SD Harapan',
            'phone' => '08123456789',
            'status' => 'verified',
        ]);

        // Buat beberapa soal dummy
        for ($i = 1; $i <= 5; $i++) {
            Question::create([
                'question_text' => "Soal nomor {$i}?",
                'option_a' => 'Pilihan A',
                'option_b' => 'Pilihan B',
                'option_c' => 'Pilihan C',
                'option_d' => 'Pilihan D',
                'correct_answer' => 'A',
                'points' => 20,
            ]);
        }

        // Start ujian pertama kali
        $response1 = $this->actingAs($user)->get('/spmb/ujian/start');
        $response1->assertStatus(200);

        $result1 = ExamResult::where('user_id', $user->id)->first();
        $this->assertNotNull($result1);
        $this->assertNotEmpty($result1->question_ids);

        $originalQuestionIds = $result1->question_ids;

        // Refresh / akses kembali start ujian
        $response2 = $this->actingAs($user)->get('/spmb/ujian/start');
        $response2->assertStatus(200);

        $result2 = ExamResult::where('user_id', $user->id)->first();
        // Soal harus tetap terkunci persis sama dan tidak berganti
        $this->assertEquals($originalQuestionIds, $result2->question_ids);
    }
}
