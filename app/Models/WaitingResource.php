<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitingResource extends Model
{
    use HasFactory;

    protected $table = 'production_orders'; // Arahkan ke tabel asli database

    protected $fillable = [
        'no_po',
        'produk',
        'jumlah_produksi',
        'status',
        'keterangan',
        'target_selesai',
    ];
    public function itemRequests()
{
    return $this->hasMany(ItemRequest::class, 'resource_id');
}

}