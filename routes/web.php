<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\ManPowerController;
use App\Http\Controllers\MachinePowerController;
use App\Http\Controllers\WaitingResourceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RnDfeatureController;
use App\Models\ApprovalRequest;
use App\Models\ManPower;
use App\Models\MachinePower;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('man-power.index');
});

// Rute untuk Sistem SPK & QC (Produksi)
Route::get('/produksi', [ProduksiController::class, 'index'])->name('produksi.index');
Route::post('/produksi/store', [ProduksiController::class, 'store'])->name('produksi.store');
Route::post('/produksi/{id}/material-request', [ProduksiController::class, 'submitMaterialRequest'])->name('produksi.material.request');
Route::post('/produksi/{id}/final-qc', [ProduksiController::class, 'submitFinalQc'])->name('produksi.final.qc');
Route::post('/produksi/{id}/trouble', [ProduksiController::class, 'reportTrouble'])->name('produksi.trouble');
Route::post('/produksi/{id}/resolve', [ProduksiController::class, 'resolveTrouble'])->name('produksi.resolve');
Route::post('/produksi/{id}/approve-spk', [ProduksiController::class, 'approveSpk'])->name('produksi.approve_spk');
Route::post('/produksi/{id}/reject-spk', [ProduksiController::class, 'rejectSpk'])->name('produksi.reject_spk');
Route::post('/produksi/{id}/request-delete', [ProduksiController::class, 'requestDelete'])->name('produksi.request_delete');
Route::post('/produksi/{id}/approve-delete', [ProduksiController::class, 'approveDelete'])->name('produksi.approve_delete');

// Rute Man Power
Route::get('/man-power', [ManPowerController::class, 'index'])->name('man-power.index');
Route::get('/man-power/create', [ManPowerController::class, 'create'])->name('man-power.create');
Route::post('/man-power', [ManPowerController::class, 'store'])->name('man-power.store');
Route::patch('/man-power/{id}/update-status', [ManPowerController::class, 'updateStatus']);
Route::get('/man-power/{manPower}/edit', [ManPowerController::class, 'edit'])->name('man-power.edit');
Route::put('/man-power/{manPower}', [ManPowerController::class, 'update'])->name('man-power.update');
Route::patch('/man-power/{manPower}', [ManPowerController::class, 'update']);
Route::post('/man-power/{manPower}/update-process', [ManPowerController::class, 'update'])->name('man-power.update-process');
Route::delete('/man-power/{manPower}', [ManPowerController::class, 'destroy'])->name('man-power.destroy');

// Rute Machine Power
Route::get('/machine-power', [MachinePowerController::class, 'index'])->name('machine-power.index');
Route::get('/machine-power/create', [MachinePowerController::class, 'create'])->name('machine-power.create');
Route::post('/machine-power', [MachinePowerController::class, 'store'])->name('machine-power.store');
Route::patch('/machine-power/{id}/update-status', [MachinePowerController::class, 'updateStatus'])->name('machine-power.update-status');
Route::get('/machine-power/{machinePower}/edit', [MachinePowerController::class, 'edit'])->name('machine-power.edit');
Route::put('/machine-power/{machinePower}', [MachinePowerController::class, 'update'])->name('machine-power.update');
Route::patch('/machine-power/{machinePower}', [MachinePowerController::class, 'update']);
Route::delete('/machine-power/{machinePower}', [MachinePowerController::class, 'destroy'])->name('machine-power.destroy');

// Rute Waiting for Resources
Route::get('/waiting-for-resources', [WaitingResourceController::class, 'index'])->name('waiting-resources.index');
Route::post('/api/waiting-resources/store', [WaitingResourceController::class, 'apiStore'])->name('waiting-resources.api-store');

