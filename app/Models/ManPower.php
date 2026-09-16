<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManPower extends Model
{
    use HasFactory;

    protected $table = 'man_powers';

    protected $fillable = [
        'nama',
        'posisi',
        'status',
        'foto',
    ];

    public function machines()
    {
        return $this->hasMany(MachinePower::class, 'man_power_id');
    }

    public function productionOrder() 
    {
        return $this->belongsTo(ProductionOrder::class, 'production_order_id');
    }
}

