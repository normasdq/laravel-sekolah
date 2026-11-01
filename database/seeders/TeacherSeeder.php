<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            ['name' => 'Dra. Hj. Neneng Yeti. D, M.Pd', 'position' => 'Kepala SMP Tunas Harapan Bekasi', 'type' => 'guru', 'order' => 1, 'bio' => ''],

            ['name' => 'Supriyanto, S.Pd', 'position' => 'PKS Kesiswaan', 'type' => 'guru', 'order' => 2, 'bio' => ''],

            ['name' => 'Agus Maulana, S.Kom', 'position' => 'PKS Kurikulum', 'type' => 'guru', 'order' => 4, 'bio' => ''],

            ['name' => 'Atin Nurhayatin, S.Pd', 'position' => 'Guru Bimbingan Konseling', 'type' => 'guru', 'order' => 5, 'bio' => ''],

            ['name' => 'Dlia Murtafiah, S.Hi', 'position' => 'Guru Prakarya', 'type' => 'guru', 'order' => 6, 'bio' => ''],

            ['name' => 'Sunoto, S.Pd', 'position' => 'Guru Agama Budha', 'type' => 'guru', 'order' => 7, 'bio' => ''],

            ['name' => 'Hokdi Sitompul, S.Pd', 'position' => 'Guru Agama Kristen', 'type' => 'guru', 'order' => 8, 'bio' => ''],

            ['name' => 'M. Ridwan, S.Sy', 'position' => 'Guru Agama Islam', 'type' => 'guru', 'order' => 9, 'bio' => ''],

            ['name' => 'Hj. Nuryaningsih, S.Pd', 'position' => 'Guru IPS', 'type' => 'guru', 'order' => 10, 'bio' => ''],

            ['name' => 'Wawan S, S.Pd', 'position' => 'Guru PJOK', 'type' => 'guru', 'order' => 11, 'bio' => ''],

            ['name' => 'Ir. Gunarso', 'position' => 'Guru IPA', 'type' => 'guru', 'order' => 12, 'bio' => ''],

            ['name' => 'Siti Ruqoyah, S.Pd', 'position' => 'Guru Matematika', 'type' => 'guru', 'order' => 13, 'bio' => ''],

            ['name' => 'Nurunnisa Al Musyarafah, S.Pd', 'position' => 'Guru Bahasa Indonesia', 'type' => 'guru', 'order' => 14, 'bio' => ''],

            ['name' => 'Guntur Zaelani', 'position' => 'Tata Usaha', 'type' => 'staff', 'order' => 15, 'bio' => ''],

            ['name' => 'Sarah Fatmaningish', 'position' => 'Tata Usaha', 'type' => 'staff', 'order' => 16, 'bio' => ''],

        ];

        foreach ($teachers as $teacher) {
            DB::table('teachers')->insert(array_merge($teacher, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
