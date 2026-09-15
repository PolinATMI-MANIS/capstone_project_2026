<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; // Wajib dipanggil untuk fitur Email

class ProduksiController extends Controller
{
    // Mengambil role asli dari user yang sedang login dari database teman Anda
    private function getCurrentRole() {
        if (Auth::check()) {
            return Auth::user()->role; // Mengambil 'super_admin', 'admin', atau 'user'
        }
        return 'user'; // Default jika belum login
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

        $operators = [
            (object) ['id' => 1, 'nama_pekerja' => 'Budi Santoso', 'posisi' => 'Operator CNC'],
            (object) ['id' => 2, 'nama_pekerja' => 'Ahmad Faisal', 'posisi' => 'Welder'],
            (object) ['id' => 3, 'nama_pekerja' => 'Siti Aminah', 'posisi' => 'Quality Control']
        ];

        return view('produksi.index', compact('orders', 'totalSpk', 'wip', 'selesai', 'operators', 'role'));
    }

    public function store(Request $request)
    {
        $role = $this->getCurrentRole();
        
        $request->validate([
            'no_po' => 'required|string|unique:production_orders,no_po',
            'produk' => 'required|string',
            'jumlah_produksi' => 'required|integer|min:1',
            'target_selesai' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        // LOGIKA ROLE SAAT INPUT:
        // Jika User biasa yang buat, harus tunggu admin. Jika Admin yg buat, langsung siap produksi.
        $statusAwal = ($role == 'user') ? 'Menunggu Approval Admin' : 'Menunggu Bahan Baku';

        ProductionOrder::create([
            'no_po' => $request->no_po,
            'produk' => $request->produk,
            'jumlah_produksi' => $request->jumlah_produksi,
            'target_selesai' => $request->target_selesai,
            'keterangan' => $request->keterangan,
            'status' => $statusAwal,
        ]);

        return redirect()->route('produksi.index')->with('success', 'SPK berhasil dibuat!');
    }

    // --- FITUR ADMIN: APPROVE & REJECT + EMAIL NOTIFICATION ---
    public function approveSpk($id) {
        $order = ProductionOrder::findOrFail($id);
        $order->update(['status' => 'Menunggu Bahan Baku']);

        // Mengirim Email Notifikasi (Approve)
        try {
            Mail::raw("Halo, SPK dengan No. PO: {$order->no_po} (Produk: {$order->produk}) telah DISETUJUI oleh Admin. SPK sekarang masuk ke antrean Menunggu Bahan Baku.", function ($message) use ($order) {
                // Email tujuan sementara di-hardcode ke user dummy (bisa diubah nanti)
                $message->to('user@capstone.com')->subject('✅ SPK Disetujui: ' . $order->no_po);
            });
        } catch (\Exception $e) {
            // Pengaman: Jika konfigurasi SMTP di .env belum disetting, abaikan error agar web tidak crash
        }

        return redirect()->back()->with('success', 'SPK dari User Disetujui. Notifikasi Email telah dikirim (jika SMTP aktif).');
    }

    public function rejectSpk($id) {
        $order = ProductionOrder::findOrFail($id);
        $order->update(['status' => 'Ditolak Admin']);

        // Mengirim Email Notifikasi (Reject)
        try {
            Mail::raw("Mohon maaf, SPK dengan No. PO: {$order->no_po} (Produk: {$order->produk}) telah DITOLAK oleh Admin. Silakan hubungi tim Admin untuk informasi lebih lanjut.", function ($message) use ($order) {
                $message->to('user@capstone.com')->subject('❌ SPK Ditolak: ' . $order->no_po);
            });
        } catch (\Exception $e) {
             // Pengaman error
        }

        return redirect()->back()->with('warning', 'SPK dari User telah Ditolak. Notifikasi Email telah dikirim.');
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

    // --- FITUR OPERASIONAL (ADMIN) ---
    public function submitMaterialRequest(Request $request, $id) {
        $order = ProductionOrder::findOrFail($id);
        $isFulfilled = $request->is_material_ready && $request->is_machine_ready;

        if (!$isFulfilled) {
            $order->update(['status' => 'Ditolak / Bahan Kurang']);
            return redirect()->back()->with('warning', 'Kelayakan gagal! Inventory/mesin tidak siap.');
        }

        $catatanOperator = "Operator Bertugas: " . $request->operator_name;
        $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatanOperator : $catatanOperator;

        $order->update(['status' => 'Proses Produksi Berjalan', 'keterangan' => $keteranganUpdate]);
        return redirect()->back()->with('success', 'SPK masuk ke proses produksi.');
    }

    public function submitFinalQc(Request $request, $id) {
        $order = ProductionOrder::findOrFail($id);
        $catatanQC = "Final QC [Good: " . $request->good_qty . " Pcs | Reject: " . $request->reject_qty . " Pcs]. " . $request->catatan_qc;
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
        $catatan = "✅ [RESOLVED] " . now()->format('d M H:i') . " - Produksi dilanjutkan kembali.";
        $keteranganUpdate = $order->keterangan ? $order->keterangan . "\n" . $catatan : $catatan;

        $order->update(['status' => 'Proses Produksi Berjalan', 'keterangan' => $keteranganUpdate]);
        return redirect()->back()->with('success', 'Masalah diselesaikan. Mesin kembali beroperasi.');
    }
}