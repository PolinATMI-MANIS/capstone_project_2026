<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\InventoryPo;
use App\Models\Supplier;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Item Stok Aktif
        $totalInventory  = Item::count();

        // 2. Production
        $totalProduction = ItemRequest::where('department', 'LIKE', '%produksi%')->count();

        // 3. Resources (Supplier)
        $totalResources  = Supplier::count(); 

        // 4. Order / PO
        $totalOrders     = InventoryPo::count();

        // 5. RnD (jika ada model RnD atau diset 0)
        $totalRnd        = 0; 

        // Data Approval Pending
        $pendingApprovals = ItemRequest::where('status', 'LIKE', '%Pending%')->get();
        $pendingCount     = $pendingApprovals->count();

        // Kategori Inventory (5 Kategori)
        $invRaw = Item::where('category', 'Raw Material')
                    ->orWhere('category', 'LIKE', '%raw%')->count();

        $invWip = Item::where('category', 'Work In Process')
                    ->orWhere('category', 'LIKE', '%wip%')->count();

        $invFg  = Item::where('category', 'Finished Goods')
                    ->orWhere('category', 'LIKE', '%finished%')->count();

        $invMro = Item::where('category', 'MRO / Sparepart')
                    ->orWhere('category', 'LIKE', '%mro%')
                    ->orWhere('category', 'LIKE', '%sparepart%')->count();

        $invPacking = Item::where('category', 'Packing Material')
                    ->orWhere('category', 'LIKE', '%packing%')->count();

        // Data Status Production dari ItemRequest
        $prodPending  = ItemRequest::where('status', 'LIKE', '%Pending%')->count();
        $prodApproved = ItemRequest::where('status', 'LIKE', '%Approved%')->count();
        $prodRejected = ItemRequest::where('status', 'LIKE', '%Rejected%')->count();

        // Data Status Resources
        $resourceActive = Supplier::count();

        // Data Tipe Dokumen Order
        $poCount = InventoryPo::count();

        return view('dashboard', compact(
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