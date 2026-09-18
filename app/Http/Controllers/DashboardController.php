<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductionOrder;
use App\Models\ApprovalRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::check() ? Auth::user()->role : 'user';

        $totalInventory  = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::count() : 0;
        $totalProduction = class_exists('\App\Models\ProductionOrder') ? ProductionOrder::count() : 0;
        $totalResources  = class_exists('\App\Models\ManPower') ? \App\Models\ManPower::where('status', 'Idle')->count() : 0;
        $totalOrders     = class_exists('\App\Models\Order') ? \App\Models\Order::count() : 0;
        $totalRnd        = class_exists('\App\Models\Rnd') ? \App\Models\Rnd::count() : 0;

        $pendingApprovals = []; 
        
        if ($role == 'super_admin') {
            $reqHapusProduksi = ProductionOrder::where('status', 'Menunggu Dihapus')->get();
            foreach ($reqHapusProduksi as $req) {
                $pendingApprovals[] = (object)[
                    'id'          => $req->id,
                    'pemohon'     => 'Admin Produksi',
                    'modul'       => 'Production',
                    'data'        => $req->no_po . ' (' . $req->produk . ')',
                    'alasan'      => 'Hapus SPK Permanen',
                    'url'         => route('produksi.approve_delete', $req->id),
                    'url_approve' => route('produksi.approve_delete', $req->id),
                    'url_reject'  => null 
                ];
            }

            $reqDeleteResource = ApprovalRequest::where('action_type', 'delete')->get();
            foreach ($reqDeleteResource as $req) {
                $pendingApprovals[] = (object)[
                    'id'          => $req->id,
                    'pemohon'     => 'Admin',
                    'modul'       => $req->target_type,
                    'data'        => $req->target_name,
                    'alasan'      => 'Permintaan Hapus ' . $req->target_type,
                    'url'         => route('approval.process', $req->id) . '?action=approve',
                    'url_approve' => route('approval.process', $req->id) . '?action=approve',
                    'url_reject'  => route('approval.process', $req->id) . '?action=reject'
                ];
            }
        } 
        elseif ($role == 'admin') {
            $reqSpkBaru = ProductionOrder::where('status', 'Menunggu Approval Admin')->get();
            foreach ($reqSpkBaru as $req) {
                $pendingApprovals[] = (object)[
                    'id'          => $req->id,
                    'pemohon'     => 'Operator User',
                    'modul'       => 'Production',
                    'data'        => $req->no_po . ' (' . $req->produk . ')',
                    'alasan'      => 'Pembuatan SPK Baru (Target: ' . $req->jumlah_produksi . ' Pcs)',
                    'url'         => route('produksi.approve_spk', $req->id),
                    'url_approve' => route('produksi.approve_spk', $req->id),
                    'url_reject'  => route('produksi.reject_spk', $req->id)
                ];
            }

            $reqResourceUser = ApprovalRequest::whereIn('action_type', ['create', 'update'])
                                              ->whereIn('target_type', ['ManPower', 'MachinePower'])
                                              ->get();
            foreach ($reqResourceUser as $req) {
                $pendingApprovals[] = (object)[
                    'id'          => $req->id,
                    'pemohon'     => 'Operator User',
                    'modul'       => $req->target_type,
                    'data'        => $req->target_name,
                    'alasan'      => 'Pengajuan ' . ucfirst($req->action_type) . ' ' . $req->target_type,
                    'url'         => route('approval.process', $req->id) . '?action=approve',
                    'url_approve' => route('approval.process', $req->id) . '?action=approve',
                    'url_reject'  => route('approval.process', $req->id) . '?action=reject'
                ];
            }
        }

        $pendingCount = count($pendingApprovals);

        $invRaw = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('type', 'raw')->count() : 0;
        $invWip = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('type', 'wip')->count() : 0;
        $invFg  = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('type', 'finished')->count() : 0;

        $prodPending = ProductionOrder::whereIn('status', ['Menunggu Approval Admin', 'Menunggu Bahan Baku', 'Pending - Trouble', 'Menunggu Dihapus'])->count();
        $prodRunning = ProductionOrder::where('status', 'Proses Produksi Berjalan')->count();
        $prodDone    = ProductionOrder::where('status', 'Selesai')->count();

        return view('dashboard', compact(
            'totalInventory', 'totalProduction', 'totalResources', 'totalOrders', 'totalRnd',
            'pendingApprovals', 'pendingCount',
            'invRaw', 'invWip', 'invFg',
            'prodPending', 'prodRunning', 'prodDone'
        ));
    }

    public function handleApproval(Request $request, $id)
    {
        return back()->with('success', 'Status pengajuan berhasil diubah!');
    }
}