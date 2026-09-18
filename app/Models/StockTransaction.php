<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $table = 'stock_transactions';

    // $fillable disesuaikan dengan struktur migration stock_transactions
    protected $fillable = [
        'transaction_no',
        'type',
        'item_id',
        'supplier_id',
        'po_no',
        'qty',
        'price',
        'destination_purpose',
        'admin_name',
        'transaction_date',
    ];

    // Relasi ke Item
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    // Relasi ke Production Order via po_no
    public function productionOrder()
    {
        return $this->belongsTo(ProductionOrder::class, 'po_no', 'no_po');
    }
}