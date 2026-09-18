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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\InventoryProduksiController;
use App\Http\Controllers\InventoryPoController;

// ==========================================
// RUTE SIMULASI LOGIN UNTUK PENGUJIAN ROLE
// ==========================================
Route::get('/login-as/{role}', [InventoryProduksiController::class, 'simulasiLogin'])->name('simulasi.login');

// Group Induk Inventory
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
Route::patch('/machine-power/{id}/update-status', [MachinePowerController::class, 'updateStatus']);


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

    // BARIS YANG ERROR SEBELUMNYA DIHAPUS/KOMENTARI DI SINI:
    // return redirect()->route('inventory.produksi.index'); <-- Ini penyebab errornya karena tidak dibungkus Route::get
});

Route::prefix('inventory')->name('inventory.')->group(function () {

    // Route Inventory Produksi (/inventory/produksi...)
    Route::prefix('produksi')->name('produksi.')->group(function () {
        
        Route::get('/', [InventoryProduksiController::class, 'index'])->name('index');
        Route::get('/laporan', [InventoryProduksiController::class, 'laporan'])->name('laporan');
        Route::get('/laporan/pdf', [InventoryProduksiController::class, 'exportPdf'])->name('laporan.pdf');
        Route::get('/laporan/excel', [InventoryProduksiController::class, 'exportExcel'])->name('laporan.excel');
        Route::get('/laporan/word', [InventoryProduksiController::class, 'exportWord'])->name('laporan.word');
        Route::get('/master', [InventoryProduksiController::class, 'master'])->name('master');

        // =========================================================================
        // ROUTE GANDA (Mendukung Underscore '_' dan Dash '-')
        // =========================================================================
        
        // Barang Masuk
        Route::get('/barang_masuk', [InventoryProduksiController::class, 'barangMasuk'])->name('barang_masuk');
        Route::get('/barang-masuk', [InventoryProduksiController::class, 'barangMasuk'])->name('barang-masuk');
        
        Route::get('/barang_masuk/create', [InventoryProduksiController::class, 'createBarangMasuk'])->name('barang_masuk.create');
        Route::get('/barang-masuk/create', [InventoryProduksiController::class, 'createBarangMasuk'])->name('barang-masuk.create');
        
        Route::post('/barang_masuk', [InventoryProduksiController::class, 'storeBarangMasuk'])->name('barang_masuk.store');
        Route::post('/barang-masuk', [InventoryProduksiController::class, 'storeBarangMasuk'])->name('barang-masuk.store');

        // Barang Keluar
        Route::get('/barang_keluar', [InventoryProduksiController::class, 'barangKeluar'])->name('barang_keluar');
        Route::get('/barang-keluar', [InventoryProduksiController::class, 'barangKeluar'])->name('barang-keluar');
        
        Route::get('/barang_keluar/create', [InventoryProduksiController::class, 'createBarangKeluar'])->name('barang_keluar.create');
        Route::get('/barang-keluar/create', [InventoryProduksiController::class, 'createBarangKeluar'])->name('barang-keluar.create');
        
        Route::post('/barang_keluar', [InventoryProduksiController::class, 'storeBarangKeluar'])->name('barang_keluar.store');
        Route::post('/barang-keluar', [InventoryProduksiController::class, 'storeBarangKeluar'])->name('barang-keluar.store');

        // =========================================================================

        // Form Submit Barang Umum & Supplier
        Route::post('/barang', [InventoryProduksiController::class, 'storeBarang'])->name('barang.store');
        Route::get('/requests', [InventoryProduksiController::class, 'indexRequest'])->name('requests.index');
        Route::post('/requests', [InventoryProduksiController::class, 'storeRequest'])->name('requests.store');

        Route::post('/supplier', [InventoryProduksiController::class, 'storeSupplier'])->name('supplier.store');
        Route::put('/supplier/{id}', [InventoryProduksiController::class, 'updateSupplier'])->name('supplier.update');
        Route::delete('/supplier/{id}', [InventoryProduksiController::class, 'destroySupplier'])->name('supplier.destroy');

        // Aksi Berbahaya / Approval
        Route::put('/barang/{id}', [InventoryProduksiController::class, 'updateBarang'])->name('barang.update');
        Route::delete('/barang/{id}', [InventoryProduksiController::class, 'destroyBarang'])->name('barang.destroy');

        Route::post('/requests/{id}/approve', [InventoryProduksiController::class, 'approveRequest'])->name('requests.approve');
        Route::post('/requests/{id}/reject', [InventoryProduksiController::class, 'rejectRequest'])->name('requests.reject');
        Route::delete('/requests/{id}', [InventoryProduksiController::class, 'destroyRequest'])->name('requests.destroy');
    });

    // Route Inventory PO / Barang Jadi (/inventory/po...)
    Route::prefix('po')->name('po.')->group(function () {
        Route::get('/', [InventoryPoController::class, 'index'])->name('index');
        Route::get('/{id}', [InventoryPoController::class, 'show'])->name('show');

        Route::group(['middleware' => function ($request, $next) {
            if (auth()->check() && in_array(auth()->user()->role, ['super-admin', 'admin'])) {
                return $next($request);
            }
            abort(403, 'AKSES DITOLAK: Fitur PO ini khusus Super-Admin dan Admin.');
        }], function () {
            Route::get('/create', [InventoryPoController::class, 'create'])->name('create');
            Route::post('/', [InventoryPoController::class, 'store'])->name('store');
            Route::get('/approval', [InventoryPoController::class, 'approval'])->name('approval');
            Route::get('/{id}/edit', [InventoryPoController::class, 'edit'])->name('edit');
            Route::put('/{id}', [InventoryPoController::class, 'update'])->name('update');
            Route::delete('/{id}', [InventoryPoController::class, 'destroy'])->name('destroy');
        });
    });

});

// ========================================================
// TAMBAHAN: Jalur Masuk Paksa Jika Kena 502 Bad Gateway
// ========================================================
Route::get('/masuk-paksa', function () {
    $user = \App\Models\User::first();
    if ($user) {
        \Illuminate\Support\Facades\Auth::login($user);
        return redirect('/dashboard'); 
    }
    return 'Data users KOSONG!';
});