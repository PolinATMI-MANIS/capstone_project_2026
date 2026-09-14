<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    // Tuliskan nama tabel di database jika tidak menggunakan bentuk plural standar (opsional tapi aman)
    protected $table = 'stock_transactions';

    // Daftarkan semua kolom yang nanti akan di-input dari Controller
    protected $fillable = [
        'transaction_code',
        'item_id',
        'type', // Masuk/Keluar
        'qty',
        'user_id',
        'notes',
    ];
}