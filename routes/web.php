<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DeliveryOrderController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// 1. Jika buka 127.0.0.1:8000, langsung lempar ke halaman purchase
Route::get('/', function () {
    return redirect('/purchase');
});

// 2. Semua route bisa dibuka langsung TANPA LOGIN
Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/purchase', [PurchaseController::class, 'index']);
Route::post('/purchase/store', [PurchaseController::class, 'store']); // Untuk simpan data
Route::get('/delivery/{id}', [DeliveryOrderController::class, 'show']);
Route::get('/purchase/{id}', [PurchaseController::class, 'show']);
Route::get('/delivery', [DeliveryOrderController::class, 'index']);
Route::post('/delivery/store', [DeliveryOrderController::class, 'store']);
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::get('/login-as/{role}', function ($role) {
    $user = User::where('role', $role)->first();
    if (!$user) {
        return "User dengan role {$role} belum ada di database!";
    }
    Auth::login($user);
    return redirect('/purchase');
});
Route::get('/purchase-delivery', [PurchaseController::class, 'hub']);