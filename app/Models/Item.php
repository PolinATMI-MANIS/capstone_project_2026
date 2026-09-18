<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'item_code',
        'name',
        'category',
        'unit',
        'stok',
        'stok_min',
        'lokasi',
        'supplier', 
        'harga',
        'status',
    ];

    protected $casts = [
        'stok' => 'integer',
        'stok_min' => 'integer',
        'harga' => 'decimal:2',
    ];

    public function itemRequests()
    {
        return $this->hasMany(ItemRequest::class, 'item_id');
    }

    // Relasi ke Riwayat Transaksi Stok (In/Out)
    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'item_id');
    }

    // Relasi ke Production Order
    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'item_id');
    }
}