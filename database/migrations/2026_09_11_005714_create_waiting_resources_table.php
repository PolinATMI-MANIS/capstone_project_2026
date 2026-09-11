<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waiting_resources', function (Blueprint $table) {
            $table->id();
            $table->string('production_code');
            $table->string('product_name');
            $table->integer('quantity');
            $table->string('status')->default('Waiting');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waiting_resources');
    }
};