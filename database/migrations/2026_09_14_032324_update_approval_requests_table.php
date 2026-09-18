<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('approval_requests', function (Blueprint $table) {
            // Cek jika kolom action_type belum ada, baru tambahkan
            if (!Schema::hasColumn('approval_requests', 'action_type')) {
                $table->string('action_type')->default('delete');
            }
            
            // Cek jika kolom payload belum ada, baru tambahkan
            if (!Schema::hasColumn('approval_requests', 'payload')) {
                $table->longText('payload')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approval_requests', function (Blueprint $table) {
            //
        });
    }
};