// Route Modul RnD
Route::prefix('rnd')->name('rnd.')->group(function () {
    Route::get('/', [RnDfeatureController::class, 'index'])->name('index');
    Route::get('/create', [RnDfeatureController::class, 'create'])->name('create');
    Route::post('/store', [RnDfeatureController::class, 'store'])->name('store');
    Route::get('/{id}', [RnDfeatureController::class, 'show'])->name('show');
    Route::patch('/{id}/status', [RnDfeatureController::class, 'updateStatus'])->name('updateStatus');
    Route::delete('/{id}', [RnDfeatureController::class, 'destroy'])->name('destroy');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login'); 
    })->name('login');

    Route::post('/proses-login', [LoginController::class, 'authenticate']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/approval/{id}/action', [DashboardController::class, 'handleApproval'])->name('approval.action');

    // Eksekusi Approve / Reject untuk ManPower & MachinePower dari Dashboard
    Route::match(['get', 'post'], '/approval-request/{id}/process', function (\Illuminate\Http\Request $request, $id) {
        $approval = ApprovalRequest::findOrFail($id);
        $action = $request->input('action'); 

        if ($action === 'approve') {
            $payload = json_decode($approval->payload, true);
            if ($approval->action_type === 'create') {
                if ($approval->target_type === 'ManPower') ManPower::create($payload);
                elseif ($approval->target_type === 'MachinePower') MachinePower::create($payload);
            } elseif ($approval->action_type === 'update') {
                if ($approval->target_type === 'ManPower') { $t = ManPower::find($approval->target_id); if($t) $t->update($payload); }
                elseif ($approval->target_type === 'MachinePower') { $t = MachinePower::find($approval->target_id); if($t) $t->update($payload); }
            } elseif ($approval->action_type === 'delete') {
                if ($approval->target_type === 'ManPower') { $t = ManPower::find($approval->target_id); if($t) $t->delete(); }
                elseif ($approval->target_type === 'MachinePower') { $t = MachinePower::find($approval->target_id); if($t) $t->delete(); }
            }
            $approval->delete();
            return back()->with('success', 'Pengajuan berhasil disetujui!');
        } else {
            $approval->delete();
            return back()->with('warning', 'Pengajuan ditolak.');
        }
    })->name('approval.process');

    // --- PURCHASE & DELIVERY (Modul Tim Lain yg Direstore) ---
    Route::get('/purchase-delivery', [PurchaseController::class, 'hub'])->name('purchase.hub');
    
    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::get('/purchase/{id}', [PurchaseController::class, 'show']);
    Route::post('/purchase/store', [PurchaseController::class, 'store']);
    Route::put('/purchase/{id}', [PurchaseController::class, 'update']);
    Route::patch('/purchase/{id}/approval', [PurchaseController::class, 'approval']);
    Route::post('/purchase/{id}/request-delete', [PurchaseController::class, 'requestDelete'])->name('purchase.requestDelete');
    Route::delete('/purchase/{id}', [PurchaseController::class, 'destroy'])->name('purchase.destroy');

    Route::get('/delivery', [DeliveryOrderController::class, 'index'])->name('delivery.index');
    Route::get('/delivery/{id}', [DeliveryOrderController::class, 'show']);
    Route::post('/delivery/store', [DeliveryOrderController::class, 'store']);
    Route::put('/delivery/{id}/confirm', [DeliveryOrderController::class, 'confirmDelivery']);
    Route::patch('/delivery/{id}/approval', [DeliveryOrderController::class, 'approval']);
    Route::delete('/delivery/{id}', [DeliveryOrderController::class, 'destroy']);
    // ---------------------------------------------------------

    Route::get('/resources', function () { return "Halaman Resources (Dalam Pengembangan)"; });

    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/inventory', function () { return "Halaman Inventory (Dalam Pengembangan)"; });
        Route::get('/production', function () { return "Halaman Production (Dalam Pengembangan)"; });
    });

    Route::post('/waiting-resources/update-status/{id}', [WaitingResourceController::class, 'updateStatus'])->name('waiting-resources.update');
    Route::delete('/waiting-resources/{id}', [WaitingResourceController::class, 'destroy'])->name('waiting-resources.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    Route::get('/link-storage-host', function () {
        Artisan::call('storage:link');
        return 'Sukses membuat storage link!';
    });
});