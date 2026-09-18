<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Casting tipe data otomatis
    protected $casts = [
        'jumlah_produksi' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessor untuk menghitung Target per Jam (TH) langsung dari model
    public function getTargetPerJamAttribute()
    {
        return ceil($this->jumlah_produksi / 8);
    }

    // Relasi ke Model Mesin (jika ada tabel mesins)
    public function mesin()
    {
        return $this->belongsTo(Mesin::class, 'mesin_id');
    }

    // Relasi ke Model User / Operator (jika relasi menggunakan ID)
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}