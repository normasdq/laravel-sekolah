<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Tambah kolom upload dokumen
            $table->string('kartu_keluarga')->nullable();
            $table->string('ijazah')->nullable(); // SKL jika belum ada ijazah
            $table->string('akte_kelahiran')->nullable();

            // Tambah kolom jadwal tes (opsional disimpan per siswa, atau global di settings)
            // Kita simpan status dokumen siswa ini
            $table->boolean('documents_verified')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['kartu_keluarga', 'ijazah', 'akte_kelahiran', 'documents_verified']);
        });
    }
};
