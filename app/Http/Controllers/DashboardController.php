<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\InventoryPo;
use App\Models\Supplier;
use App\Models\Resource;  
use App\Models\Inventory; 
use App\Models\Order;     
use App\Models\Rnd;
use App\Models\ProductionOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::check() ? Auth::user()->role : 'user';

        // Pengecekan fisik tabel di MySQL untuk mencegah QueryException (Table not found)
        $hasInventory       = Schema::hasTable('inventories');
        $hasProductionOrder = Schema::hasTable('production_orders');
        $hasResource        = Schema::hasTable('resources');
        $hasOrder           = Schema::hasTable('orders');
        $hasRnd             = Schema::hasTable('rnds');

        // 1. Total & Data Utama
        $totalInventory  = $hasInventory ? Inventory::count() : Item::count();
        $totalProduction = $hasProductionOrder ? ProductionOrder::count() : ItemRequest::where('department', 'LIKE', '%produksi%')->count();
        $totalResources  = $hasResource ? Resource::count() : Supplier::count(); 
        $totalOrders     = $hasOrder ? Order::count() : InventoryPo::count();
        $totalRnd        = $hasRnd ? Rnd::count() : 0; 

        // 2. Pending Approvals & Logic Role
        $pendingApprovals = []; 
        
        if ($hasProductionOrder) {
            if ($role == 'super_admin') {
                $reqHapusProduksi = ProductionOrder::where('status', 'Menunggu Dihapus')->get();
                foreach ($reqHapusProduksi as $req) {
                    $pendingApprovals[] = (object)[
                        'id'        => $req->id,
                        'user_name' => 'Admin Produksi',
                        'module'    => 'Production',
                        'item_name' => $req->no_po . ' (' . $req->produk . ')',
                        'reason'    => 'Hapus SPK Permanen',
                        'url'       => Route::has('produksi.approve_delete') ? route('produksi.approve_delete', $req->id) : '#'
                    ];
                }
            } 
            elseif ($role == 'admin') {
                $reqSpkBaru = ProductionOrder::where('status', 'Menunggu Approval Admin')->get();
                foreach ($reqSpkBaru as $req) {
                    $pendingApprovals[] = (object)[
                        'id'          => $req->id,
                        'user_name'   => 'Operator User',
                        'category'    => 'Production',
                        'description' => $req->no_po . ' (' . $req->produk . ') - Target: ' . $req->jumlah_produksi . ' Pcs',
                        'status'      => 'Pending',
                        'url_approve' => Route::has('produksi.approve_spk') ? route('produksi.approve_spk', $req->id) : '#',
                        'url_reject'  => Route::has('produksi.reject_spk') ? route('produksi.reject_spk', $req->id) : '#'
                    ];
                }
            }
        }
        $pendingCount = count($pendingApprovals);

        // 3. Kategori Inventory
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

        // 4. Data Status Production
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

        // 5. Status Lainnya
        $resourceActive = Supplier::count();
        $poCount        = InventoryPo::count();

        // Mengirim seluruh data ke view dashboard.blade.php
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
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $itemRequest = ItemRequest::findOrFail($id);

        $newStatus = ($request->input('action') === 'approve') ? 'Approved by Admin' : 'Rejected';
        
        $itemRequest->update([
            'status' => $newStatus
        ]);

        return back()->with('success', 'Status pengajuan berhasil diubah!');
    }
}