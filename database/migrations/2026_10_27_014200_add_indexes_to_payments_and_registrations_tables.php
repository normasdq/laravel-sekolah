<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->index('status', 'payments_status_index');
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->index('payment_status', 'registrations_payment_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_status_index');
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropIndex('registrations_payment_status_index');
        });
    }
};
