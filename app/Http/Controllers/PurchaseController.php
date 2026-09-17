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

        // 2. Simpan data Purchase
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

        // 3. Generate Nomor DO otomatis
        $currentMonth = Carbon::now()->format('Ym');
        $lastDo = DeliveryOrder::where('no_do', 'LIKE', 'DO-' . $currentMonth . '-%')
                               ->orderBy('no_do', 'desc')
                               ->first();
        
        $newDoNumber = $lastDo ? ((int) substr($lastDo->no_do, -3)) + 1 : 1;
        $noDo = 'DO-' . $currentMonth . '-' . sprintf('%03d', $newDoNumber);

        // 4. Simpan ke database Delivery Order
        DeliveryOrder::create([
            'no_do'         => $noDo,
            'ref_po'        => $noPo, 
            'tanggal_kirim' => now()->format('Y-m-d'),
            'driver'        => '-', 
            'status'        => 'Waiting',
            'keterangan'    => 'Dibuat otomatis dari sistem PO',
        ]);

        return redirect('/purchase')->with('success', 'Purchase Order berhasil ditambahkan!');
    }

    public function show($id)
    {
        $purchase = Purchase::findOrFail($id);

        return view('purchase-detail', compact('purchase'));
    }

    public function hub()
    {
        $totalPo   = Purchase::count();
        $totalDo   = DeliveryOrder::count();
        $waitingPo = Purchase::where('status', 'Waiting')->count();

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

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_customer'      => 'required',
            'nama_barang'        => 'required',
            'tanggal_pemesanan'  => 'required',
            'waktu_tgl_deadline' => 'required',
            'kuantitas'          => 'required|numeric',
            'harga_satuan'       => 'required|numeric',
        ]);

        $purchase = Purchase::findOrFail($id);
        
        $purchase->update([
            'nama_customer'       => $request->nama_customer,
            'nama_barang'         => $request->nama_barang,
            'kode_barang'         => $request->kode_barang,
            'permintaan_material' => $request->permintaan_material,
            'tanggal_pemesanan'   => $request->tanggal_pemesanan,
            'waktu_tgl_deadline'  => $request->waktu_tgl_deadline,
            'kuantitas'           => $request->kuantitas,
            'harga_satuan'        => $request->harga_satuan,
            'estimasi_pengerjaan' => $request->estimasi_pengerjaan,
        ]);

        return redirect()->back()->with('success', 'Data Purchase Order berhasil diperbarui!');
    }

    public function approval(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        $purchase->update([
            'status'           => $request->status,
            'catatan_approval' => $request->catatan_approval,
        ]);

        return redirect()->back()->with('success', 'Status approval berhasil diperbarui!');
    }

    // Fungsi Pengajuan Hapus dari Admin ke Superadmin
    public function requestDelete(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $purchase = Purchase::findOrFail($id);

        // Mengisi catatan pengajuan jika ada kolom penampungnya
        if (\Schema::hasColumn('purchases', 'delete_reason')) {
            $purchase->update(['delete_reason' => $request->reason]);
        }

        return redirect()->back()->with('info', 'Pengajuan hapus untuk PO ' . $purchase->no_po . ' berhasil dikirim ke Superadmin.');
    }

    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);

        DeliveryOrder::where('ref_po', $purchase->no_po)->delete();

        $purchase->delete();

        return redirect()->back()->with('success', 'Data Purchase Order dan Delivery Order terkait berhasil dihapus!');
    }
}