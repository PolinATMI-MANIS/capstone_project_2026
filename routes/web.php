<?php

use Illuminate\Support\Facades\Route;
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