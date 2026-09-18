<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProduksiController extends Controller
{
    private function getCurrentRole() {
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

        // Mengambil data Operator dan Mesin secara aman dari database resources
        $operators = collect();
        $mesins = collect();

        try {
            if (Schema::hasTable('resources')) {
                $operators = DB::table('resources')->whereIn('type', ['operator', 'Operator', 'SDM', 'User'])->get();
                $mesins    = DB::table('resources')->whereIn('type', ['mesin', 'Machine', 'Mesin', 'Alat'])->get();

                // Jika kolom type tidak spesifik, ambil semua resource sebagai pilihan
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
            // Tetap berjalan meskipun tabel resource belum disiapkan
        }

        return view('produksi.index', compact('orders', 'totalSpk', 'wip', 'selesai', 'role', 'operators', 'mesins'));
    }

    public function store(Request $request)
    {
        $role = $this->getCurrentRole();
        
        $request->validate([
            'no_po' => 'required|string|unique:production_orders,no_po',
            'produk' => 'required|string',
            'jumlah_produksi' => 'required|integer|min:1',
            'target_selesai' => 'required|date',
        ]);

        ProductionOrder::create([
            'no_po' => $request->no_po,
            'produk' => $request->produk,
            'jumlah_produksi' => $request->jumlah_produksi,
            'target_selesai' => $request->target_selesai,
            'keterangan' => $request->keterangan,
            'status' => ($role == 'user') ? 'Menunggu Approval Admin' : 'Menunggu Bahan Baku',
        ]);

        return redirect()->route('produksi.index')->with('success', 'SPK berhasil dibuat!');
    }

    // --- FITUR ADMIN: APPROVE DENGAN PEMILIHAN OPERATOR & MESIN ---
    public function approveSpk(Request $request, $id) {
        $order = ProductionOrder::findOrFail($id);

        $namaMesin = $request->mesin ?? $request->mesin_id ?? 'Mesin Default';
        $namaOperator = $request->operator ?? $request->operator_name ?? 'Operator Default';

        $catatanPlotting = "⚙️ [APPROVED & PLOTTED] Mesin: " . $namaMesin . " | Operator: " . $namaOperator;
        $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatanPlotting : $catatanPlotting;

        $updateData = [
            'status' => 'Proses Produksi Berjalan',
            'keterangan' => $keteranganUpdate
        ];

        // Update kolom operator & mesin jika kolomnya tersedia di tabel database
        if (Schema::hasColumn('production_orders', 'operator')) {
            $updateData['operator'] = $namaOperator;
        }
        if (Schema::hasColumn('production_orders', 'mesin')) {
            $updateData['mesin'] = $namaMesin;
        }

        $order->update($updateData);

        return redirect()->back()->with('success', 'PO Disetujui! Mesin & Operator berhasil di-plotting dan masuk ke proses produksi.');
    }

    public function rejectSpk($id) {
        $order = ProductionOrder::findOrFail($id);
        $order->update(['status' => 'Ditolak Admin']);
        return redirect()->back()->with('warning', 'Order Produksi telah Ditolak.');
    }

    // --- FITUR HAPUS DATA ---
    public function requestDelete($id) {
        $order = ProductionOrder::findOrFail($id);
        $order->update(['status' => 'Menunggu Dihapus']);
        return redirect()->back()->with('success', 'Permintaan Hapus telah dikirim ke Super Admin.');
    }

    public function approveDelete($id) {
        $order = ProductionOrder::findOrFail($id);
        $order->delete();
        return redirect()->back()->with('success', 'Data SPK telah Dihapus secara permanen oleh Super Admin.');
    }

    // --- FITUR OPERASIONAL (EKSEKUSI RESOURCES) ---
    public function submitMaterialRequest(Request $request, $id) {
        $order = ProductionOrder::findOrFail($id);

        $namaMesin = $request->mesin_id ?? 'Mesin Default';
        $namaOperator = $request->operator_name ?? 'Operator Default';

        $catatanPlotting = "⚙️ [EKSEKUSI] Mesin: " . $namaMesin . " | Operator: " . $namaOperator;
        $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatanPlotting : $catatanPlotting;

        $order->update([
            'status' => 'Proses Produksi Berjalan', 
            'keterangan' => $keteranganUpdate
        ]);
        
        return redirect()->back()->with('success', 'SPK sukses masuk antrean produksi dengan Mesin & Operator terpilih!');
    }

    public function submitFinalQc(Request $request, $id) {
        $order = ProductionOrder::findOrFail($id);
        $catatanQC = "✅ Final QC [Good: " . $request->good_qty . " Pcs | Reject: " . $request->reject_qty . " Pcs]. " . $request->catatan_qc;
        $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatanQC : $catatanQC;

        $order->update(['status' => 'Selesai', 'keterangan' => $keteranganUpdate]);
        return redirect()->back()->with('success', 'Laporan Final QC berhasil disimpan!');
    }

    public function reportTrouble(Request $request, $id) {
        $order = ProductionOrder::findOrFail($id);
        $catatan = "⚠️ [DOWNTIME] " . now()->format('d M H:i') . " - " . $request->alasan_trouble;
        $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatan : $catatan;

        $order->update(['status' => 'Pending - Trouble', 'keterangan' => $keteranganUpdate]);
        return redirect()->back()->with('warning', 'Sistem Andon aktif! SPK dihentikan sementara.');
    }

    public function resolveTrouble($id) {
        $order = ProductionOrder::findOrFail($id);
        $catatan = "🛠️ [RESOLVED] " . now()->format('d M H:i') . " - Produksi dilanjutkan kembali.";
        $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatan : $catatan;

        $order->update(['status' => 'Proses Produksi Berjalan', 'keterangan' => $keteranganUpdate]);
        return redirect()->back()->with('success', 'Masalah diselesaikan. Mesin kembali beroperasi.');
    }
}