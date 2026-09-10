<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Pengecekan aman: Jika Model tim lain sudah dibuat, hitung jumlah datanya.
        // Jika belum ada/belum di-merge, otomatis bernilai 0.
        $totalInventory  = class_exists('App\Models\Inventory') ? \App\Models\Inventory::count() : 0;
        $totalProduction = class_exists('App\Models\Production') ? \App\Models\Production::count() : 0;
        $totalResources  = class_exists('App\Models\User') ? \App\Models\User::count() : 0;
        $totalOrders     = class_exists('App\Models\PurchaseOrder') ? \App\Models\PurchaseOrder::count() : 0;
        $totalRnd        = class_exists('App\Models\RndProject') ? \App\Models\RndProject::count() : 0;

        return view('dashboard', compact(
            'totalInventory',
            'totalProduction',
            'totalResources',
            'totalOrders',
            'totalRnd'
        ));
    }
}