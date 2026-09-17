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

    /**
     * Relasi ke ItemRequest (One to Many)
     */
    public function itemRequests()
    {
        return $this->hasMany(ItemRequest::class, 'item_id');
    }
}