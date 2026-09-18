<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRequest extends Model
{
    use HasFactory;

    protected $table = 'item_requests';

    protected $fillable = [
        'request_code',
        'user_id',
        'production_order_id',
        'resource_id',
        'department',
        'item_id',
        'nama_barang',
        'qty',
        'satuan',
        'supplier_id',
        'supplier',
        'lokasi',
        'stok_min',
        'harga',
        'type',
        'status',
        'notes',
    ];

    protected $attributes = [
        'status' => 'Pending Admin',
    ];

    protected $casts = [
        'qty'   => 'integer',
        'harga' => 'decimal:2',
    ];

    // Relasi Master
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    // Relasi Modul Produksi & Resource
    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class, 'resource_id');
    }
}