<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManPowerController;
use App\Http\Controllers\MachinePowerController;
use App\Http\Controllers\WaitingResourceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('man-power.index');
});

// --- MAN POWER ROUTES ---
Route::get('/man-power', [ManPowerController::class, 'index'])->name('man-power.index');
Route::get('/man-power/create', [ManPowerController::class, 'create'])->name('man-power.create');
Route::post('/man-power', [ManPowerController::class, 'store'])->name('man-power.store');
Route::get('/man-power/{manPower}/edit', [ManPowerController::class, 'edit'])->name('man-power.edit');

// Support kedua jenis nama rute (update standar & update-process agar tidak error lagi)
Route::put('/man-power/{manPower}', [ManPowerController::class, 'update'])->name('man-power.update');
Route::patch('/man-power/{manPower}', [ManPowerController::class, 'update']);
Route::post('/man-power/{manPower}/update-process', [ManPowerController::class, 'update'])->name('man-power.update-process');

Route::delete('/man-power/{manPower}', [ManPowerController::class, 'destroy'])->name('man-power.destroy');
Route::patch('/man-power/{id}/update-status', [ManPowerController::class, 'updateStatus']);


// --- MACHINE POWER ROUTES ---
Route::get('/machine-power', [MachinePowerController::class, 'index'])->name('machine-power.index');
Route::get('/machine-power/create', [MachinePowerController::class, 'create'])->name('machine-power.create');
Route::post('/machine-power', [MachinePowerController::class, 'store'])->name('machine-power.store');
Route::get('/machine-power/{machinePower}/edit', [MachinePowerController::class, 'edit'])->name('machine-power.edit');

Route::put('/machine-power/{machinePower}', [MachinePowerController::class, 'update'])->name('machine-power.update');
Route::patch('/machine-power/{machinePower}', [MachinePowerController::class, 'update']);
Route::delete('/machine-power/{machinePower}', [MachinePowerController::class, 'destroy'])->name('machine-power.destroy');
Route::patch('/machine-power/{id}/update-status', [MachinePowerController::class, 'updateStatus']);

// --- WAITING FOR RESOURCES ROUTES ---
Route::get('/waiting-for-resources', [WaitingResourceController::class, 'index'])->name('waiting-resources.index');
Route::post('/api/waiting-resources/store', [WaitingResourceController::class, 'apiStore'])->name('waiting-resources.api-store');