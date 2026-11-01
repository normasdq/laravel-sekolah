<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_renders_successfully(): void
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
        $response->assertSee('SMP Tunas Harapan');
        $response->assertSee('Portal Administrator');
    }

    public function test_admin_dashboard_renders_for_admin_role(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'name' => 'Bapak Administrator',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('admin-hero-banner');
        $response->assertSee('Bapak Administrator');
        $response->assertSee('Total Artikel Berita');
        $response->assertSee('Pendaftar SPMB 2026');
        $response->assertSee('Dewan Guru &amp; Tenaga Didik', false);
    }

    public function test_admin_dashboard_renders_for_spmb_role(): void
    {
        $spmbUser = User::factory()->create([
            'role' => 'spmb',
            'name' => 'Petugas SPMB',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($spmbUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Petugas SPMB');
        $response->assertSee('Portal SPMB Online');
    }

    public function test_admin_dashboard_renders_for_penulis_role(): void
    {
        $penulis = User::factory()->create([
            'role' => 'penulis',
            'name' => 'Penulis Berita',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($penulis)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Penulis Berita');
        $response->assertSee('Manajemen Konten');
    }
}
