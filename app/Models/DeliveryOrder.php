<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;

    // Tambahkan baris ini, ganti 'deliveries' dengan nama tabel asli kamu di phpMyAdmin 

    protected $guarded = ['id'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'ref_po', 'no_po');
    }
}