<?php

namespace Database\Seeders;

use App\Models\SawCriterion;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SawCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $criteria = [
            [
                'code' => 'C1',
                'name' => 'Nilai Ujian CBT',
                'type' => 'benefit',
                'weight' => 35,
                'source' => 'cbt_score',
                'min_value' => 0,
                'max_value' => 100,
                'scale_options' => null,
                'description' => 'Diambil otomatis dari skor hasil ujian seleksi online (CBT) pendaftar.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'C2',
                'name' => 'Rata-rata Nilai Rapor',
                'type' => 'benefit',
                'weight' => 30,
                'source' => 'manual',
                'min_value' => 0,
                'max_value' => 100,
                'scale_options' => null,
                'description' => 'Rata-rata nilai rapor SD/MI semester 1 s.d. 5 (skala 0-100).',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'C3',
                'name' => 'Nilai Wawancara & Baca Al-Qur\'an',
                'type' => 'benefit',
                'weight' => 20,
                'source' => 'manual',
                'min_value' => 0,
                'max_value' => 100,
                'scale_options' => null,
                'description' => 'Nilai wawancara karakter dan tes kemampuan baca tulis Al-Qur\'an (skala 0-100).',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'code' => 'C4',
                'name' => 'Prestasi Akademik / Non-Akademik',
                'type' => 'benefit',
                'weight' => 15,
                'source' => 'manual',
                'min_value' => 1,
                'max_value' => 5,
                'scale_options' => [
                    ['value' => 1, 'label' => '1 - Tidak Ada Prestasi'],
                    ['value' => 2, 'label' => '2 - Tingkat Sekolah / Kecamatan'],
                    ['value' => 3, 'label' => '3 - Tingkat Kota / Kabupaten'],
                    ['value' => 4, 'label' => '4 - Tingkat Provinsi'],
                    ['value' => 5, 'label' => '5 - Tingkat Nasional / Internasional'],
                ],
                'description' => 'Skor sertifikat prestasi tertinggi yang dimiliki calon siswa (skala 1-5).',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($criteria as $item) {
            SawCriterion::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
        }

        SiteSetting::updateOrCreate(
            ['key' => 'saw_threshold'],
            ['value' => '0.70']
        );

        SiteSetting::firstOrCreate(
            ['key' => 'spmb_re_registration_fee'],
            ['value' => '150000']
        );
    }
}

