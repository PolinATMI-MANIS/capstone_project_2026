<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachinePower extends Model
{
    use HasFactory;

    protected $table = 'machine_powers';

    protected $fillable = [
        'machine_name',
        'machine_type',
        'location',
        'capacity',
        'status',
        'foto',
        'man_power_id',
        'start_time'
    ];

    public function operator()
    {
        return $this->belongsTo(ManPower::class, 'man_power_id');
    }

    public function manPower() 
    {
        return $this->belongsTo(ManPower::class, 'man_power_id');
    }
}