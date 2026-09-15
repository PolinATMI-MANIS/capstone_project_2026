<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('machine_powers', function (Blueprint $table) {
            if (!Schema::hasColumn('machine_powers', 'start_time')) {
                $table->time('start_time')->nullable(); 
            }
        });
    }

    public function down()
    {
        Schema::table('machine_powers', function (Blueprint $table) {
            if (Schema::hasColumn('machine_powers', 'start_time')) {
                $table->dropColumn('start_time');
            }
        });
    }
};
