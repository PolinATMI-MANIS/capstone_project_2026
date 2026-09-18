<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;

    // Buka proteksi mass-assignment agar semua kolom bisa di-update
    protected $guarded = []; 

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'ref_po', 'no_po');
    }
}