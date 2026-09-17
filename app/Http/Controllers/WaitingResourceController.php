<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionOrder;
use App\Models\ManPower;
use App\Models\MachinePower;

class WaitingResourceController extends Controller
{
    public function index()
    {
        $rawList = collect();
        if (class_exists(ProductionOrder::class)) {
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
        try {
            $item = ProductionOrder::findOrFail($id);
            
            $statusInput = $request->input('status') ?? $request->json('status') ?? 'Menunggu Bahan Baku';
            $lowerStatus = strtolower(trim($statusInput));
            
            if (in_array($lowerStatus, ['ready', 'ready_to_process', 'siap'])) {
                $newStatus = 'ready';
            } elseif (in_array($lowerStatus, ['running', 'proses', 'proses produksi berjalan'])) {
                $newStatus = 'Proses Produksi Berjalan';
            } else {
                $newStatus = 'Menunggu Bahan Baku';
            }

            $item->status = $newStatus;
            $item->save();

            // KUNCI UTAMA SINKRONISASI REAL-TIME:
            // JIKA SPK DI-RETURN KE "MENUNGGU BAHAN BAKU" (Keluar dari Ready/Work Zone)
            if ($newStatus !== 'ready' && $newStatus !== 'Proses Produksi Berjalan') {
                
                // 1. Ambil semua pekerja yang statusnya sedang 'Kerja' tapi tidak terikat aktif, 
                // atau lepaskan semua worker yang statusnya dikembalikan ke Idle
                $workers = ManPower::where('status', 'Kerja')->get();
                
                foreach ($workers as $worker) {
                    // Cari mesin apa yang sedang dipegang oleh worker ini
                    $machine = MachinePower::where('man_power_id', $worker->id)->first();
                    
                    if ($machine) {
                        // Paksa mesin balik ke Standby dan putuskan relasinya
                        $machine->update([
                            'status'       => 'Standby',
                            'man_power_id' => null,
                            'start_time'   => null
                        ]);
                    }

                    // Kembalikan pekerja ke status Idle
                    $worker->update(['status' => 'Idle']);
                }
            }

            return response()->json([
                'success' => true, 
                'message' => 'Status berhasil diperbarui ke ' . $newStatus,
                'status'  => $newStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = ProductionOrder::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data antrean produksi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}