<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machine_powers', function (Blueprint $table) {
            $table->foreignId('man_power_id')
                ->nullable()
                ->constrained('man_powers')
                ->nullOnDelete();

            $table->time('start_time')->nullable();

            $table->foreignId('production_order_id')
                ->nullable()
                ->constrained('production_orders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('machine_powers', function (Blueprint $table) {
            $table->dropForeign(['man_power_id']);
            $table->dropForeign(['production_order_id']);

            $table->dropColumn([
                'man_power_id',
                'start_time',
                'production_order_id',
            ]);
        });
    }
};