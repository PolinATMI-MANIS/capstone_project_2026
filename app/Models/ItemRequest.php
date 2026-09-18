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
        'harga',
        'type',
        'status',
        'notes',
    ];

    // Nilai default untuk kolom tertentu saat data baru dibuat
    protected $attributes = [
        'status' => 'Pending Admin',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}