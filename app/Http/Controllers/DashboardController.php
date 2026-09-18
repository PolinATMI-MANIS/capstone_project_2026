<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\InventoryPo;
use App\Models\Supplier;
use App\Models\Resource;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Rnd;
use App\Models\ProductionOrder;
use App\Models\ApprovalRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::check() ? Auth::user()->role : 'user';

        $hasInventory       = Schema::hasTable('inventories');
        $hasProductionOrder = Schema::hasTable('production_orders');
        $hasResource        = Schema::hasTable('resources');
        $hasOrder           = Schema::hasTable('orders');
        $hasRnd             = Schema::hasTable('rnds');
        $hasApprovalReq     = Schema::hasTable('approval_requests');

        $totalInventory  = $hasInventory ? Inventory::count() : Item::count();
        $totalProduction = $hasProductionOrder ? ProductionOrder::count() : ItemRequest::where('department', 'LIKE', '%produksi%')->count();
        $totalResources  = class_exists('\App\Models\ManPower') ? \App\Models\ManPower::where('status', 'Idle')->count() : ($hasResource ? Resource::count() : Supplier::count());
        $totalOrders     = $hasOrder ? Order::count() : InventoryPo::count();
        $totalRnd        = $hasRnd ? Rnd::count() : 0;

        $pendingApprovals = [];

        if ($role == 'super_admin' || $role == 'superadmin') {
            if ($hasProductionOrder) {
                $reqHapusProduksi = ProductionOrder::where('status', 'Menunggu Dihapus')->get();
                foreach ($reqHapusProduksi as $req) {
                    $pendingApprovals[] = (object)[
                        'id'          => $req->id,
                        'pemohon'     => 'Admin Produksi',
                        'modul'       => 'Production',
                        'data'        => $req->no_po . ' (' . $req->produk . ')',
                        'alasan'      => 'Hapus SPK Permanen',
                        'url'         => Route::has('produksi.approve_delete') ? route('produksi.approve_delete', $req->id) : '#',
                        'url_approve' => Route::has('produksi.approve_delete') ? route('produksi.approve_delete', $req->id) : '#',
                        'url_reject'  => null
                    ];
                }
            }

            if ($hasApprovalReq) {
                $reqDeleteResource = ApprovalRequest::where('action_type', 'delete')->get();
                foreach ($reqDeleteResource as $req) {
                    $pendingApprovals[] = (object)[
                        'id'          => $req->id,
                        'pemohon'     => 'Admin',
                        'modul'       => $req->target_type,
                        'data'        => $req->target_name,
                        'alasan'      => 'Permintaan Hapus ' . $req->target_type,
                        'url'         => Route::has('approval.process') ? route('approval.process', $req->id) . '?action=approve' : '#',
                        'url_approve' => Route::has('approval.process') ? route('approval.process', $req->id) . '?action=approve' : '#',
                        'url_reject'  => Route::has('approval.process') ? route('approval.process', $req->id) . '?action=reject' : '#'
                    ];
                }
            }
        } elseif ($role == 'admin') {
            if ($hasProductionOrder) {
                $reqSpkBaru = ProductionOrder::where('status', 'Menunggu Approval Admin')->get();
                foreach ($reqSpkBaru as $req) {
                    $pendingApprovals[] = (object)[
                        'id'          => $req->id,
                        'pemohon'     => 'Operator User',
                        'modul'       => 'Production',
                        'data'        => $req->no_po . ' (' . $req->produk . ')',
                        'alasan'      => 'Pembuatan SPK Baru (Target: ' . $req->jumlah_produksi . ' Pcs)',
                        'url'         => Route::has('produksi.approve_spk') ? route('produksi.approve_spk', $req->id) : '#',
                        'url_approve' => Route::has('produksi.approve_spk') ? route('produksi.approve_spk', $req->id) : '#',
                        'url_reject'  => Route::has('produksi.reject_spk') ? route('produksi.reject_spk', $req->id) : '#'
                    ];
                }
            }

            if ($hasApprovalReq) {
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
                        'url'         => Route::has('approval.process') ? route('approval.process', $req->id) . '?action=approve' : '#',
                        'url_approve' => Route::has('approval.process') ? route('approval.process', $req->id) . '?action=approve' : '#',
                        'url_reject'  => Route::has('approval.process') ? route('approval.process', $req->id) . '?action=reject' : '#'
                    ];
                }
            }
        }

        $pendingCount = count($pendingApprovals);

        $invRaw = $hasInventory
            ? Inventory::where('type', 'raw')->count()
            : Item::where('category', 'Raw Material')->orWhere('category', 'LIKE', '%raw%')->count();

        $invWip = $hasInventory
            ? Inventory::where('type', 'wip')->count()
            : Item::where('category', 'Work In Process')->orWhere('category', 'LIKE', '%wip%')->count();

        $invFg = $hasInventory
            ? Inventory::where('type', 'finished')->count()
            : Item::where('category', 'Finished Goods')->orWhere('category', 'LIKE', '%finished%')->count();

        $invMro = Item::where('category', 'MRO / Sparepart')
                    ->orWhere('category', 'LIKE', '%mro%')
                    ->orWhere('category', 'LIKE', '%sparepart%')->count();

        $invPacking = Item::where('category', 'Packing Material')
                    ->orWhere('category', 'LIKE', '%packing%')->count();

        $prodPending = $hasProductionOrder
            ? ProductionOrder::whereIn('status', ['Menunggu Approval Admin', 'Menunggu Bahan Baku', 'Pending - Trouble', 'Menunggu Dihapus'])->count()
            : ItemRequest::where('status', 'LIKE', '%Pending%')->count();

        $prodRunning = $hasProductionOrder
            ? ProductionOrder::where('status', 'Proses Produksi Berjalan')->count()
            : 0;

        $prodDone = $hasProductionOrder
            ? ProductionOrder::where('status', 'Selesai')->count()
            : ItemRequest::where('status', 'LIKE', '%Approved%')->count();

        $prodApproved = $hasProductionOrder
            ? ProductionOrder::where('status', 'Approved')->count()
            : ItemRequest::where('status', 'LIKE', '%Approved%')->count();

        $prodRejected = $hasProductionOrder
            ? ProductionOrder::where('status', 'Rejected')->count()
            : ItemRequest::where('status', 'LIKE', '%Rejected%')->count();

        $resourceActive = Supplier::count();
        $poCount        = InventoryPo::count();

        return view('dashboard', compact(
            'role',
            'totalInventory',
            'totalProduction',
            'totalResources',
            'totalOrders',
            'totalRnd',
            'pendingApprovals',
            'pendingCount',
            'invRaw',
            'invWip',
            'invFg',
            'invMro',
            'invPacking',
            'prodPending',
            'prodRunning',
            'prodDone',
            'prodApproved',
            'prodRejected',
            'resourceActive',
            'poCount'
        ));
    }

    public function handleApproval(Request $request, $id)
    {
        if ($request->has('action') && Schema::hasTable('item_requests')) {
            $request->validate([
                'action' => 'required|in:approve,reject',
            ]);

            $itemRequest = ItemRequest::find($id);

            if ($itemRequest) {
                $newStatus = ($request->input('action') === 'approve') ? 'Approved by Admin' : 'Rejected';
                $itemRequest->update([
                    'status' => $newStatus
                ]);
            }
        }

        return back()->with('success', 'Status pengajuan berhasil diubah!');
    }
}