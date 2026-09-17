<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('item_requests', function (Blueprint $table) {
        $table->id();
        $table->string('request_code');
        $table->foreignId('user_id')->constrained();
        $table->string('department')->nullable();
        $table->foreignId('item_id')->nullable();
        $table->string('nama_barang');
        $table->decimal('qty', 10, 2);
        $table->string('satuan');
        $table->foreignId('supplier_id')->nullable();
        $table->string('supplier')->nullable();
        $table->string('lokasi')->nullable();
        $table->integer('stok_min')->default(5);
        $table->decimal('harga', 15, 2)->default(0);
        $table->string('type'); // in, out, del, create_barang
        $table->string('status');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
    }

    public function down()
    {
        Schema::dropIfExists('item_requests');
    }
};