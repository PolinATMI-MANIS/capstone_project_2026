<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;

// Halaman Login
Route::get('/login', function () {
    return view('login');
})->name('login');

// Proses Login yang mengarah ke Controller
Route::post('/proses-login', [LoginController::class, 'authenticate']);

// Halaman Dashboard (Hanya bisa diakses kalau sudah login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');

Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');