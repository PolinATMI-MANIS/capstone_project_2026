<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nama tabel diubah menjadi po_inventories
        Schema::create('po_inventories', function (Blueprint $table) {
            $table->id();
            
            // Relasi diubah menjadi item_id yang mengacu ke tabel items
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('cascade');
            
            $table->string('no_po')->unique();
            $table->string('produk_jadi');
            $table->integer('jumlah_produksi');
            $table->date('tanggal_produksi');
            $table->string('lokasi_penyimpanan');
            $table->date('deadline');
            $table->string('status')->default('Dalam Gudang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('po_inventories');
    }
};