<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Sinkronkan nama akun user (khusus role 'user') dengan nama calon siswa yang terdaftar di SPMB
        $registrations = DB::table('registrations')
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->whereNull('deleted_at')
            ->select('user_id', 'name')
            ->get();

        foreach ($registrations as $reg) {
            if ($reg->user_id) {
                DB::table('users')
                    ->where('id', $reg->user_id)
                    ->where('role', 'user')
                    ->update(['name' => $reg->name]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu membalikkan nama karena nama pendaftar resmi adalah sumber kebenaran
    }
};

