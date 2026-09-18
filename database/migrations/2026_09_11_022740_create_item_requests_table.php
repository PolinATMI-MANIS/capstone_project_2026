<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            
            // Relasi ke Produksi & Item (sebelumnya resources)
            $table->foreignId('production_order_id')->nullable()->constrained('production_orders')->nullOnDelete();
            $table->foreignId('resource_id')->nullable()->constrained('items')->nullOnDelete();

            $table->string('department')->nullable();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->string('nama_barang')->nullable();
            $table->decimal('qty', 10, 2);
            $table->string('satuan')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('supplier')->nullable();
            $table->string('lokasi')->nullable();
            $table->integer('stok_min')->default(5);
            $table->decimal('harga', 15, 2)->default(0);
            $table->string('type'); // in, out, del, create_barang, general_request
            $table->string('status');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_requests');
    }
};