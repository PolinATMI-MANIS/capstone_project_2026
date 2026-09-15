<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rn_dfeatures', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->string('nama_pengaju');
            $table->text('deskripsi_konsep');
            $table->string('gambar')->nullable();
            $table->string('dokumen_teknis')->nullable(); // Untuk file dokumen riset/uji
            
            // Status sesuai tahapan Flowchart R&D
            // Valid nilai: 
            // 'Pengajuan Konsep', 'Konsep Perlu Revisi', 'Desain & Rekayasa',
            // 'Pembuatan Prototype', 'Uji Teknis Internal', 'Rekayasa Ulang',
            // 'Pengujian Validasi', 'Remedial Uji', 'Persiapan Produksi',
            // 'Produksi Komersial', 'Peluncuran Produk Baru', 'Ide Dihentikan'
            $table->string('status')->default('Pengajuan Konsep');
            
            $table->text('catatan_revisi')->nullable(); // Catatan masukan revisi dari evaluator
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rn_dfeatures');
    }
};