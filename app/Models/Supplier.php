<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
    'name', 'nama_supplier', 
    'code', 'kode_supplier', 'supplier_code',
    'phone', 'kontak', 
    'address', 'alamat', 
    'email', 'pic', 'status'
];

}