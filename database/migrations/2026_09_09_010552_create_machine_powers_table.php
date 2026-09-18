<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_powers', function (Blueprint $table) {
            $table->id();
            $table->string('machine_name');
            $table->string('machine_type');
            $table->string('location');
            $table->string('capacity');
            $table->enum('status', ['Running', 'Breakdown', 'Standby'])->default('Standby');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_powers');
    }
};