<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases';

    protected $fillable = [
        'no_po',
        'nama_customer',
        'kode_barang',
        'nama_barang',
        'kuantitas',
        'harga_satuan',
        'permintaan_material',
        'tanggal_pemesanan',
        'waktu_tgl_deadline',
        'estimasi_pengerjaan',
        'status',
        'keterangan'
    ];

    // Tambahkan relasi ini untuk memanggil Delivery Order dari Purchase
    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'ref_po', 'no_po');
    }
}