<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\RnDfeatureController;

// Redirect halaman utama ke dashboard
Route::get('/', function () {
    return redirect('/dashboard');
});

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

    // Modul RnD
    Route::prefix('rnd')->name('rnd.')->group(function () {
        Route::get('/', [RnDfeatureController::class, 'index'])->name('index');
        Route::get('/create', [RnDfeatureController::class, 'create'])->name('create');
        Route::post('/store', [RnDfeatureController::class, 'store'])->name('store');
        Route::get('/{id}', [RnDfeatureController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [RnDfeatureController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [RnDfeatureController::class, 'destroy'])->name('destroy');
    });

    // Route Resources / Man Power
    Route::get('/man-power', function () {
        return 'Halaman Resources / Man Power (Dalam Pengembangan)';
    })->name('man-power.index');

    Route::get('/purchase', function () { 
        return "Halaman Order Here! (Dalam Pengembangan)"; 
    });

    // RUTE KHUSUS (Super Admin & Admin)
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/inventory', function () { return "Halaman Inventory (Dalam Pengembangan)"; });
        Route::get('/production', function () { return "Halaman Production (Dalam Pengembangan)"; });
    });

    // RUTE KHUSUS Super Admin Saja
    Route::middleware('role:super_admin')->group(function () {
        // Rute khusus super admin di sini
    });

});