<?php

use App\Http\Controllers\TurnoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Módulo Turnos de Atención (Cola FIFO)
    Route::resource('turnos', TurnoController::class)->only(['index', 'create', 'store']);
    Route::post('/turnos/llamar-siguiente', [TurnoController::class, 'llamarSiguiente'])->name('turnos.llamar');
    Route::patch('/turnos/{turno}/cancelar', [TurnoController::class, 'cancelar'])->name('turnos.cancelar');
});

require __DIR__.'/auth.php';