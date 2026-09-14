<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('item_requests', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->nullable()->after('request_code');
        $table->string('nama_barang')->nullable();
        $table->string('satuan')->nullable();
        $table->decimal('harga', 15, 2)->default(0)->nullable();
        $table->string('no_surat_jalan')->nullable();
        $table->unsignedBigInteger('supplier_id')->nullable();
    });
}
};
