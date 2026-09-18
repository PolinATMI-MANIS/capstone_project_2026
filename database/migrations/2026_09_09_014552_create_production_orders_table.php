<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_po')->unique();
            
            // Relasi ke tabel items (sebelumnya inventories)
            $table->foreignId('inventory_id')
                  ->nullable()
                  ->constrained('items')
                  ->onDelete('cascade'); 

            $table->string('produk'); // Nama produk (diisi otomatis dari nama barang di inventory)
            $table->integer('jumlah_produksi');
            $table->date('target_selesai');
            $table->text('keterangan')->nullable();
            $table->string('status')->default('Menunggu Bahan Baku');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};