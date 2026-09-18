<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Menambahkan kolom stok minimum dan supplier ke tabel items
            $table->integer('stok_min')->default(0)->after('stok');
            $table->string('supplier')->nullable()->after('harga');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['stok_min', 'supplier']);
        });
    }
};