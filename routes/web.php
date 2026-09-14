<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryProduksiController;
use App\Http\Controllers\InventoryPoController;

// ==========================================
// RUTE SIMULASI LOGIN UNTUK PENGUJIAN ROLE
// ==========================================
Route::get('/login-as/{role}', [InventoryProduksiController::class, 'simulasiLogin'])->name('simulasi.login');

// Group Induk Inventory
Route::get('/', function () {
    return redirect()->route('inventory.produksi.index');
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