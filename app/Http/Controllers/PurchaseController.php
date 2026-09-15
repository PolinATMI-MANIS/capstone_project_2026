<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use Carbon\Carbon;
use App\Models\DeliveryOrder;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::latest()->get();
        return view('purchase', compact('purchases'));
    }

    public function store(Request $request)
    {
        // 1. Generate No PO otomatis
        $noPo = 'PO-' . date('Ym') . '-' . sprintf('%03d', Purchase::count() + 1);

        // 2. Simpan data Purchase (dan simpan ke dalam variabel $purchase)
        $purchase = Purchase::create([
            'no_po'               => $noPo,
            'nama_customer'       => $request->nama_customer,
            'kode_barang'         => $request->kode_barang,
            'nama_barang'         => $request->nama_barang,
            'kuantitas'           => $request->kuantitas,
            'harga_satuan'        => $request->harga_satuan,
            'permintaan_material' => $request->permintaan_material,
            'tanggal_pemesanan'   => $request->tanggal_pemesanan,
            'waktu_tgl_deadline'  => $request->waktu_tgl_deadline,
            'estimasi_pengerjaan' => $request->estimasi_pengerjaan,
            'status'              => $request->status,
            'keterangan'          => $request->keterangan,
        ]);

        // 3. LOGIKA OTOMATIS: Langsung buatkan data Delivery Order!
        // a. Generate Nomor DO otomatis yang aman
        $currentMonth = Carbon::now()->format('Ym');
        $lastDo = DeliveryOrder::where('no_do', 'LIKE', 'DO-' . $currentMonth . '-%')
                               ->orderBy('no_do', 'desc')
                               ->first();
        
        $newDoNumber = $lastDo ? ((int) substr($lastDo->no_do, -3)) + 1 : 1;
        $noDo = 'DO-' . $currentMonth . '-' . sprintf('%03d', $newDoNumber);

        // b. Simpan ke database Delivery Order lengkap dengan datanya
        DeliveryOrder::create([
            'no_do'         => $noDo,
            'ref_po'        => $noPo, 
            'tanggal_kirim' => now()->format('Y-m-d'), // Diisi default tanggal hari ini
            'driver'        => '-', 
            'status'        => 'Waiting', // Status awal
            'keterangan'    => 'Dibuat otomatis dari sistem PO',
        ]);

        return redirect('/purchase')->with('success', 'Purchase Order berhasil ditambahkan!');
    }

    public function show($id)
    {
        $purchase = \App\Models\Purchase::findOrFail($id);

        return view('purchase-detail', compact('purchase'));
    }

    public function hub()
    {
        $totalPo   = Purchase::count();
        $totalDo   = DeliveryOrder::count();
        $waitingPo = Purchase::where('status', 'Waiting')->count();

        // GANTI BARIS 74 (YANG ERROR TADI) DENGAN KODE INI:
        $totalNilaiPo = Purchase::all()->sum(function ($po) {
            return ($po->kuantitas ?? 0) * ($po->harga_satuan ?? $po->harga ?? 0);
        });

        $recentPurchases  = Purchase::latest()->take(5)->get();
        $recentDeliveries = DeliveryOrder::latest()->take(5)->get();

        return view('purchase-delivery', compact(
            'totalPo', 
            'totalDo', 
            'waitingPo', 
            'totalNilaiPo',
            'recentPurchases',
            'recentDeliveries'
        ));
    }
}