<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DeliveryOrderController;
use App\Models\Purchase;
use App\Models\DeliveryOrder;

// Halaman Login (WAJIB ada ->name('login') di ujungnya)
Route::get('/login', function () {
    return view('login');
})->name('login');

// Proses Login & Logout
Route::post('/proses-login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

// Halaman yang diproteksi (Hanya bisa dibuka jika sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $totalPO = Purchase::count();
        $totalDO = DeliveryOrder::count();
        return view('dashboard', compact('totalPO', 'totalDO'));
    });

    Route::get('/purchase', [PurchaseController::class, 'index']);
    Route::get('/delivery', [DeliveryOrderController::class, 'index']);
});