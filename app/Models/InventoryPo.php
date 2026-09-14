<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryPo extends Model
{
    use HasFactory;

    protected $table = 'po_inventories';

    protected $fillable = [
        'item_id',
        'no_po',
        'produk_jadi',
        'jumlah_produksi',
        'tanggal_produksi',
        'lokasi_penyimpanan',
        'deadline',
        'status',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}