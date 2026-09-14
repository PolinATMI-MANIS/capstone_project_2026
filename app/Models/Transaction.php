<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke detail transaksi (untuk mengambil item-item yang dibeli/keluar)
    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }

    // Relasi ke supplier (jika ada kolom supplier_id di tabel transactions)
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}