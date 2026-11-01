<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSimulatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_simulator_payment_flow_works_end_to_end(): void
    {
        config(['services.midtrans.simulator' => true]);

        $user = User::factory()->create([
            'name' => 'Aulia Rossa Nasution',
            'email' => 'aulia@example.com',
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $registration = Registration::create([
            'user_id' => $user->id,
            'name' => 'Aulia Rossa Nasution',
            'nisn' => '1923046316',
            'school_origin' => 'SMP Contoh',
            'phone' => '081234567890',
            'status' => 'lulus',
            'payment_status' => 'unpaid',
            'payment_amount' => 150000,
        ]);

        // 1. Pendaftar mengakses halaman payment
        $response = $this->actingAs($user)->get(route('payment.index'));
        $response->assertStatus(200);
        $response->assertSee('Mode Simulasi Pembayaran');
        $response->assertSee('Rp 150.000');

        // 2. Klik bayar sekarang (POST payment.create) menghasilkan demo token
        $createResponse = $this->actingAs($user)->postJson(route('payment.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertJson([
            'is_demo' => true,
        ]);
        $this->assertNotNull($createResponse->json('snap_token'));

        // 3. Konfirmasi simulasi pembayaran sukses (POST payment.demo.pay)
        $demoPayResponse = $this->actingAs($user)->postJson(route('payment.demo.pay'), [
            'method' => 'QRIS (Gopay/ShopeePay)',
        ]);
        $demoPayResponse->assertStatus(200);
        $demoPayResponse->assertJson(['success' => true]);

        // Verifikasi database
        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'payment_status' => 'paid',
        ]);

        $this->assertDatabaseHas('payments', [
            'registration_id' => $registration->id,
            'status' => 'success',
            'method' => 'QRIS (Gopay/ShopeePay)',
        ]);

        // 4. Unduh Invoice PDF
        $invoiceResponse = $this->actingAs($user)->get(route('payment.invoice'));
        $invoiceResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $invoiceResponse->headers->get('content-type'));
    }
}
