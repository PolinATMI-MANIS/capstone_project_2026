<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $table = 'production_orders';

    protected $guarded = ['id'];

    // Casting tipe data otomatis
    protected $casts = [
        'jumlah_produksi' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessor untuk menghitung Target per Jam (TH) langsung dari model
    public function getTargetPerJamAttribute()
    {
        return ceil($this->jumlah_produksi / 8);
    }

    // ==========================================
    // RELASI DATABASE
    // ==========================================

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'po_no', 'no_po');
    }

    public function itemRequests()
    {
        return $this->hasMany(ItemRequest::class, 'production_order_id');
    }

    // Relasi ke Model Mesin (jika menggunakan tabel mesin dari modul resources)
    public function mesin()
    {
        return $this->belongsTo(Mesin::class, 'mesin_id');
    }

    // Relasi ke Model User / Operator (jika relasi menggunakan ID user/manpower)
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}