<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedesignViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_successfully(): void
    {
        $routes = ['home', 'about', 'contact', 'berita', 'login', 'register'];

        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertStatus(200);
        }
    }

    public function test_about_page_renders_teachers_and_staff(): void
    {
        \App\Models\Teacher::create([
            'name' => 'Nurunnisa Al Musyarafah, S.Pd',
            'position' => 'Guru Bahasa Indonesia',
            'type' => 'guru',
            'order' => 14,
        ]);
        \App\Models\Teacher::create([
            'name' => 'Guntur Zaelani',
            'position' => 'Tata Usaha',
            'type' => 'staff',
            'order' => 15,
        ]);

        $response = $this->get(route('about'));
        $response->assertStatus(200);
        $response->assertSee('Nurunnisa Al Musyarafah, S.Pd');
        $response->assertSee('Staf Administrasi & Pendukung');
        $response->assertSee('Guntur Zaelani');
    }

    public function test_authenticated_dashboard_renders_successfully(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
    }

    public function test_cek_status_renders_successfully(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'email_verified_at' => now(),
        ]);
        \App\Models\Registration::create([
            'user_id' => $user->id,
            'name' => 'Calon Siswa',
            'nisn' => '1234567890',
            'school_origin' => 'SD Harapan',
            'phone' => '081234567890',
            'status' => 'pending',
        ]);
        $this->actingAs($user);

        $response = $this->get(route('pendaftar.cek-status'));
        $response->assertStatus(200);
    }
}
