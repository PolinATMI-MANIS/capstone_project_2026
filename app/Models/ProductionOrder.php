<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $table = 'production_orders';

    protected $fillable = [
        'no_po',
        'inventory_id',
        'item_id',
        'produk',
        'jumlah_produksi',
        'target_selesai',
        'keterangan',
        'status',
    ];

    // Relasi ke Model Inventory (Tabel inventories)
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    // Relasi ke Master Item (Tabel items)
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    // Relasi ke Transaksi Stok berdasarkan nomor PO/SPK
    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'po_no', 'no_po');
    }

    // Relasi ke Permintaan Bahan Baku (Item Request)
    public function itemRequests()
    {
        return $this->hasMany(ItemRequest::class, 'production_order_id');
    }
}