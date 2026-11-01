<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Payment;
use App\Models\Post;
use App\Models\Registration;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\QueuedVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhaseTwoSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_status_does_not_reveal_other_users_nisn(): void
    {
        $userA = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);
        $userB = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);

        Registration::create([
            'user_id' => $userA->id,
            'name' => 'User A',
            'nisn' => '1111111111',
            'school_origin' => 'SD 1',
            'phone' => '0811111111',
            'status' => 'pending',
        ]);

        $regB = Registration::create([
            'user_id' => $userB->id,
            'name' => 'User B',
            'nisn' => '2222222222',
            'school_origin' => 'SD 2',
            'phone' => '0822222222',
            'status' => 'verified',
        ]);

        // User A mencoba mencari NISN milik User B
        $response = $this->actingAs($userA)->post('/spmb/cek-status', [
            'nisn' => '2222222222',
        ]);

        $response->assertSessionHas('not_found', true);
        $response->assertSessionMissing('registration');
    }

    public function test_comments_api_hides_email_and_filters_unapproved_replies(): void
    {
        $category = Category::create([
            'name' => 'Umum',
            'slug' => 'umum',
        ]);

        $post = Post::create([
            'title' => 'Berita Sekolah',
            'slug' => 'berita-sekolah',
            'content' => 'Konten berita',
            'category_id' => $category->id,
            'is_published' => true,
        ]);

        $rootComment = Comment::create([
            'post_id' => $post->id,
            'name' => 'Budi',
            'email' => 'budi.rahasia@example.com',
            'body' => 'Komentar pertama',
            'is_approved' => true,
        ]);

        // Balasan disetujui
        Comment::create([
            'post_id' => $post->id,
            'parent_id' => $rootComment->id,
            'name' => 'Siti',
            'email' => 'siti.rahasia@example.com',
            'body' => 'Balasan disetujui',
            'is_approved' => true,
        ]);

        // Balasan belum disetujui
        Comment::create([
            'post_id' => $post->id,
            'parent_id' => $rootComment->id,
            'name' => 'Spammer',
            'email' => 'spam@example.com',
            'body' => 'Balasan pending',
            'is_approved' => false,
        ]);

        $response = $this->getJson("/berita/{$post->slug}/comments");

        $response->assertStatus(200);

        // Pastikan email disembunyikan dari JSON
        $response->assertDontSee('budi.rahasia@example.com');
        $response->assertDontSee('siti.rahasia@example.com');

        // Balasan yang belum disetujui tidak boleh muncul
        $response->assertDontSee('Balasan pending');
        $response->assertSee('Balasan disetujui');
    }

    public function test_post_creation_sanitizes_xss_scripts(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $category = Category::create([
            'name' => 'Pengumuman',
            'slug' => 'pengumuman',
        ]);

        $maliciousContent = '<p>Halo</p><script>alert("XSS")</script><img src="x" onerror="alert(1)">';

        $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'Judul Artikel Aman',
            'category_id' => $category->id,
            'content' => $maliciousContent,
            'is_published' => 1,
        ]);

        $post = Post::where('title', 'Judul Artikel Aman')->first();
        $this->assertNotNull($post);

        // Tag <script> dan event onerror harus dibersihkan
        $this->assertStringNotContainsString('<script>', $post->content);
        $this->assertStringNotContainsString('onerror', $post->content);
        $this->assertStringContainsString('<p>Halo</p>', $post->content);
    }

    public function test_spmb_store_is_blocked_when_spmb_is_disabled(): void
    {
        Storage::fake('local');

        SiteSetting::updateOrCreate(
            ['key' => 'spmb_disabled'],
            ['value' => '1']
        );

        $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);

        $response = $this->actingAs($user)->post(route('pendaftar.spmb.store'), [
            'name' => 'Test Calon',
            'nisn' => '1234567890',
            'school_origin' => 'SD Harapan',
            'phone' => '08123456789',
            'gender' => 'L',
            'birth_date' => '2012-01-01',
            'birth_place' => 'Jakarta',
            'address' => 'Jl. Merdeka',
            'parent_name' => 'Orang Tua',
            'parent_phone' => '08123456789',
            'kartu_keluarga' => UploadedFile::fake()->create('kk.pdf', 100),
            'ijazah' => UploadedFile::fake()->create('ijazah.pdf', 100),
            'akte_kelahiran' => UploadedFile::fake()->create('akte.pdf', 100),
            'pas_foto' => UploadedFile::fake()->create('foto.jpg', 100),
        ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Pendaftaran SPMB saat ini sedang ditutup.');

        $this->assertEquals(0, Registration::count());
    }

    public function test_payment_deletion_is_forbidden_for_spmb_role(): void
    {
        $spmbStaff = User::factory()->create(['role' => 'spmb', 'email_verified_at' => now()]);
        $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);

        $reg = Registration::create([
            'user_id' => $user->id,
            'name' => 'Siswa Pembayar',
            'nisn' => '9999999999',
            'school_origin' => 'SD Maju',
            'phone' => '0899999999',
            'status' => 'lulus',
        ]);

        $payment = Payment::create([
            'registration_id' => $reg->id,
            'user_id' => $user->id,
            'amount' => 1000000,
            'status' => 'success',
            'transaction_id' => 'REG-999',
        ]);

        // Staff SPMB mencoba menghapus transaksi pembayaran
        $response = $this->actingAs($spmbStaff)->delete(route('admin.payments.destroy', $payment->id));

        $response->assertStatus(403);
        $this->assertDatabaseHas('payments', ['id' => $payment->id]);
    }

    public function test_email_verification_notification_is_queued(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Calon Siswa Baru',
            'email' => 'calon@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'calon@example.com')->first();
        $this->assertNotNull($user);

        Notification::assertSentTo($user, QueuedVerifyEmail::class);
    }
}
