<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_do');
            $table->string('ref_po'); // Relasi ke nomor PO
            $table->date('tanggal_kirim');
            $table->string('driver')->nullable();
            $table->string('status')->default('On Progress');
            $table->string('status_pengiriman')->nullable(); // Ditambahkan
            $table->string('status_approval')->default('Pending'); // Ditambahkan
            $table->text('catatan_approval')->nullable(); // Ditambahkan
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};