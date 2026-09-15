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
            $table->renameColumn('operating_hours', 'start_time');
        });
    }
    public function down()
    {
        Schema::table('machine_powers', function (Blueprint $table) {
            $table->renameColumn('start_time', 'operating_hours');
        });
    }
};
