<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\InventoryController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// 1. Redirect Halaman Utama ke Hub Purchase & Delivery
Route::get('/', function () {
    return redirect('/purchase-delivery');
});

// ==========================================
// RUTE UNTUK TAMU (BELUM LOGIN)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');
    Route::post('/proses-login', [LoginController::class, 'authenticate']);

    // Rute Helper untuk Testing Login Cepat
    Route::get('/login-as/{role}', function ($role) {
        $user = User::where('role', $role)->first();
        if (!$user) {
            return "User dengan role {$role} belum ada di database!";
        }
        Auth::login($user);
        return redirect('/purchase-delivery');
    });
});

// ==========================================
// RUTE WAJIB LOGIN (SEMUA ROLE)
// ==========================================
Route::middleware('auth')->group(function () {
    
    // Logout
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/approval/{id}/action', [DashboardController::class, 'handleApproval']);

    // --- HALAMAN UTAMA HUB ---
    Route::get('/purchase-delivery', [PurchaseController::class, 'hub'])->name('purchase.hub');

    // --- PURCHASE ORDER ---
    Route::get('/purchase', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::get('/purchase/{id}', [PurchaseController::class, 'show']);
    Route::post('/purchase/store', [PurchaseController::class, 'store']);
    Route::put('/purchase/{id}', [PurchaseController::class, 'update']);
    Route::patch('/purchase/{id}/approval', [PurchaseController::class, 'approval']);
    Route::post('/purchase/{id}/request-delete', [PurchaseController::class, 'requestDelete'])->name('purchase.requestDelete');
    Route::delete('/purchase/{id}', [PurchaseController::class, 'destroy'])->name('purchase.destroy');

    // --- DELIVERY ORDER ---
    Route::get('/delivery', [DeliveryOrderController::class, 'index'])->name('delivery.index');
    Route::get('/delivery/{id}', [DeliveryOrderController::class, 'show']);
    Route::post('/delivery/store', [DeliveryOrderController::class, 'store']);
    Route::put('/delivery/{id}/confirm', [DeliveryOrderController::class, 'confirmDelivery']);
    Route::patch('/delivery/{id}/approval', [DeliveryOrderController::class, 'approval']);
    Route::delete('/delivery/{id}', [DeliveryOrderController::class, 'destroy']);

    // Dummy Modules
    Route::get('/resources', function () { return "Halaman Resources (Dalam Pengembangan)"; });
    Route::get('/rnd', function () { return "Halaman RnD (Dalam Pengembangan)"; });

    // --- RUTE KHUSUS SUPER ADMIN & ADMIN ---
    Route::middleware('role:super_admin,admin')->group(function () {
        Route::get('/inventory', function () { return "Halaman Inventory (Dalam Pengembangan)"; });
        Route::get('/production', function () { return "Halaman Production (Dalam Pengembangan)"; });
    });

    // --- RUTE KHUSUS SUPER ADMIN SAJA ---
    Route::middleware('role:super_admin')->group(function () {
        // Taruh rute khusus super admin di sini nanti
    });
});
use App\Http\Controllers\RnDfeatureController;

// Redirect halaman utama langsung ke daftar RnD
Route::get('/', function () {
    return redirect()->route('rnd.index');
});

// Route Modul RnD
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
    return 'Halaman Resources / Man Power belum dibuat';
})->name('man-power.index');
