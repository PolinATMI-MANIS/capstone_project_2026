<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Pengecekan aman (Class Check) jika Model milik tim belum dibuat/di-migrate
        $totalInventory  = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::count() : 0;
        $totalProduction = class_exists('\App\Models\Production') ? \App\Models\Production::count() : 0;
        $totalResources  = class_exists('\App\Models\Resource') ? \App\Models\Resource::count() : 0;
        $totalOrders     = class_exists('\App\Models\Order') ? \App\Models\Order::count() : 0;
        $totalRnd        = class_exists('\App\Models\Rnd') ? \App\Models\Rnd::count() : 0;

        // Data Approval Pending (Otomatis sinkron ke Dashboard & Profile)
        // Jika nanti tim sudah buat tabel approval, tinggal ganti ke: \App\Models\Approval::where('status', 'pending')->get();
        $pendingApprovals = []; 
        $pendingCount     = count($pendingApprovals);

        // Kategori Inventory (Aman dari error class)
        $invRaw = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('type', 'raw')->count() : 0;
        $invWip = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('type', 'wip')->count() : 0;
        $invFg  = class_exists('\App\Models\Inventory') ? \App\Models\Inventory::where('type', 'finished')->count() : 0;

        // Data Production (Aman dari error class)
        $prodPending = class_exists('\App\Models\Production') ? \App\Models\Production::where('status', 'pending')->count() : 0;

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
            'prodPending'
        ));
    }

    public function handleApproval(Request $request, $id)
    {
        $action = $request->input('action'); // 'approve' atau 'reject'
        
        // Nanti tinggal dihubungkan dengan tabel database jika tim sudah buat
        return back()->with('success', 'Status pengajuan berhasil diubah!');
    }
}