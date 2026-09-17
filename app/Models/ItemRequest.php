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

    // Nilai default untuk kolom tertentu saat data baru dibuat
    protected $attributes = [
        'status' => 'Pending Admin',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga' => 'decimal:2',
    ];

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
}