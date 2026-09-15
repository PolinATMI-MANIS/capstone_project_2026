<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryOrder;
use App\Models\Purchase;
use Carbon\Carbon; // Jangan lupa tambahkan Carbon

class DeliveryOrderController extends Controller
{
    public function index()
    {
        // Ambil data Delivery beserta relasi data Purchase-nya
        $deliveries = DeliveryOrder::with('purchase')->latest()->get();
        
        // Ambil PO yang belum dibatalkan untuk pilihan di modal (jika mau input manual)
        $purchases = Purchase::where('status', '!=', 'Cancelled')->get();

        return view('delivery', compact('deliveries', 'purchases'));
    }

    public function store(Request $request)
    {
        // Restriksi Simpan (Hanya Admin & Superadmin)
        if (!in_array(auth()->user()->role ?? 'user', ['admin', 'superadmin'])) {
            return back()->with('error', 'Akses Ditolak! Hanya Admin yang dapat menambah Delivery Order.');
        }

        $request->validate([
            'ref_po' => 'required',
            'tanggal_kirim' => 'required|date',
            'driver' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        // Generate No DO otomatis yang lebih aman
        $currentMonth = Carbon::now()->format('Ym');
        $lastDo = DeliveryOrder::where('no_do', 'LIKE', 'DO-' . $currentMonth . '-%')
                               ->orderBy('no_do', 'desc')
                               ->first();

        $newNumber = $lastDo ? ((int) substr($lastDo->no_do, -3)) + 1 : 1;
        $noDo = 'DO-' . $currentMonth . '-' . sprintf('%03d', $newNumber);

        // Cari data PO berdasarkan ref_po
        $po = Purchase::where('no_po', $request->ref_po)->first();

        // Cek Otomatis: Apakah tanggal kirim melewati deadline PO?
        $status = 'On Progress';
        if ($po && $po->waktu_tgl_deadline) {
            $deadline = Carbon::parse($po->waktu_tgl_deadline)->format('Y-m-d');
            if ($request->tanggal_kirim > $deadline) {
                $status = 'Delayed'; 
            }
        }

        // Simpan Data Delivery
        DeliveryOrder::create([
            'no_do' => $noDo,
            'ref_po' => $request->ref_po,
            'tanggal_kirim' => $request->tanggal_kirim,
            'driver' => $request->driver ?? '-',
            'status' => $request->status ?? $status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect('/delivery')->with('success', 'Delivery Order berhasil dibuat!');
    }

    public function show($id)
    {
        // Ambil data delivery beserta relasi purchase-nya
        $delivery = \App\Models\DeliveryOrder::with('purchase')->findOrFail($id);

        return view('delivery-detail', compact('delivery'));
    }
}