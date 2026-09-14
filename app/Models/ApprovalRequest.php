<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        'target_name',
        'action_type',
        'payload',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}