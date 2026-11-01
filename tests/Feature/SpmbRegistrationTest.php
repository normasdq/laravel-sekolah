<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SpmbRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_spmb_form(): void
    {
        $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);

        $response = $this->actingAs($user)->get(route('spmb'));

        $response->assertStatus(200);
        $response->assertSee('Identitas Calon Siswa');
        $response->assertSee('text-gray-900');
    }

    public function test_user_can_submit_spmb_registration_with_valid_data_and_large_files(): void
    {
        Storage::fake('local');

        $user = User::factory()->create([
            'name' => 'Norma Sidiq Adiluhur',
            'email' => 'user@example.com',
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Upload files around 3MB each (which previously failed on 2048KB limit)
        $kk = UploadedFile::fake()->create('kk_scan.pdf', 3000, 'application/pdf');
        $ijazah = UploadedFile::fake()->create('ijazah_foto.jpg', 4000, 'image/jpeg');
        $akte = UploadedFile::fake()->create('akte_scan.webp', 2500, 'image/webp');
        $pasFoto = UploadedFile::fake()->create('pas_foto.jpg', 1500, 'image/jpeg');

        $response = $this->actingAs($user)->post(route('pendaftar.spmb.store'), [
            'name' => 'Norma Sidiq Adiluhur',
            'email' => 'user@example.com',
            'nisn' => '1234567890',
            'school_origin' => 'SD Negeri 1 Tupan',
            'phone' => '081234567890',
            'gender' => 'L',
            'birth_date' => '2012-05-15',
            'birth_place' => 'Jakarta',
            'address' => 'Jl. Pendidikan No. 12',
            'parent_name' => 'Adiluhur',
            'parent_phone' => '081298765432',
            'kartu_keluarga' => $kk,
            'ijazah' => $ijazah,
            'akte_kelahiran' => $akte,
            'pas_foto' => $pasFoto,
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('registrations', [
            'user_id' => $user->id,
            'name' => 'Norma Sidiq Adiluhur',
            'nisn' => '1234567890',
            'status' => 'pending',
        ]);

        $registration = Registration::where('nisn', '1234567890')->first();
        $this->assertNotNull($registration);
        $this->assertNotNull($registration->kartu_keluarga);
        $this->assertNotNull($registration->ijazah);
        $this->assertNotNull($registration->akte_kelahiran);
        $this->assertNotNull($registration->pas_foto);

        // Pastikan halaman dashboard pendaftar dapat dimuat tanpa error route
        $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Identitas Calon Siswa');
        $dashboardResponse->assertSee('Norma Sidiq Adiluhur');
        $dashboardResponse->assertSee(route('pendaftar.download'));

        // Pastikan unduh bukti pendaftaran PDF berjalan normal
        $pdfResponse = $this->actingAs($user)->get(route('pendaftar.download'));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('content-type'));

        // Pastikan unduh kartu peserta ujian PDF berjalan normal dengan pas foto
        $kartuResponse = $this->actingAs($user)->get(route('pendaftar.kartu'));
        $kartuResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $kartuResponse->headers->get('content-type'));
    }

    public function test_user_can_upload_or_update_pas_foto_from_profile(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);
        $registration = Registration::create([
            'user_id' => $user->id,
            'name' => 'Siswa Test',
            'nisn' => '9988776655',
            'school_origin' => 'SD Harapan',
            'phone' => '081234567890',
            'status' => 'pending',
        ]);

        $newFoto = UploadedFile::fake()->create('pas_foto_baru.jpg', 600, 'image/jpeg');
        $response = $this->actingAs($user)->post(route('pendaftar.profile.update-photo'), [
            'pas_foto' => $newFoto,
        ]);

        $response->assertSessionHas('success');
        $registration->refresh();
        $this->assertNotNull($registration->pas_foto);
        Storage::disk('local')->assertExists($registration->pas_foto);
    }

    public function test_spmb_shows_indonesian_validation_errors_when_required_fields_missing(): void
    {
        $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);

        $response = $this->actingAs($user)->post(route('pendaftar.spmb.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'nisn',
            'school_origin',
            'phone',
            'gender',
            'birth_date',
            'birth_place',
            'address',
            'parent_name',
            'parent_phone',
            'pas_foto',
            'kartu_keluarga',
            'ijazah',
            'akte_kelahiran',
        ]);
    }
}
