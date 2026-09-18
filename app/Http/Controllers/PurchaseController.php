<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\DeliveryOrder;
use App\Models\ProductionOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::latest()->get();
        return view('purchase', compact('purchases'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_customer'       => 'required',
            'permintaan_material' => 'required',
            'nama_barang'         => 'required',
            'waktu_tgl_deadline'  => 'required|date',
            'kuantitas'           => 'required|numeric',
            'harga_satuan'        => 'required|numeric',
        ]);

        $noPo = 'PO-' . date('Ym') . '-' . sprintf('%03d', Purchase::count() + 1);

        Purchase::create([
            'no_po'               => $noPo,
            'nama_customer'       => $request->nama_customer,
            'kode_barang'         => '-', 
            'nama_barang'         => $request->nama_barang,
            'kuantitas'           => $request->kuantitas,
            'harga_satuan'        => $request->harga_satuan,
            'permintaan_material' => $request->permintaan_material,
            'tanggal_pemesanan'   => now()->format('Y-m-d'), 
            'waktu_tgl_deadline'  => $request->waktu_tgl_deadline,
            'estimasi_pengerjaan' => '-', 
            'status'              => 'Waiting Approval',
            'keterangan'          => $request->keterangan,
        ]);

        $currentMonth = Carbon::now()->format('Ym');
        $lastDo = DeliveryOrder::where('no_do', 'LIKE', 'DO-' . $currentMonth . '-%')
                               ->orderBy('no_do', 'desc')
                               ->first();
        
        $newDoNumber = $lastDo ? ((int) substr($lastDo->no_do, -3)) + 1 : 1;
        $noDo = 'DO-' . $currentMonth . '-' . sprintf('%03d', $newDoNumber);

        DeliveryOrder::create([
            'no_do'         => $noDo,
            'ref_po'        => $noPo, 
            'tanggal_kirim' => now()->format('Y-m-d'),
            'driver'        => '-', 
            'status'        => 'Waiting',
            'keterangan'    => 'Dibuat otomatis dari sistem PO',
        ]);

        // ==============================================================
        // 6. AUTO-GENERATE SPK KE TABEL PRODUKSI (MENGGUNAKAN DB INSERT)
        // ==============================================================
        try {
            DB::table('production_orders')->insert([
                'no_po'           => $noPo, 
                'produk'          => $request->nama_barang, 
                'jumlah_produksi' => $request->kuantitas, 
                'target_selesai'  => Carbon::parse($request->waktu_tgl_deadline)->format('Y-m-d'), 
                'status'          => 'Menunggu Approval Admin', 
                'keterangan'      => 'Material: ' . $request->permintaan_material . ' | Di-generate dari PO: ' . $noPo,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } catch (\Exception $e) {
            dd("GAGAL MASUK PRODUKSI: " . $e->getMessage());
        }
        // ==============================================================

        return redirect('/purchase')->with('success', 'Purchase Order berhasil ditambahkan dan dikirim ke Produksi!');
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
        $waitingPo = Purchase::where('status', 'Waiting Approval')->count();

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
            'nama_customer'       => 'required',
            'nama_barang'         => 'required',
            'permintaan_material' => 'required',
            'waktu_tgl_deadline'  => 'required|date',
            'kuantitas'           => 'required|numeric',
            'harga_satuan'        => 'required|numeric',
        ]);

        $purchase = Purchase::findOrFail($id);
        
        $purchase->update([
            'nama_customer'       => $request->nama_customer,
            'nama_barang'         => $request->nama_barang,
            'permintaan_material' => $request->permintaan_material,
            'waktu_tgl_deadline'  => $request->waktu_tgl_deadline,
            'kuantitas'           => $request->kuantitas,
            'harga_satuan'        => $request->harga_satuan,
        ]);

        $prod = ProductionOrder::where('no_po', $purchase->no_po)->first();
        if($prod && in_array($prod->status, ['Menunggu Approval Admin', 'Menunggu Bahan Baku'])) {
            $prod->update([
                'produk'          => $request->nama_barang,
                'jumlah_produksi' => $request->kuantitas,
                'target_selesai'  => Carbon::parse($request->waktu_tgl_deadline)->format('Y-m-d'),
                'keterangan'      => 'Material: ' . $request->permintaan_material . ' | Diupdate dari PO',
            ]);
        }

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

    public function requestDelete(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $purchase = Purchase::findOrFail($id);

        if (\Schema::hasColumn('purchases', 'delete_reason')) {
            $purchase->update(['delete_reason' => $request->reason]);
        }

        return redirect()->back()->with('info', 'Pengajuan hapus untuk PO ' . $purchase->no_po . ' berhasil dikirim ke Superadmin.');
    }

    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);

        DeliveryOrder::where('ref_po', $purchase->no_po)->delete();
        ProductionOrder::where('no_po', $purchase->no_po)->delete();
        
        $purchase->delete();

        return redirect()->back()->with('success', 'Data Purchase Order, Delivery Order, dan Produksi terkait dihapus permanen!');
    }
}