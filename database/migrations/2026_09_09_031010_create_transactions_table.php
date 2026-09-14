<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique(); // No Transaksi (Contoh: TRX-001)
            $table->enum('type', ['IN', 'OUT']);            // IN (Barang Masuk), OUT (Barang Keluar)
            $table->date('transaction_date');               // Tanggal Transaksi
            $table->string('partner_name');                 // Nama Supplier (jika Masuk) / Tujuan (jika Keluar)
            $table->string('reference_no')->nullable();     // No Surat Jalan / PO
            $table->string('admin_name')->nullable();       // Admin yang bertugas
            $table->text('notes')->nullable();              // Keterangan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};