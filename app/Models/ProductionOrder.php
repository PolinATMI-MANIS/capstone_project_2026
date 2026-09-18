<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $table = 'production_orders';

    protected $guarded = ['id'];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class, 'po_no', 'no_po');
    }

    public function itemRequests()
    {
        return $this->hasMany(ItemRequest::class, 'production_order_id');
    }
}