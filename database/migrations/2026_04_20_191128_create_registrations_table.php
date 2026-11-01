<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();

            // RELASI KE USER (PENTING)
            // Menggunakan constrained() agar otomatis terhubung ke tabel users
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            // NOMOR PENDAFTARAN (Unik untuk identitas pendaftar)
            $table->string('registration_number')->unique()->nullable();

            $table->string('name');
            $table->string('nisn', 20)->unique();
            $table->string('school_origin');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->text('address')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone', 20)->nullable();

            // STATUS PENDAFTARAN
            // Saya tambahkan 'lulus' & 'tidak_lulus' agar sinkron dengan Dashboard
            $table->enum('status', ['pending', 'verified', 'rejected', 'lulus', 'tidak_lulus'])->default('pending');

            $table->text('notes')->nullable();
            $table->timestamps();

            // INDEXING UNTUK PERFORMA
            $table->index('status');
            $table->index('created_at');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
