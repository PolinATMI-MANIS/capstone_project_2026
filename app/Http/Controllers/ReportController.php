<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManPower;
use App\Models\MachinePower;
use App\Models\ProductionOrder;

class ReportController extends Controller
{
    public function index()
    {
        $manPowers = ManPower::all();
        $machinePowers = MachinePower::with('operator')->get();
        $productionOrders = ProductionOrder::all();

        // Statistik Ringkasan untuk Header Laporan
        $stats = [
            'total_man'         => $manPowers->count(),
            'idle_man'          => $manPowers->where('status', 'Idle')->count(),
            'working_man'       => $manPowers->where('status', 'Kerja')->count(),
            'total_machine'     => $machinePowers->count(),
            'running_machine'   => $machinePowers->filter(fn($m) => trim(ucfirst(strtolower($m->status))) === 'Running')->count(),
            'standby_machine'   => $machinePowers->filter(fn($m) => trim(ucfirst(strtolower($m->status))) === 'Standby')->count(),
            'breakdown_machine' => $machinePowers->filter(fn($m) => trim(ucfirst(strtolower($m->status))) === 'Breakdown')->count(),
            'total_order'       => $productionOrders->count(),
            'ready_order'       => $productionOrders->where('status', 'ready')->count(),
        ];

        return view('reports.index', compact('manPowers', 'machinePowers', 'productionOrders', 'stats'));
    }
}