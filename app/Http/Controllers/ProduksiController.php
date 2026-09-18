<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionOrder;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ProduksiController extends Controller
{
    private function getCurrentRole() 
    {
        if (Auth::check()) {
            return Auth::user()->role; 
        }
        return 'user'; 
    }

    public function index(Request $request)
    {
        $role = $this->getCurrentRole();
        $query = ProductionOrder::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('no_po', 'like', '%' . $request->search . '%')
                  ->orWhere('produk', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(7);
        $totalSpk = ProductionOrder::count();
        $wip = ProductionOrder::where('status', 'Proses Produksi Berjalan')->count();
        $selesai = ProductionOrder::where('status', 'Selesai')->count();

        $inventories = Item::orderBy('name', 'asc')->get();

        // Mengambil data Operator dan Mesin secara aman dari database resources atau fallback ke data statis
        $operators = collect();
        $mesins = collect();

        try {
            if (Schema::hasTable('resources')) {
                $operators = DB::table('resources')->whereIn('type', ['operator', 'Operator', 'SDM', 'User'])->get();
                $mesins    = DB::table('resources')->whereIn('type', ['mesin', 'Machine', 'Mesin', 'Alat'])->get();

                if ($operators->isEmpty() && $mesins->isEmpty()) {
                    $allResources = DB::table('resources')->get();
                    $operators = $allResources;
                    $mesins    = $allResources;
                }
            } elseif (Schema::hasTable('operators') && Schema::hasTable('mesins')) {
                $operators = DB::table('operators')->get();
                $mesins    = DB::table('mesins')->get();
            }
        } catch (\Exception $e) {
            // Fallback jika tabel resources belum ada
        }

        // Fallback operator default jika database kosong
        if ($operators->isEmpty()) {
            $operators = collect([
                (object) ['id' => 1, 'nama_pekerja' => 'Budi Santoso', 'posisi' => 'Operator CNC'],
                (object) ['id' => 2, 'nama_pekerja' => 'Ahmad Faisal', 'posisi' => 'Welder'],
                (object) ['id' => 3, 'nama_pekerja' => 'Siti Aminah', 'posisi' => 'Quality Control']
            ]);
        }

        return view('produksi.index', compact('orders', 'totalSpk', 'wip', 'selesai', 'operators', 'mesins', 'role', 'inventories'));
    }

    public function store(Request $request)
    {
        $role = $this->getCurrentRole();
        
        $request->validate([
            'no_po' => 'required|string|unique:production_orders,no_po',
            'inventory_id' => 'required|exists:items,id',
            'jumlah_produksi' => 'required|integer|min:1',
            'target_selesai' => 'required|date',
        ]);

        $inventory = Item::findOrFail($request->inventory_id);
        $statusAwal = ($role == 'user') ? 'Menunggu Approval Admin' : 'Menunggu Bahan Baku';

        ProductionOrder::create([
            'no_po' => $request->no_po,
            'inventory_id' => $inventory->id,
            'produk' => $inventory->name, 
            'jumlah_produksi' => $request->jumlah_produksi,
            'target_selesai' => $request->target_selesai,
            'keterangan' => $request->keterangan,
            'status' => $statusAwal,
        ]);

        return redirect()->route('produksi.index')->with('success', 'SPK berhasil dibuat dan terhubung ke Inventory!');
    }

    public function approveSpk(Request $request, $id) 
    {
        $order = ProductionOrder::findOrFail($id);

        $namaMesin = $request->mesin ?? $request->mesin_id ?? 'Mesin Default';
        $namaOperator = $request->operator ?? $request->operator_name ?? 'Operator Default';

        $catatanPlotting = "⚙️ [APPROVED & PLOTTED] Mesin: " . $namaMesin . " | Operator: " . $namaOperator;
        $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatanPlotting : $catatanPlotting;

        $updateData = [
            'status' => 'Menunggu Bahan Baku',
            'keterangan' => $keteranganUpdate
        ];

        if (Schema::hasColumn('production_orders', 'operator')) {
            $updateData['operator'] = $namaOperator;
        }
        if (Schema::hasColumn('production_orders', 'mesin')) {
            $updateData['mesin'] = $namaMesin;
        }

        $order->update($updateData);

        try {
            Mail::raw("Halo, SPK dengan No. PO: {$order->no_po} (Produk: {$order->produk}) telah DISETUJUI oleh Admin. SPK sekarang masuk ke antrean Menunggu Bahan Baku.", function ($message) use ($order) {
                $message->to('user@capstone.com')->subject('✅ SPK Disetujui: ' . $order->no_po);
            });
        } catch (\Exception $e) {}

        return redirect()->back()->with('success', 'SPK disetujui dan masuk ke tahap Menunggu Bahan Baku.');
    }

    public function rejectSpk($id) 
    {
        $order = ProductionOrder::findOrFail($id);
        $order->update(['status' => 'Ditolak Admin']);

        try {
            Mail::raw("Mohon maaf, SPK dengan No. PO: {$order->no_po} (Produk: {$order->produk}) telah DITOLAK oleh Admin.", function ($message) use ($order) {
                $message->to('user@capstone.com')->subject('❌ SPK Ditolak: ' . $order->no_po);
            });
        } catch (\Exception $e) {}

        return redirect()->back()->with('warning', 'SPK telah Ditolak.');
    }

    public function requestDelete($id) 
    {
        $order = ProductionOrder::findOrFail($id);
        $order->update(['status' => 'Menunggu Dihapus']);
        
        return redirect()->back()->with('success', 'Permintaan Hapus telah dikirim ke Super Admin.');
    }

    public function approveDelete($id) 
    {
        $order = ProductionOrder::findOrFail($id);
        $order->delete();
        
        return redirect()->back()->with('success', 'Data SPK telah Dihapus secara permanen oleh Super Admin.');
    }

    public function submitMaterialRequest(Request $request, $id) 
    {
        DB::beginTransaction();
        try {
            $order = ProductionOrder::findOrFail($id);
            $isFulfilled = ($request->has('is_material_ready') ? $request->is_material_ready : 1) && 
                           ($request->has('is_machine_ready') ? $request->is_machine_ready : 1);

            if (!$isFulfilled) {
                $order->update(['status' => 'Ditolak / Bahan Kurang']);
                DB::commit();
                return redirect()->back()->with('warning', 'Kelayakan gagal! Inventory/mesin tidak siap.');
            }

            // Kurangi stok inventory jika item terkait ditemukan
            if ($order->inventory_id) {
                $inventoryItem = Item::find($order->inventory_id);

                if ($inventoryItem) {
                    $stokTersedia = $inventoryItem->stock ?? $inventoryItem->stok ?? 0;
                    if ($stokTersedia < $order->jumlah_produksi) {
                        throw new \Exception("Stok barang '{$inventoryItem->name}' tidak mencukupi! Sisa stok saat ini: {$stokTersedia}");
                    }

                    if (isset($inventoryItem->stock)) {
                        $inventoryItem->stock -= $order->jumlah_produksi;
                    } else {
                        $inventoryItem->stok -= $order->jumlah_produksi;
                    }
                    $inventoryItem->save();
                }
            }

            $namaMesin = $request->mesin_id ?? 'Mesin Default';
            $namaOperator = $request->operator_name ?? 'Operator Bertugas';
            $catatanOperator = "⚙️ [EKSEKUSI] Mesin: {$namaMesin} | Operator: {$namaOperator}";
            $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatanOperator : $catatanOperator;

            $order->update([
                'status' => 'Proses Produksi Berjalan', 
                'keterangan' => $keteranganUpdate
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'SPK masuk ke proses produksi dan stok inventory berhasil disesuaikan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function submitFinalQc(Request $request, $id) 
    {
        $order = ProductionOrder::findOrFail($id);
        $catatanQC = "✅ Final QC [Good: " . $request->good_qty . " Pcs | Reject: " . $request->reject_qty . " Pcs]. " . ($request->catatan_qc ?? '');
        $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatanQC : $catatanQC;

        $order->update(['status' => 'Selesai', 'keterangan' => $keteranganUpdate]);
        return redirect()->back()->with('success', 'Laporan Final QC berhasil disimpan!');
    }

    public function reportTrouble(Request $request, $id) 
    {
        $order = ProductionOrder::findOrFail($id);
        $catatan = "⚠️ [DOWNTIME] " . now()->format('d M H:i') . " - " . $request->alasan_trouble;
        $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatan : $catatan;

        $order->update(['status' => 'Pending - Trouble', 'keterangan' => $keteranganUpdate]);
        return redirect()->back()->with('warning', 'Sistem Andon aktif! SPK dihentikan sementara.');
    }

    public function resolveTrouble($id) 
    {
        $order = ProductionOrder::findOrFail($id);
        $catatan = "🛠️ [RESOLVED] " . now()->format('d M H:i') . " - Produksi dilanjutkan kembali.";
        $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatan : $catatan;

        $order->update(['status' => 'Proses Produksi Berjalan', 'keterangan' => $keteranganUpdate]);
        return redirect()->back()->with('success', 'Masalah diselesaikan. Mesin kembali beroperasi.');
    }

    // --- FUNGSI BARU: APPROVE DAN KIRIM KE RESOURCES ---
    public function sendToResources($id) 
    {
        $order = ProductionOrder::findOrFail($id);
        
        $catatan = "➡️ [FORWARDED] " . now()->format('d M H:i') . " - Order diteruskan ke modul Resources.";
        $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatan : $catatan;

        // Ubah status ke "Waiting for Process"
        $order->update([
            'status' => 'Waiting for Process', 
            'keterangan' => $keteranganUpdate
        ]);

        return redirect()->back()->with('success', 'Order berhasil di-approve dan dipindahkan ke antrean Resources (Waiting for Process).');
    }
}