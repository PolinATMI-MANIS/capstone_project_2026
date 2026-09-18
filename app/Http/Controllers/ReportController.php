<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionOrder;
use App\Models\ManPower;
use App\Models\Inventory;
use App\Models\Purchase;
use App\Models\RnDfeature;

class ReportController extends Controller
{
    public function index()
    {
        // 1. DATA PRODUKSI (Sekarang ditarik semua datanya)
        $produksi_pending = ProductionOrder::whereIn('status', ['Menunggu Approval Admin', 'Menunggu Bahan Baku'])->get();
        $produksi_running = ProductionOrder::where('status', 'Proses Produksi Berjalan')->get();
        $produksi_selesai = ProductionOrder::where('status', 'Selesai')->get();

        $spk_pending = $produksi_pending->count();
        $spk_running = $produksi_running->count();
        $spk_selesai = $produksi_selesai->count();

        // 2. DATA RESOURCES
        $man_power = ManPower::all();

        // 3. DATA INVENTORY
        $inv_total = Inventory::count();
        $inventory_items = Inventory::latest()->take(10)->get();

        // 4. DATA ORDER / PURCHASE
        $po_total = Purchase::count();
        $purchase_items = Purchase::latest()->take(10)->get();

        // 5. DATA RnD
        $rnd_total = RnDfeature::count();
        $rnd_items = RnDfeature::latest()->take(10)->get();

        return view('reports.index', compact(
            'spk_pending', 'spk_running', 'spk_selesai', 
            'produksi_pending', 'produksi_running', 'produksi_selesai',
            'man_power',
            'inv_total', 'inventory_items',
            'po_total', 'purchase_items',
            'rnd_total', 'rnd_items'
        ));
    }
}