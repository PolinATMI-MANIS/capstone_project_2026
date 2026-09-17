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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('no_po')->unique()->nullable();
            $table->string('nama_customer');
            $table->string('kode_barang')->nullable();
            $table->string('nama_barang');
            $table->integer('kuantitas');
            $table->decimal('harga_satuan', 15, 2);
            $table->string('permintaan_material')->nullable();
            $table->date('tanggal_pemesanan');
            $table->dateTime('waktu_tgl_deadline');
            $table->string('estimasi_pengerjaan')->nullable();
            
            // PERBAIKAN: Menggunakan string agar fleksibel menampung 'Approved', 'Rejected', dll.
            $table->string('status', 50)->default('On Progress');
            
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};