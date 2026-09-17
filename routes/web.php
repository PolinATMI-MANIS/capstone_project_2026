<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryProduksiController;
use App\Http\Controllers\InventoryPoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==========================================
// RUTE SIMULASI LOGIN UNTUK PENGUJIAN ROLE
// ==========================================
Route::get('/login-as/{role}', [InventoryProduksiController::class, 'simulasiLogin'])->name('simulasi.login');

// Rute untuk Tamu / Belum Login
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::post('/proses-login', [LoginController::class, 'authenticate']);
});

// ==========================================
// RUTE WAJIB LOGIN (SEMUA ROLE)
// ==========================================
Route::middleware(['auth'])->group(function () {

    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

    // Redirect Root ke Inventory Produksi
    Route::get('/', function () {
        return redirect()->route('inventory.produksi.index');
    });

    // Group Induk Inventory (/inventory/...)
    Route::prefix('inventory')->name('inventory.')->group(function () {

        // -----------------------------------------------------------------
        // ROUTE INVENTORY PRODUKSI (/inventory/produksi/...)
        // -----------------------------------------------------------------
        Route::prefix('produksi')->name('produksi.')->group(function () {

            // Read / Lihat Data (Semua Role Login)
            Route::get('/', [InventoryProduksiController::class, 'index'])->name('index');
            Route::get('/laporan', [InventoryProduksiController::class, 'laporan'])->name('laporan');
            Route::get('/laporan/pdf', [InventoryProduksiController::class, 'exportPdf'])->name('laporan.pdf');
            Route::get('/laporan/excel', [InventoryProduksiController::class, 'exportExcel'])->name('laporan.excel');
            Route::get('/laporan/word', [InventoryProduksiController::class, 'exportWord'])->name('laporan.word');
            Route::get('/master', [InventoryProduksiController::class, 'master'])->name('master');

            // --- Transaksi Barang Masuk ---
            Route::get('/barang_masuk', [InventoryProduksiController::class, 'barangMasuk'])->name('barang_masuk');
            Route::get('/barang-masuk', [InventoryProduksiController::class, 'barangMasuk'])->name('barang-masuk');
            
            Route::get('/barang_masuk/create', [InventoryProduksiController::class, 'createBarangMasuk'])->name('barang_masuk.create');
            Route::get('/barang-masuk/create', [InventoryProduksiController::class, 'createBarangMasuk'])->name('barang-masuk.create');
            Route::get('/create-barang-masuk', [InventoryProduksiController::class, 'createBarangMasuk'])->name('create-barang-masuk');
            Route::get('/create_barang_masuk', [InventoryProduksiController::class, 'createBarangMasuk'])->name('create_barang_masuk');

            Route::post('/barang_masuk', [InventoryProduksiController::class, 'storeBarangMasuk'])->name('barang_masuk.store');
            Route::post('/barang-masuk', [InventoryProduksiController::class, 'storeBarangMasuk'])->name('barang-masuk.store');

            // --- Transaksi Barang Keluar ---
            Route::get('/barang_keluar', [InventoryProduksiController::class, 'barangKeluar'])->name('barang_keluar');
            Route::get('/barang-keluar', [InventoryProduksiController::class, 'barangKeluar'])->name('barang-keluar');

            Route::get('/barang_keluar/create', [InventoryProduksiController::class, 'createBarangKeluar'])->name('barang_keluar.create');
            Route::get('/barang-keluar/create', [InventoryProduksiController::class, 'createBarangKeluar'])->name('barang-keluar.create');

            Route::post('/barang_keluar', [InventoryProduksiController::class, 'storeBarangKeluar'])->name('barang_keluar.store');
            Route::post('/barang-keluar', [InventoryProduksiController::class, 'storeBarangKeluar'])->name('barang-keluar.store');

            // --- Pengajuan Request ---
            Route::get('/requests', [InventoryProduksiController::class, 'indexRequest'])->name('requests.index');
            Route::post('/requests', [InventoryProduksiController::class, 'storeRequest'])->name('requests.store');

            // -------------------------------------------------------------
            // RUTE UNTUK SUPERADMIN & ADMIN
            // -------------------------------------------------------------
            Route::middleware('role:SUPER_ADMIN,super_admin,super-admin,admin')->group(function () {
                // Master Barang (Alias Tambahan untuk Modal)
                Route::post('/barang', [InventoryProduksiController::class, 'storeBarang'])->name('barang.store');
                Route::post('/master-barang', [InventoryProduksiController::class, 'storeBarang'])->name('master_barang.store'); // Penyelarasan Form Modal
                Route::post('/barang/ajax-store', [InventoryProduksiController::class, 'storeAjax'])->name('barang.storeAjax');
                Route::put('/barang/{id}', [InventoryProduksiController::class, 'updateBarang'])->name('barang.update');
                Route::delete('/barang/{id}', [InventoryProduksiController::class, 'destroyBarang'])->name('barang.destroy');

                // Master Supplier
                Route::post('/supplier', [InventoryProduksiController::class, 'storeSupplier'])->name('supplier.store');
                Route::put('/supplier/{id}', [InventoryProduksiController::class, 'updateSupplier'])->name('supplier.update');
                Route::delete('/supplier/{id}', [InventoryProduksiController::class, 'destroySupplier'])->name('supplier.destroy');

                // Approval Request
                Route::post('/requests/{id}/approve', [InventoryProduksiController::class, 'approveRequest'])->name('requests.approve');
                Route::post('/requests/{id}/reject', [InventoryProduksiController::class, 'rejectRequest'])->name('requests.reject');
            });

            // -------------------------------------------------------------
            // RUTE KHUSUS SUPERADMIN SAJA
            // -------------------------------------------------------------
            Route::middleware('role:SUPER_ADMIN,super_admin,super-admin')->group(function () {
                Route::delete('/requests/{id}', [InventoryProduksiController::class, 'destroyRequest'])->name('requests.destroy');
                Route::post('/requests/bulk-destroy', [InventoryProduksiController::class, 'destroyBulkRequests'])->name('requests.destroyBulk');
            });
        });

        // -----------------------------------------------------------------
        // ROUTE INVENTORY PO / BARANG JADI (/inventory/po/...)
        // -----------------------------------------------------------------
        Route::prefix('po')->name('po.')->group(function () {
            Route::get('/', [InventoryPoController::class, 'index'])->name('index');

            Route::middleware('role:SUPER_ADMIN,super_admin,super-admin,admin')->group(function () {
                Route::get('/create', [InventoryPoController::class, 'create'])->name('create');
                Route::post('/', [InventoryPoController::class, 'store'])->name('store');
                Route::get('/approval', [InventoryPoController::class, 'approval'])->name('approval');
                Route::get('/{id}/edit', [InventoryPoController::class, 'edit'])->name('edit');
                Route::put('/{id}', [InventoryPoController::class, 'update'])->name('update');
                Route::delete('/{id}', [InventoryPoController::class, 'destroy'])->name('destroy');
            });

            Route::get('/{id}', [InventoryPoController::class, 'show'])->name('show');
        });
    });

    // ---------------------------------------------------------------------
    // DASHBOARD & MODUL LAINNYA
    // ---------------------------------------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/approval/{id}/action', [DashboardController::class, 'handleApproval']);

    Route::get('/resources', function () { return "Halaman Resources (Dalam Pengembangan)"; });
    Route::get('/rnd', function () { return "Halaman RnD (Dalam Pengembangan)"; });
    Route::get('/purchase', function () { return "Halaman Order Here! (Dalam Pengembangan)"; });

    Route::middleware('role:SUPER_ADMIN,super_admin,super-admin,admin')->group(function () {
        Route::get('/inventory', function () { return "Halaman Inventory (Dalam Pengembangan)"; });
        Route::get('/production', function () { return "Halaman Production (Dalam Pengembangan)"; });
    });
});