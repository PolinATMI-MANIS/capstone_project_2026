<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machine_powers', function (Blueprint $table) {
            // Cek dulu apakah kolom 'operating_hours' benar-benar ada sebelum di-rename
            if (Schema::hasColumn('machine_powers', 'operating_hours')) {
                $table->renameColumn('operating_hours', 'start_time');
            } 
            // Jika kolom 'operating_hours' tidak ada, cek apakah 'start_time' sudah ada. Jika belum, buat baru.
            elseif (!Schema::hasColumn('machine_powers', 'start_time')) {
                $table->time('start_time')->nullable();
            }

            // Tambahkan kolom lain dengan aman jika belum ada
            if (!Schema::hasColumn('machine_powers', 'man_power_id')) {
                $table->foreignId('man_power_id')->nullable()->constrained('man_powers')->nullOnDelete();
            }

            if (!Schema::hasColumn('machine_powers', 'production_order_id')) {
                $table->foreignId('production_order_id')->nullable()->constrained('production_orders')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('machine_powers', function (Blueprint $table) {
            if (Schema::hasColumn('machine_powers', 'start_time')) {
                $table->dropColumn('start_time');
            }
            if (Schema::hasColumn('machine_powers', 'man_power_id')) {
                $table->dropForeign(['man_power_id']);
                $table->dropColumn('man_power_id');
            }
            if (Schema::hasColumn('machine_powers', 'production_order_id')) {
                $table->dropForeign(['production_order_id']);
                $table->dropColumn('production_order_id');
            }
        });
    }
};