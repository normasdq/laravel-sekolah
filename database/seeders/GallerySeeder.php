<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $galleries = [
            ['title' => 'Gedung Utama Sekolah', 'order' => 1],
            ['title' => 'Laboratorium IPA', 'order' => 2],
            ['title' => 'Perpustakaan', 'order' => 3],
            ['title' => 'Masjid Sekolah', 'order' => 4],
            ['title' => 'Lapangan Olahraga', 'order' => 5],
            ['title' => 'Ruang Kelas', 'order' => 6],
            ['title' => 'Upacara Bendera', 'order' => 7],
            ['title' => 'Kegiatan Ekstrakurikuler', 'order' => 8],
        ];

        foreach ($galleries as $gallery) {
            DB::table('galleries')->insert(array_merge($gallery, [
                'image' => 'https://picsum.photos/seed/'.Str::slug($gallery['title']).'/800/600',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
