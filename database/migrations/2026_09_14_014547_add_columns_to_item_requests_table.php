<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah kolom 'user_id' BELUM ada di tabel 'item_requests'
        if (!Schema::hasColumn('item_requests', 'user_id')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('request_code');
                $table->string('nama_barang')->nullable();
                $table->string('satuan')->nullable();
                $table->decimal('harga', 15, 2)->default(0)->nullable();
                $table->string('no_surat_jalan')->nullable();
                $table->unsignedBigInteger('supplier_id')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('item_requests', 'user_id')) {
            Schema::table('item_requests', function (Blueprint $table) {
                $table->dropColumn([
                    'user_id', 'nama_barang', 'satuan', 'harga', 'no_surat_jalan', 'supplier_id'
                ]);
            });
        }
    }
};