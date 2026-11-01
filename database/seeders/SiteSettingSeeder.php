<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'school_name', 'value' => 'SMP Tunas Harapan Bekasi'],
            ['key' => 'school_short_name', 'value' => 'SMP Tupan'],
            ['key' => 'school_motto', 'value' => 'Unggul dalam Ilmu, Berkarakter Islami'],
            ['key' => 'school_address', 'value' => 'JL. RS. Mekar Sari No 71.B Bekasi Jaya-Bekasi Timur 17112
                Kota Bekasi'],
            ['key' => 'school_phone', 'value' => '6281770748835'],
            ['key' => 'school_email', 'value' => 'smptupanbekasi71@gmail.com'],
            ['key' => 'school_facebook', 'value' => 'https://web.facebook.com/p/SMP-Tunas-Harapan-Bekasi-100063487035976/'],
            ['key' => 'school_instagram', 'value' => 'https://www.instagram.com/smptunasharapan_bekasi/'],
            ['key' => 'school_youtube', 'value' => 'https://www.youtube.com/@SMPTunasHarapanBekasi'],
            ['key' => 'school_tiktok', 'value' => 'https://www.tiktok.com/@smptunasharapanbekasi'],
            ['key' => 'spmb_info', 'value' => '-'],
            ['key' => 'headmaster_welcome', 'value' => 'Saya, Dra.Hj. Neneng Yeti.D, M.Pd merasa bangga menyambut Anda di halaman digital kami. Di sini, Anda akan menemukan informasi terkini tentang sekolah kami, termasuk program akademik, kegiatan ekstrakurikuler, dan prestasi yang telah kami raih. Di SMP Tunas Harapan Bekasi , kami berkomitmen untuk memberikan pendidikan yang berkualitas, mendukung perkembangan holistik setiap siswa, dan menciptakan lingkungan belajar yang inspiratif. Tim kami yang profesional siap mendampingi dan membantu siswa dalam mencapai potensi terbaik mereka.Jelajahi website kami untuk mengetahui lebih lanjut tentang sekolah kami. Jika ada pertanyaan atau masukan, jangan ragu untuk menghubungi kami.Terima kasih atas kunjungan Anda. Mari bersama-sama membangun masa depan yang cerah untuk anak-anak kita.'],
        ];

        foreach ($settings as $setting) {
            DB::table('site_settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
