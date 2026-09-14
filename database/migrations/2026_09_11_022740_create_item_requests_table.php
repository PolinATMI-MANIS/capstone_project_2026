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
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('department')->nullable();
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('cascade');
            $table->string('nama_barang')->nullable();
            $table->integer('qty');
            $table->string('satuan')->nullable();
            $table->decimal('harga', 15, 2)->default(0)->nullable();
            $table->string('no_surat_jalan')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('type'); // in, out, create_barang, delete_barang, general_request
            $table->string('status', 100)->default('Pending Admin');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_requests');
    }
};