<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RnDfeature extends Model
{
    use HasFactory;

    protected $table = 'rn_dfeatures';

    protected $fillable = [
        'nama_produk',
        'nama_pengaju',
        'deskripsi_konsep',
        'gambar',
        'dokumen_teknis',
        'status',
        'catatan_revisi',
    ];
}