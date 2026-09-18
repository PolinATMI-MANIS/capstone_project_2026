<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::check() ? Auth::user()->role : 'user';

        // --- HITUNG TOTAL KARTU INDIKATOR ---
        $totalInventory  = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::count() : 0;
        $totalProduction = class_exists('\App\Models\ProductionOrder') ? \App\Models\ProductionOrder::count() : 0;
        $totalResources  = class_exists('\App\Models\ManPower') ? \App\Models\ManPower::count() : 0;
        $totalOrders     = class_exists('\App\Models\Order') ? \App\Models\Order::count() : 0;
        $totalRnd        = class_exists('\App\Models\RnDfeature') ? \App\Models\RnDfeature::count() : 0;

        // --- PENGUMPULAN PERMINTAAN APPROVAL ---
        $pendingApprovals = []; 

        // 1. APPROVAL UNTUK SUPER ADMIN
        if ($role === 'super_admin') {
            
            // A. Permintaan Hapus dari Production SPK
            if (class_exists('\App\Models\ProductionOrder')) {
                $reqHapusProduksi = \App\Models\ProductionOrder::where('status', 'Menunggu Dihapus')->get();
                foreach ($reqHapusProduksi as $req) {
                    $pendingApprovals[] = (object)[
                        'id'      => $req->id,
                        'pemohon' => 'Admin Produksi',
                        'modul'   => 'Production',
                        'data'    => $req->no_po . ' (' . $req->produk . ')',
                        'alasan'  => 'Hapus SPK Permanen',
                        'url'     => Route::has('produksi.approve_delete') ? route('produksi.approve_delete', $req->id) : '#'
                    ];
                }
            }

            // B. Permintaan Hapus dari ApprovalRequest (Man Power / Resources)
            if (class_exists('\App\Models\ApprovalRequest')) {
                $reqApprovalSystem = \App\Models\ApprovalRequest::where('action_type', 'delete')->get();
                foreach ($reqApprovalSystem as $appReq) {
                    $pendingApprovals[] = (object)[
                        'id'      => $appReq->id,
                        'pemohon' => 'Admin Resources',
                        'modul'   => $appReq->target_type ?? 'Resources',
                        'data'    => $appReq->target_name ?? ('Data #' . $appReq->target_id),
                        'alasan'  => 'Hapus Data ' . ($appReq->target_type ?? 'Resources'),
                        'url'     => Route::has('approval.approve') ? route('approval.approve', $appReq->id) : '#'
                    ];
                }
            }
        } 
        // 2. APPROVAL UNTUK ADMIN BIASA
        elseif ($role === 'admin') {
            if (class_exists('\App\Models\ProductionOrder')) {
                $reqSpkBaru = \App\Models\ProductionOrder::where('status', 'Menunggu Approval Admin')->get();
                foreach ($reqSpkBaru as $req) {
                    $pendingApprovals[] = (object)[
                        'id'          => $req->id,
                        'pemohon'     => 'Operator User',
                        'modul'       => 'Production',
                        'data'        => $req->no_po . ' (' . $req->produk . ')',
                        'alasan'      => 'Pembuatan SPK Baru (Target: ' . $req->jumlah_produksi . ' Pcs)',
                        'url_approve' => Route::has('produksi.approve_spk') ? route('produksi.approve_spk', $req->id) : '#',
                        'url_reject'  => Route::has('produksi.reject_spk') ? route('produksi.reject_spk', $req->id) : '#'
                    ];
                }
            }
        }

        $pendingCount = count($pendingApprovals);

        // --- DATA GRAFIK & STATISTIK ---
        $invRaw = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('type', 'raw')->count() : 0;
        $invWip = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('type', 'wip')->count() : 0;
        $invFg  = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('type', 'finished')->count() : 0;

        $prodPending = class_exists('\App\Models\ProductionOrder') ? \App\Models\ProductionOrder::whereIn('status', ['Menunggu Approval Admin', 'Menunggu Bahan Baku', 'Pending - Trouble', 'Menunggu Dihapus'])->count() : 0;
        $prodRunning = class_exists('\App\Models\ProductionOrder') ? \App\Models\ProductionOrder::where('status', 'Proses Produksi Berjalan')->count() : 0;
        $prodDone    = class_exists('\App\Models\ProductionOrder') ? \App\Models\ProductionOrder::where('status', 'Selesai')->count() : 0;

        $resIdle  = class_exists('\App\Models\ManPower') ? \App\Models\ManPower::where('status', 'Idle')->count() : 0;
        $resKerja = class_exists('\App\Models\ManPower') ? \App\Models\ManPower::where('status', 'Kerja')->count() : 0;
        $resCuti  = class_exists('\App\Models\ManPower') ? \App\Models\ManPower::where('status', 'Cuti')->count() : 0;

        $rndResearch = class_exists('\App\Models\RnDfeature') ? \App\Models\RnDfeature::whereIn('status', ['Konsep Perlu Revisi', 'Ide Dihentikan'])->count() : 0;
        $rndProto    = class_exists('\App\Models\RnDfeature') ? \App\Models\RnDfeature::whereIn('status', ['Desain & Rekayasa', 'Rekayasa Ulang'])->count() : 0;
        $rndTesting  = class_exists('\App\Models\RnDfeature') ? \App\Models\RnDfeature::whereIn('status', ['Pengujian Produk', 'Remedial Uji', 'Peluncuran Produk Baru'])->count() : 0;

        return view('dashboard', compact(
            'totalInventory', 'totalProduction', 'totalResources', 'totalOrders', 'totalRnd',
            'pendingApprovals', 'pendingCount',
            'invRaw', 'invWip', 'invFg',
            'prodPending', 'prodRunning', 'prodDone',
            'resIdle', 'resKerja', 'resCuti',
            'rndResearch', 'rndProto', 'rndTesting'
        ));
    }

    public function handleApproval(Request $request, $id)
    {
        return back()->with('success', 'Status pengajuan berhasil diperbarui!');
    }
}