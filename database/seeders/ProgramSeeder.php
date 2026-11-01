<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'title' => 'Kurikulum',
                'description' => 'Kurikulum Merdeka & K-13',
                'icon' => 'fa-solid fa-book-open-reader',
                'order' => 1,
            ],
            [
                'title' => 'Fasilitas',
                'description' => 'Fasilitas Lengkap dan Menunjang Pembelajaran',
                'icon' => 'fa-solid fa-building-flag',
                'order' => 2,
            ],
            [
                'title' => 'Ekstrakurikuler',
                'description' => 'Untuk Menunjang Kegiatan Siswa',
                'icon' => 'fa-solid fa-football',
                'order' => 3,
            ],
            [
                'title' => 'Guru Terverifikasi',
                'description' => 'Para Guru Memiliki Sertifikasi dibidangnya',
                'icon' => 'fa-solid fa-star',
                'order' => 4,
            ],
        ];

        foreach ($programs as $program) {
            DB::table('programs')->insert(array_merge($program, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
