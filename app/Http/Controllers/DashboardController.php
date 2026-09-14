<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::check() ? Auth::user()->role : 'user';

        $totalInventory  = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::count() : 0;
        $totalProduction = class_exists('\App\Models\ProductionOrder') ? \App\Models\ProductionOrder::count() : 0;
        $totalResources  = class_exists('\App\Models\Resource') ? \App\Models\Resource::count() : 0;
        $totalOrders     = class_exists('\App\Models\Order') ? \App\Models\Order::count() : 0;
        $totalRnd        = class_exists('\App\Models\Rnd') ? \App\Models\Rnd::count() : 0;

        $pendingApprovals = []; 
        
        if (class_exists('\App\Models\ProductionOrder')) {
            // 1. JIKA YANG LOGIN SUPER ADMIN (Melihat Permintaan Hapus)
            if ($role == 'super_admin') {
                $reqHapusProduksi = \App\Models\ProductionOrder::where('status', 'Menunggu Dihapus')->get();
                foreach ($reqHapusProduksi as $req) {
                    $pendingApprovals[] = (object)[
                        'id'      => $req->id,
                        'pemohon' => 'Admin Produksi',
                        'modul'   => 'Production',
                        'data'    => $req->no_po . ' (' . $req->produk . ')',
                        'alasan'  => 'Hapus SPK Permanen',
                        'url'     => route('produksi.approve_delete', $req->id)
                    ];
                }
            } 
            // 2. JIKA YANG LOGIN ADMIN BIASA (Melihat Permintaan SPK Baru dari User)
            elseif ($role == 'admin') {
                $reqSpkBaru = \App\Models\ProductionOrder::where('status', 'Menunggu Approval Admin')->get();
                foreach ($reqSpkBaru as $req) {
                    $pendingApprovals[] = (object)[
                        'id'      => $req->id,
                        'pemohon' => 'Operator User',
                        'modul'   => 'Production',
                        'data'    => $req->no_po . ' (' . $req->produk . ')',
                        'alasan'  => 'Pembuatan SPK Baru (Target: ' . $req->jumlah_produksi . ' Pcs)',
                        'url_approve' => route('produksi.approve_spk', $req->id),
                        'url_reject'  => route('produksi.reject_spk', $req->id)
                    ];
                }
            }
        }
        $pendingCount = count($pendingApprovals);

        $invRaw = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('type', 'raw')->count() : 0;
        $invWip = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('type', 'wip')->count() : 0;
        $invFg  = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('type', 'finished')->count() : 0;

        $prodPending = class_exists('\App\Models\ProductionOrder') ? \App\Models\ProductionOrder::whereIn('status', ['Menunggu Approval Admin', 'Menunggu Bahan Baku', 'Pending - Trouble', 'Menunggu Dihapus'])->count() : 0;
        $prodRunning = class_exists('\App\Models\ProductionOrder') ? \App\Models\ProductionOrder::where('status', 'Proses Produksi Berjalan')->count() : 0;
        $prodDone    = class_exists('\App\Models\ProductionOrder') ? \App\Models\ProductionOrder::where('status', 'Selesai')->count() : 0;

        return view('dashboard', compact(
            'totalInventory', 'totalProduction', 'totalResources', 'totalOrders', 'totalRnd',
            'pendingApprovals', 'pendingCount',
            'invRaw', 'invWip', 'invFg',
            'prodPending', 'prodRunning', 'prodDone'
        ));
    }

    public function handleApproval(Request $request, $id)
    {
        $action = $request->input('action'); 
        return back()->with('success', 'Status pengajuan berhasil diubah!');
    }
}