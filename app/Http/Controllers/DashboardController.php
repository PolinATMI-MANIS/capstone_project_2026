<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\DeliveryOrder;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Data PO & DO
        $purchases  = class_exists('\App\Models\Purchase') ? Purchase::all() : collect();
        $deliveries = class_exists('\App\Models\DeliveryOrder') ? DeliveryOrder::all() : collect();

        $totalPO   = class_exists('\App\Models\Purchase') ? Purchase::count() : 0;
        $totalDO   = class_exists('\App\Models\DeliveryOrder') ? DeliveryOrder::count() : 0;
        $pendingPO = class_exists('\App\Models\Purchase') ? Purchase::where('status', 'Waiting')->count() : 0;
        $pendingDO = class_exists('\App\Models\DeliveryOrder') ? DeliveryOrder::where('status', 'Pending')->count() : 0;

        $totalOrders = $pendingPO + $pendingDO;

        // Total Nilai Transaksi PO
        $totalNilaiPO = $purchases->sum(function ($po) {
            return ($po->kuantitas ?? 0) * ($po->harga_satuan ?? $po->harga ?? 0);
        });

        // 2. Pengecekan Aman Model Eksternal / Tim Lain
        $totalInventory  = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::count() : 0;
        $totalProduction = class_exists('\App\Models\ProductionOrder') ? \App\Models\ProductionOrder::count() : 0;
        $totalResources  = class_exists('\App\Models\Resource') ? \App\Models\Resource::count() : 0;
        $totalRnd        = class_exists('\App\Models\Rnd') ? \App\Models\Rnd::count() : 0;

        // 3. Approval & Notifikasi
        $pendingApprovals = class_exists('\App\Models\Approval') ? \App\Models\Approval::where('status', 'Pending')->get() : collect();
        $pendingCount     = $pendingApprovals->count();

        // 4. Data Breakdown Chart (Inventory)
        $invRaw = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('category', 'Raw Material')->count() : 0;
        $invWip = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('category', 'WIP')->count() : 0;
        $invFg  = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('category', 'Finished Goods')->count() : 0;

        // 5. Data Breakdown Chart (Production)
        $prodPending = class_exists('\App\Models\Production') ? \App\Models\Production::where('status', 'Pending')->count() : 0;
        $prodRunning = class_exists('\App\Models\Production') ? \App\Models\Production::where('status', 'Running')->count() : 0;
        $prodDone    = class_exists('\App\Models\Production') ? \App\Models\Production::where('status', 'Done')->count() : 0;

        // 6. Data Breakdown Chart (Resources)
        $resActive = class_exists('\App\Models\Resource') ? \App\Models\Resource::where('status', 'Active')->count() : 0;
        $resIdle   = class_exists('\App\Models\Resource') ? \App\Models\Resource::where('status', 'Idle')->count() : 0;
        $resLeave  = class_exists('\App\Models\Resource') ? \App\Models\Resource::where('status', 'On Leave')->count() : 0;

        // 7. Data Breakdown Chart (Order)
        $orderPo  = $totalPO;
        $orderDo  = $totalDO;
        $orderInv = class_exists('\App\Models\Invoice') ? \App\Models\Invoice::count() : 0;

        // 8. Data Breakdown Chart (RnD)
        $rndResearch = class_exists('\App\Models\Rnd') ? \App\Models\Rnd::where('phase', 'Research')->count() : 0;
        $rndProto    = class_exists('\App\Models\Rnd') ? \App\Models\Rnd::where('phase', 'Prototyping')->count() : 0;
        $rndTesting  = class_exists('\App\Models\Rnd') ? \App\Models\Rnd::where('phase', 'Testing')->count() : 0;

        return view('dashboard', compact(
            'purchases',
            'deliveries',
            'totalPO',
            'totalDO',
            'pendingPO',
            'totalOrders',
            'totalNilaiPO',
            'totalInventory',
            'totalProduction',
            'totalResources',
            'totalRnd',
            'pendingApprovals',
            'pendingCount',
            'invRaw',
            'invWip',
            'invFg',
            'prodPending',
            'prodRunning',
            'prodDone',
            'resActive',
            'resIdle',
            'resLeave',
            'orderPo',
            'orderDo',
            'orderInv',
            'rndResearch',
            'rndProto',
            'rndTesting'
        ));
    }
}