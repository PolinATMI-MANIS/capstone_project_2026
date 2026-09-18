<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'items';

    // Kolom yang dapat diisi secara mass-assignment
    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'stok',
        'satuan',
        'harga_satuan',
        'keterangan',
    ];

    /**
     * Relasi One-to-Many ke ProductionOrder
     * Satu barang inventory bisa digunakan di banyak SPK/Production Order
     */
    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'inventory_id');
    }
}