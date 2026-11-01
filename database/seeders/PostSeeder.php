<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID User pertama (biasanya admin)
        $adminId = User::first()?->id ?? 1;

        // Ambil ID Kategori berdasarkan nama (Pastikan kategori ini sudah ada di DB)
        $catKegiatan = DB::table('categories')->where('name', 'Kegiatan')->first()?->id;
        $catPrestasi = DB::table('categories')->where('name', 'Prestasi')->first()?->id;
        $catAkademik = DB::table('categories')->where('name', 'Akademik')->first()?->id;
        $catEkstrakurikuler = DB::table('categories')->where('name', 'Ekstrakurikuler')->first()?->id;

        $posts = [
            [
                'title' => 'Aksi Lingkungan: Kerja Bakti Massal Sambut Tahun Ajaran Baru',
                'slug' => 'kerja-bakti-massal-sekolah',
                'category_id' => $catKegiatan,
                'content' => '<p>Seluruh civitas akademika berkumpul hari ini untuk melaksanakan kerja bakti lingkungan. Kegiatan ini bertujuan menciptakan suasana belajar yang asri dan nyaman.</p><p>Fokus utama adalah penataan taman depan sekolah dan pembersihan area selokan untuk mencegah genangan air saat musim hujan.</p>',
                'excerpt' => 'Gotong royong guru dan siswa dalam membersihkan lingkungan sekolah demi kenyamanan belajar.',
                'is_published' => true,
            ],
            [
                'title' => 'Workshop Pemrograman: Membangun Aplikasi Pertama dengan Laravel 12',
                'slug' => 'workshop-coding-laravel-12',
                'category_id' => $catAkademik,
                'content' => '<p>Ekstrakurikuler IT menyelenggarakan workshop coding intensif. Siswa belajar dasar-dasar MVC dan cara kerja Routing di Laravel 12.</p><p>Hasil dari workshop ini, setiap siswa berhasil mendeploy aplikasi "Daily Journal" sederhana di server lokal masing-masing.</p>',
                'excerpt' => 'Siswa antusias belajar pemrograman backend menggunakan framework Laravel 12 terbaru.',
                'is_published' => true,
            ],
            [
                'title' => 'Tim Robotika Sekolah Sabet Juara Nasional di ITB',
                'slug' => 'juara-robotika-nasional-itb',
                'category_id' => $catPrestasi,
                'content' => '<p>Selamat kepada Tim Robotika yang berhasil meraih medali emas dalam kategori Robot Penyelamat. Inovasi sensor ultrasonik yang mereka buat mendapat pujian dari dewan juri.</p>',
                'excerpt' => 'Kemenangan membanggakan tim robotika sekolah di ajang kompetisi teknologi nasional.',
                'is_published' => true,
            ],
            [
                'title' => 'Latihan Rutin Basket: Mempersiapkan Mental Juara DBL',
                'slug' => 'latihan-basket-rutin-dbl',
                'category_id' => $catEkstrakurikuler,
                'content' => '<p>Tim basket sekolah terus mengasah kemampuan *defense* dan *shooting* dalam latihan sore yang diadakan 3 kali seminggu. Pelatih menekankan pentingnya disiplin fisik dan kerjasama tim.</p>',
                'excerpt' => 'Persiapan intensif tim basket sekolah menuju kompetisi antar pelajar paling bergengsi.',
                'is_published' => true,
            ],
            [
                'title' => 'Pemanfaatan AI dalam Pembelajaran Sejarah di Kelas',
                'slug' => 'belajar-sejarah-dengan-ai',
                'category_id' => $catAkademik,
                'content' => '<p>Guru sejarah mulai menerapkan metode baru dengan menggunakan visualisasi AI untuk menggambarkan suasana perang kemerdekaan, membuat siswa lebih mudah memahami konteks sejarah.</p>',
                'excerpt' => 'Inovasi pembelajaran menggunakan teknologi AI untuk memvisualisasikan peristiwa masa lalu.',
                'is_published' => true,
            ],
        ];

        foreach ($posts as $post) {
            DB::table('posts')->insert(array_merge($post, [
                'user_id' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
