<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saw_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name');
            $table->enum('type', ['benefit', 'cost'])->default('benefit');
            $table->decimal('weight', 6, 2);
            $table->enum('source', ['manual', 'cbt_score'])->default('manual');
            $table->decimal('min_value', 8, 2)->default(0);
            $table->decimal('max_value', 8, 2)->default(100);
            $table->json('scale_options')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saw_criteria');
    }
};

