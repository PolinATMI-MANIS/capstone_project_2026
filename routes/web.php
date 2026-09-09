<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

// Halaman Login
Route::get('/login', function () {
    return view('login');
})->name('login');

// Proses Login yang mengarah ke Controller
Route::post('/proses-login', [LoginController::class, 'authenticate']);

// Halaman Dashboard (Hanya bisa diakses kalau sudah login)
Route::get('/dashboard', function () {
    return "<h1>Selamat Datang di Dashboard Admin!</h1>";
})->middleware('auth');

Route::post('/logout', [LoginController::class, 'logout']);