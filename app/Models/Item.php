<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'items'; // Sesuaikan nama tabel jika beda

    protected $fillable = [
        'item_code',
        'name',
        'category',
        'unit',
        'stok',          // Pastikan ini 'stok', bukan 'current_stock'
        'stok_min',      // Sesuai input controller baru
        'lokasi',        // Sesuai input controller baru
        'harga',
        'supplier',      // Sesuai input controller baru
        'status',
    ];
}