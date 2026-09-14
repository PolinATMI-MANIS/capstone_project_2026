<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ApprovalRequest;
use App\Models\ManPower;
use App\Models\MachinePower;
use Illuminate\Support\Facades\Storage;

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
        $totalInventory  = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::count() : 0;
        $totalProduction = class_exists('\App\Models\Production') ? \App\Models\Production::count() : 0;
        
        // Menghitung total data dari model ManPower untuk kartu statistik Resources
        $totalResources  = ManPower::count(); 
        
        $totalOrders     = class_exists('\App\Models\Order') ? \App\Models\Order::count() : 0;
        $totalRnd        = class_exists('\App\Models\Rnd') ? \App\Models\Rnd::count() : 0;

        $approvalRequests = ApprovalRequest::with('user')->latest()->get();
        $pendingCount     = $approvalRequests->count();

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
        $prodPending = class_exists('\App\Models\Production') ? \App\Models\Production::where('status', 'pending')->count() : 0;

        return view('dashboard', compact(
            'totalInventory', 'totalProduction', 'totalResources', 'totalOrders', 'totalRnd',
            'approvalRequests', 'pendingCount', 'invRaw', 'invWip', 'invFg', 'prodPending'
        ));
    }

    public function handleApproval(Request $request, $id)
    {
        $action = $request->input('action'); 
        return back()->with('success', 'Status pengajuan berhasil diubah!');
        $user = auth()->user();
        // Super Admin dan Admin berhak menyetujui request
        if (!in_array($user->role, ['super_admin', 'admin'])) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $action = $request->input('action'); // 'approve' atau 'reject'
        $approval = ApprovalRequest::findOrFail($id);

        if ($action === 'approve') {
            // 1. APPROVAL UNTUK MAN POWER
            if ($approval->target_type === 'ManPower') {
                if ($approval->action_type === 'create') {
                    $data = json_decode($approval->payload, true);
                    ManPower::create($data);
                } elseif ($approval->action_type === 'update') {
                    $target = ManPower::find($approval->target_id);
                    if ($target) {
                        $data = json_decode($approval->payload, true);
                        $target->update($data);
                    }
                } elseif ($approval->action_type === 'delete') {
                    $target = ManPower::find($approval->target_id);
                    if ($target) {
                        MachinePower::where('man_power_id', $target->id)->update(['status' => 'Standby', 'man_power_id' => null]);
                        if ($target->foto && Storage::disk('public')->exists($target->foto)) {
                            Storage::disk('public')->delete($target->foto);
                        }
                        $target->delete();
                    }
                }
            } 
            // 2. APPROVAL UNTUK MACHINE POWER
            elseif ($approval->target_type === 'MachinePower') {
                if ($approval->action_type === 'create') {
                    $data = json_decode($approval->payload, true);
                    MachinePower::create($data);
                } elseif ($approval->action_type === 'update') {
                    $target = MachinePower::find($approval->target_id);
                    if ($target) {
                        $data = json_decode($approval->payload, true);
                        $target->update($data);
                    }
                } elseif ($approval->action_type === 'delete') {
                    $target = MachinePower::find($approval->target_id);
                    if ($target) {
                        $target->delete();
                    }
                }
            }

            $approval->delete();
            return back()->with('success', 'Permintaan berhasil disetujui dan diterapkan.');
        } else {
            $approval->delete();
            return back()->with('info', 'Permintaan ditolak.');
        }
    }
}