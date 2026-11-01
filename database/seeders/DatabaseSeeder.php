<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('PERINGATAN: DatabaseSeeder tidak dijalankan pada mode production untuk melindungi akun default.');

            return;
        }

        User::create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Penulis',
            'email' => 'penulis@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'penulis',
        ]);

        User::create([
            'name' => 'Panitia SPMB',
            'email' => 'spmb@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'spmb',
        ]);

        $this->call([
            ProgramSeeder::class,
            TeacherSeeder::class,
            PostSeeder::class,
            GallerySeeder::class,
            SiteSettingSeeder::class,
            QuestionSeeder::class,
            SawCriteriaSeeder::class,
        ]);
    }
}
