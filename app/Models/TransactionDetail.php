<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'item_id',
        'quantity',
        'price',
    ];

    /**
     * Relasi ke model Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relasi kembali ke Header Transaksi
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}