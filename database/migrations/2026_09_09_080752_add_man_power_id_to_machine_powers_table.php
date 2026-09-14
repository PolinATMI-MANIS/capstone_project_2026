<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machine_powers', function (Blueprint $table) {
            $table->unsignedBigInteger('man_power_id')->nullable()->after('capacity');
            // Opsional foreign key, kalau man_power dihapus mesin kembali bebas
            $table->foreign('man_power_id')->references('id')->on('man_powers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('machine_powers', function (Blueprint $table) {
            $table->dropForeign(['man_power_id']);
            $table->dropColumn('man_power_id');
        });
    }
};
