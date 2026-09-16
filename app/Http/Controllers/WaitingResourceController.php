<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionOrder;
use App\Models\ManPower;

class WaitingResourceController extends Controller
{
    public function index()
    {
        $rawList = collect();
        if (class_exists('\App\Models\ProductionOrder')) {
            $rawList = ProductionOrder::whereIn('status', ['Menunggu Bahan Baku', 'Proses Produksi Berjalan', 'ready'])->latest()->get();
        }

        $waitingList = $rawList->map(function($item) {
            return (object) [
                'id'              => $item->id,
                'production_code' => $item->no_po,
                'product_name'    => $item->produk,
                'quantity'        => $item->jumlah_produksi,
                'status'          => $item->status,
                'notes'           => $item->keterangan
            ];
        });

        return view('waiting-resources.index', compact('waitingList'));
    }

    public function updateStatus(Request $request, $id)
    {
        $item = ProductionOrder::findOrFail($id);
        
        $isReady = $request->status === 'ready';
        $item->status = $isReady ? 'ready' : 'Menunggu Bahan Baku';
        $item->save();

        if (!$isReady) {
            // 1. Ambil ID pekerja yang ada di SPK ini
            $workerIds = \App\Models\ManPower::where('production_order_id', $id)->pluck('id');

            // 2. Kembalikan status mesin yang dipakai pekerja tersebut menjadi 'idle' (DAN lepaskan relasinya)
            \App\Models\MachinePower::whereIn('man_power_id', $workerIds)->update([
                'status' => 'idle',          // <--- INI KUNCI UTAMANYA AGAR TIDAK TERHITUNG RUNNING
                'man_power_id' => null
            ]);

            // 3. Kembalikan pekerja ke Idle dan lepaskan dari SPK
            \App\Models\ManPower::where('production_order_id', $id)->update([
                'status' => 'Idle',
                'production_order_id' => null
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $item = ProductionOrder::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data antrean produksi berhasil dihapus.'
        ]);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'production_code' => 'required|string',
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        if (class_exists('\App\Models\ProductionOrder')) {
            $waiting = ProductionOrder::create([
                'no_po'           => $validated['production_code'],
                'produk'          => $validated['product_name'],
                'jumlah_produksi' => $validated['quantity'],
                'keterangan'      => $validated['notes'],
                'target_selesai'  => now()->addDays(7),
                'status'          => 'Menunggu Bahan Baku'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data produksi berhasil disinkronkan ke Resources!',
                'data' => $waiting
            ], 201);
        }

        return response()->json(['success' => false, 'message' => 'Tabel Produksi belum tersedia.'], 500);
    }
    
}