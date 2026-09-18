<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// ==========================================
// IMPORT SEMUA CONTROLLER DI SINI
// ==========================================
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\ManPowerController;
use App\Http\Controllers\MachinePowerController;
use App\Http\Controllers\WaitingResourceController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\RnDfeatureController;
use App\Http\Controllers\InventoryProduksiController;
use App\Http\Controllers\InventoryPoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// RUTE HELPER & PENGUJIAN
// ==========================================
Route::get('/masuk-paksa', function () {
    $user = User::first();
    if ($user) {
        Auth::login($user);
        return redirect('/dashboard'); 
    }
    return 'Data users KOSONG!';
});

Route::get('/login-as/{role}', [InventoryProduksiController::class, 'simulasiLogin'])->name('simulasi.login');

// Redirect Halaman Utama
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// ==========================================
// RUTE UNTUK TAMU (BELUM LOGIN)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');
    
    Route::post('/proses-login', [LoginController::class, 'authenticate']);
});


// ==========================================
// RUTE WAJIB LOGIN (SEMUA ROLE & MODUL)
// ==========================================
Route::middleware('auth')->group(function () {
    
    // Auth & Dashboard
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/approval/{id}/action', [DashboardController::class, 'handleApproval'])->name('approval.action');

    // ==========================================
    // MODUL PRODUKSI
    // ==========================================
    Route::prefix('produksi')->name('produksi.')->group(function () {
        Route::get('/', [ProduksiController::class, 'index'])->name('index');
        Route::post('/store', [ProduksiController::class, 'store'])->name('store');
        Route::post('/{id}/material-request', [ProduksiController::class, 'submitMaterialRequest'])->name('material.request');
        Route::post('/{id}/final-qc', [ProduksiController::class, 'submitFinalQc'])->name('final.qc');
        Route::post('/{id}/trouble', [ProduksiController::class, 'reportTrouble'])->name('trouble');
        Route::post('/{id}/resolve', [ProduksiController::class, 'resolveTrouble'])->name('resolve');
        
        // Approval Admin & Super Admin
        Route::post('/{id}/approve-spk', [ProduksiController::class, 'approveSpk'])->name('approve_spk');
        Route::post('/{id}/reject-spk', [ProduksiController::class, 'rejectSpk'])->name('reject_spk');
        Route::post('/{id}/request-delete', [ProduksiController::class, 'requestDelete'])->name('request_delete');
        Route::post('/{id}/approve-delete', [ProduksiController::class, 'approveDelete'])->name('approve_delete');
    });

    // ==========================================
    // MODUL RESOURCES (MAN POWER & MACHINE)
    // ==========================================
    Route::resource('man-power', ManPowerController::class)->except(['show']);
    Route::patch('/man-power/{id}/update-status', [ManPowerController::class, 'updateStatus']);
    Route::post('/man-power/{manPower}/update-process', [ManPowerController::class, 'update'])->name('man-power.update-process');

    Route::resource('machine-power', MachinePowerController::class)->except(['show']);
    Route::patch('/machine-power/{id}/update-status', [MachinePowerController::class, 'updateStatus']);

    Route::get('/waiting-for-resources', [WaitingResourceController::class, 'index'])->name('waiting-resources.index');
    Route::post('/api/waiting-resources/store', [WaitingResourceController::class, 'apiStore'])->name('waiting-resources.api-store');

    // ==========================================
    // MODUL PURCHASE & DELIVERY ORDER
    // ==========================================
    Route::get('/purchase-delivery', [PurchaseController::class, 'hub'])->name('purchase.hub');
    
    // Purchase Order
    Route::prefix('purchase')->name('purchase.')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('index');
        Route::post('/store', [PurchaseController::class, 'store']);
        Route::get('/{id}', [PurchaseController::class, 'show']);
        Route::put('/{id}', [PurchaseController::class, 'update']);
        Route::patch('/{id}/approval', [PurchaseController::class, 'approval']);
        Route::post('/{id}/request-delete', [PurchaseController::class, 'requestDelete'])->name('requestDelete');
        Route::delete('/{id}', [PurchaseController::class, 'destroy'])->name('destroy');
    });

    // Delivery Order
    Route::prefix('delivery')->name('delivery.')->group(function () {
        Route::get('/', [DeliveryOrderController::class, 'index'])->name('index');
        Route::post('/store', [DeliveryOrderController::class, 'store']);
        Route::get('/{id}', [DeliveryOrderController::class, 'show']);
        Route::put('/{id}/confirm', [DeliveryOrderController::class, 'confirmDelivery']);
        Route::patch('/{id}/approval', [DeliveryOrderController::class, 'approval']);
        Route::delete('/{id}', [DeliveryOrderController::class, 'destroy']);
    });

    // ==========================================
    // MODUL RnD
    // ==========================================
    Route::prefix('rnd')->name('rnd.')->group(function () {
        Route::get('/', [RnDfeatureController::class, 'index'])->name('index');
        Route::get('/create', [RnDfeatureController::class, 'create'])->name('create');
        Route::post('/store', [RnDfeatureController::class, 'store'])->name('store');
        Route::get('/{id}', [RnDfeatureController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [RnDfeatureController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [RnDfeatureController::class, 'destroy'])->name('destroy');
    });

    // ==========================================
    // MODUL INVENTORY
    // ==========================================
    Route::prefix('inventory')->name('inventory.')->group(function () {

        // Inventory Produksi (/inventory/produksi...)
        Route::prefix('produksi')->name('produksi.')->group(function () {
            Route::get('/', [InventoryProduksiController::class, 'index'])->name('index');
            Route::get('/laporan', [InventoryProduksiController::class, 'laporan'])->name('laporan');
            Route::get('/laporan/pdf', [InventoryProduksiController::class, 'exportPdf'])->name('laporan.pdf');
            Route::get('/laporan/excel', [InventoryProduksiController::class, 'exportExcel'])->name('laporan.excel');
            Route::get('/laporan/word', [InventoryProduksiController::class, 'exportWord'])->name('laporan.word');
            Route::get('/master', [InventoryProduksiController::class, 'master'])->name('master');

            // Barang Masuk (Mendukung _ dan -)
            Route::get('/barang_masuk', [InventoryProduksiController::class, 'barangMasuk'])->name('barang_masuk');
            Route::get('/barang-masuk', [InventoryProduksiController::class, 'barangMasuk'])->name('barang-masuk');
            Route::get('/barang_masuk/create', [InventoryProduksiController::class, 'createBarangMasuk'])->name('barang_masuk.create');
            Route::get('/barang-masuk/create', [InventoryProduksiController::class, 'createBarangMasuk'])->name('barang-masuk.create');
            Route::post('/barang_masuk', [InventoryProduksiController::class, 'storeBarangMasuk'])->name('barang_masuk.store');
            Route::post('/barang-masuk', [InventoryProduksiController::class, 'storeBarangMasuk'])->name('barang-masuk.store');

            // Barang Keluar (Mendukung _ dan -)
            Route::get('/barang_keluar', [InventoryProduksiController::class, 'barangKeluar'])->name('barang_keluar');
            Route::get('/barang-keluar', [InventoryProduksiController::class, 'barangKeluar'])->name('barang-keluar');
            Route::get('/barang_keluar/create', [InventoryProduksiController::class, 'createBarangKeluar'])->name('barang_keluar.create');
            Route::get('/barang-keluar/create', [InventoryProduksiController::class, 'createBarangKeluar'])->name('barang-keluar.create');
            Route::post('/barang_keluar', [InventoryProduksiController::class, 'storeBarangKeluar'])->name('barang_keluar.store');
            Route::post('/barang-keluar', [InventoryProduksiController::class, 'storeBarangKeluar'])->name('barang-keluar.store');

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

        // Inventory PO / Barang Jadi (/inventory/po...)
        Route::prefix('po')->name('po.')->group(function () {
            Route::get('/', [InventoryPoController::class, 'index'])->name('index');
            Route::get('/{id}', [InventoryPoController::class, 'show'])->name('show');

            Route::middleware('role:super_admin,admin')->group(function () {
                Route::get('/create', [InventoryPoController::class, 'create'])->name('create');
                Route::post('/', [InventoryPoController::class, 'store'])->name('store');
                Route::get('/approval', [InventoryPoController::class, 'approval'])->name('approval');
                Route::get('/{id}/edit', [InventoryPoController::class, 'edit'])->name('edit');
                Route::put('/{id}', [InventoryPoController::class, 'update'])->name('update');
                Route::delete('/{id}', [InventoryPoController::class, 'destroy'])->name('destroy');
            });
        });
    });

});