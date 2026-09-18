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

    public function index()
    {
        // Pilihan A: Menghitung semua data di tabel RnD
        $rndCount = RnDfeature::count();
        
        // Pilihan B (Alternatif): Menghitung hanya data yang berstatus tertentu
        // $rndCount = RnDfeature::where('status', 'Pengajuan Konsep')->count();

        // Kirim variabel ke view
        return view('dashboard.index', compact('rndCount')); 
        // Catatan: Gabungkan compact ini dengan variabel modul lain (Inventory, Production, dll) jika sudah ada.
    }
}