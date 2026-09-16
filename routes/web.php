<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduksiController;

// Rute untuk Sistem SPK & QC (Produksi)
Route::get('/produksi', [ProduksiController::class, 'index'])->name('produksi.index');
Route::post('/produksi/store', [ProduksiController::class, 'store'])->name('produksi.store');
Route::post('/produksi/{id}/material-request', [ProduksiController::class, 'submitMaterialRequest'])->name('produksi.material.request');
Route::post('/produksi/{id}/final-qc', [ProduksiController::class, 'submitFinalQc'])->name('produksi.final.qc');

// Rute Sistem Andon (Downtime/Trouble)
Route::post('/produksi/{id}/trouble', [ProduksiController::class, 'reportTrouble'])->name('produksi.trouble');
Route::post('/produksi/{id}/resolve', [ProduksiController::class, 'resolveTrouble'])->name('produksi.resolve');

// Rute Akses Role (Admin & Super Admin)
Route::post('/produksi/{id}/approve-spk', [ProduksiController::class, 'approveSpk'])->name('produksi.approve_spk');
Route::post('/produksi/{id}/reject-spk', [ProduksiController::class, 'rejectSpk'])->name('produksi.reject_spk');
Route::post('/produksi/{id}/request-delete', [ProduksiController::class, 'requestDelete'])->name('produksi.request_delete');
Route::post('/produksi/{id}/approve-delete', [ProduksiController::class, 'approveDelete'])->name('produksi.approve_delete');

use App\Http\Controllers\ManPowerController;
use App\Http\Controllers\MachinePowerController;
use App\Http\Controllers\WaitingResourceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('man-power.index');
});


Route::get('/man-power', [ManPowerController::class, 'index'])->name('man-power.index');
Route::get('/man-power/create', [ManPowerController::class, 'create'])->name('man-power.create');
Route::post('/man-power', [ManPowerController::class, 'store'])->name('man-power.store');
Route::get('/man-power/{manPower}/edit', [ManPowerController::class, 'edit'])->name('man-power.edit');

Route::put('/man-power/{manPower}', [ManPowerController::class, 'update'])->name('man-power.update');
Route::patch('/man-power/{manPower}', [ManPowerController::class, 'update']);
Route::post('/man-power/{manPower}/update-process', [ManPowerController::class, 'update'])->name('man-power.update-process');

Route::delete('/man-power/{manPower}', [ManPowerController::class, 'destroy'])->name('man-power.destroy');
Route::patch('/man-power/{id}/update-status', [ManPowerController::class, 'updateStatus']);


Route::get('/machine-power', [MachinePowerController::class, 'index'])->name('machine-power.index');
Route::get('/machine-power/create', [MachinePowerController::class, 'create'])->name('machine-power.create');
Route::post('/machine-power', [MachinePowerController::class, 'store'])->name('machine-power.store');
Route::get('/machine-power/{machinePower}/edit', [MachinePowerController::class, 'edit'])->name('machine-power.edit');

Route::put('/machine-power/{machinePower}', [MachinePowerController::class, 'update'])->name('machine-power.update');
Route::patch('/machine-power/{machinePower}', [MachinePowerController::class, 'update']);
Route::delete('/machine-power/{machinePower}', [MachinePowerController::class, 'destroy'])->name('machine-power.destroy');
Route::patch('/machine-power/{id}/update-status', [MachinePowerController::class, 'updateStatus'])->name('machine-power.update-status');


Route::get('/waiting-for-resources', [WaitingResourceController::class, 'index'])->name('waiting-resources.index');
Route::post('/api/waiting-resources/store', [WaitingResourceController::class, 'apiStore'])->name('waiting-resources.api-store');


Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login'); 
    })->name('login');

    Route::post('/proses-login', [LoginController::class, 'authenticate']);
});

Route::middleware('auth')->group(function () {
    
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/approval/{id}/action', [DashboardController::class, 'handleApproval'])->name('approval.action');

    Route::get('/resources', function () { return "Halaman Resources (Dalam Pengembangan)"; });
    Route::get('/rnd', function () { return "Halaman RnD (Dalam Pengembangan)"; });
    Route::get('/purchase', function () { return "Halaman Order Here! (Dalam Pengembangan)"; });

    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/inventory', function () { return "Halaman Inventory (Dalam Pengembangan)"; });
        Route::get('/production', function () { return "Halaman Production (Dalam Pengembangan)"; });
    });

    Route::middleware('role:super_admin')->group(function () {
    });

    Route::post('/waiting-resources/update-status/{id}', [App\Http\Controllers\WaitingResourceController::class, 'updateStatus'])->name('waiting-resources.update');
    Route::delete('/waiting-resources/{id}', [App\Http\Controllers\WaitingResourceController::class, 'destroy'])->name('waiting-resources.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

});
