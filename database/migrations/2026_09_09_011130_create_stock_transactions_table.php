<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
{
    Schema::create('stock_transactions', function (Blueprint $table) {
        $table->id();
        $table->string('transaction_no')->unique(); // No Transaksi
        $table->enum('type', ['in', 'out']);        // Transaksi Masuk / Keluar
        $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
        $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
        $table->string('po_no')->nullable();         // Production Order / PO (untuk Barang Keluar/Masuk)
        $table->integer('qty');                     // Jumlah
        $table->decimal('price', 15, 2)->default(0); // Harga
        $table->string('destination_purpose')->nullable(); // Tujuan / Retur / Keterangan
        $table->string('admin_name')->nullable();   // Nama Admin
        $table->date('transaction_date');
        $table->timestamps();
    });
}
};
