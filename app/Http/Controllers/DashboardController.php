<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;       // Atau PurchaseOrder (sesuai nama model kamu)
use App\Models\DeliveryOrder;  // Sesuaikan nama model DO kamu

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Semua Data Purchase & Delivery
        $purchases = Purchase::latest()->get();
        $deliveries = DeliveryOrder::latest()->get();

        // 2. Hitung Ringkasan Statistik
        $totalPO = $purchases->count();
        $totalDO = $deliveries->count();
        $pendingPO = $purchases->where('status', 'Waiting')->count();
        
        // Hitung total nilai transaksi PO (Kuantitas * Harga Satuan)
        $totalNilaiPO = $purchases->sum(function($po) {
            return ($po->kuantitas ?? 0) * ($po->harga_satuan ?? 0);
        });

        return view('dashboard', compact(
            'purchases', 
            'deliveries', 
            'totalPO', 
            'totalDO', 
            'pendingPO', 
            'totalNilaiPO'
        ));
    }
}