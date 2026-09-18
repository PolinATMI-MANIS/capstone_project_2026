<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('items', function (Blueprint $table) {
        $table->id();
        $table->string('item_code')->nullable();
        $table->string('name');
        $table->string('category')->default('Umum')->nullable();
        $table->string('unit')->default('Pcs')->nullable();
        $table->integer('stok')->default(0);
        $table->decimal('harga', 15, 2)->default(0);
        $table->string('lokasi')->nullable(); // Kolom lokasi/rak
        $table->string('status')->default('Aktif');
        $table->unsignedBigInteger('supplier_id')->nullable(); // Kolom relasi supplier
        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};