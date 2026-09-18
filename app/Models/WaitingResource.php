<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitingResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_code',
        'product_name',
        'quantity',
        'status',
        'notes',
    ];
    public function itemRequests()
{
    return $this->hasMany(ItemRequest::class, 'resource_id');
}

}