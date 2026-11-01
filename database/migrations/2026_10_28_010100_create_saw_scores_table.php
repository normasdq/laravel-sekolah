<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saw_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('saw_criterion_id')->constrained('saw_criteria')->cascadeOnDelete();
            $table->decimal('value', 8, 2);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['registration_id', 'saw_criterion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saw_scores');
    }
};

