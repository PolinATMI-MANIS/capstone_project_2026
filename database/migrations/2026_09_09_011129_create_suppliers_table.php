<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
        $table->id();
        $table->string('supplier_code')->unique();
        $table->string('name');
        $table->text('alamat')->nullable();
        $table->string('phone')->nullable(); // Pastikan ini ada
        $table->string('email')->nullable(); // Pastikan ini ada
        $table->string('pic')->nullable();   // Pastikan ini ada
        $table->string('status')->default('Aktif');
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};