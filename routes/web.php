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
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;

// Rute untuk Tamu / Belum Login
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login'); 
    })->name('login');

    Route::post('/proses-login', [LoginController::class, 'authenticate']);
});

// Rute Wajib Login (Semua Role: super_admin, admin, user)
Route::middleware('auth')->group(function () {
    
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/approval/{id}/action', [DashboardController::class, 'handleApproval']);

    // --- RUTE DUMMY SEMENTARA (Bisa diakses semua role) ---
    Route::get('/resources', function () { return "Halaman Resources (Dalam Pengembangan)"; });
    Route::get('/rnd', function () { return "Halaman RnD (Dalam Pengembangan)"; });
    
    // Karena tadi kamu bilang Purchase sudah bisa, arahkan ke view aslinya:
    Route::get('/purchase', function () { return "Halaman Order Here! (Dalam Pengembangan)"; });
    // (Ganti jadi "Dalam Pengembangan" jika view purchase.blade.php ternyata belum ada)

    // --- RUTE KHUSUS (Cuma bisa diakses Super Admin & Admin) ---
    Route::middleware('role:super_admin,admin')->group(function () {
        
        // Rute dummy sementara karena filenya belum di-merge
        Route::get('/inventory', function () { return "Halaman Inventory (Dalam Pengembangan)"; });
        Route::get('/production', function () { return "Halaman Production (Dalam Pengembangan)"; });

        /* 
         * CATATAN: 
         * Nanti kalau file dari tim kamu sudah di-merge, hapus 2 baris dummy di atas, 
         * lalu pakai kode aslinya yang ini:
         * 
         * Route::get('/inventory', [InventoryController::class, 'index']);
         * Route::get('/production', function () { return view('production'); });
         */
    });

    // Rute KHUSUS Super Admin Saja
    Route::middleware('role:super_admin')->group(function () {
        // Rute khusus super admin di sini
    });

});
