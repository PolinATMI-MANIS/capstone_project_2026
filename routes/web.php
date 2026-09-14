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