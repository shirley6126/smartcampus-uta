<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\TurnoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cola FIFO — Turnos
    Route::resource('turnos', TurnoController::class)->only(['index', 'create', 'store']);
    Route::post('/turnos/llamar-siguiente', [TurnoController::class, 'llamarSiguiente'])->name('turnos.llamar');
    Route::patch('/turnos/{turno}/cancelar', [TurnoController::class, 'cancelar'])->name('turnos.cancelar');

    // Lista Doble — Tramites
    Route::resource('tramites', TramiteController::class)->only(['index', 'create', 'store', 'show']);
    Route::patch('/tramites/{tramite}/estado', [TramiteController::class, 'actualizarEstado'])->name('tramites.estado');

    // Pila LIFO — Historial
    Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');

    // Arbol — Documentos
    Route::get('/documentos',            [DocumentoController::class, 'index'])->name('documentos.index');
    Route::post('/documentos/categoria', [DocumentoController::class, 'storeCategoria'])->name('documentos.categoria.store');
    Route::post('/documentos/documento', [DocumentoController::class, 'storeDocumento'])->name('documentos.documento.store');

    // Grafo — Rutas
    Route::get('/rutas',               [RutaController::class, 'index'])->name('rutas.index');
    Route::post('/rutas/calcular',     [RutaController::class, 'calcularRuta'])->name('rutas.calcular');
    Route::post('/rutas/punto',        [RutaController::class, 'storePunto'])->name('rutas.punto.store');
    Route::post('/rutas/conexion',     [RutaController::class, 'storeConexion'])->name('rutas.conexion.store');
    Route::delete('/rutas/punto/{punto}',       [RutaController::class, 'destroyPunto'])->name('rutas.punto.destroy');
    Route::delete('/rutas/conexion/{conexion}', [RutaController::class, 'destroyConexion'])->name('rutas.conexion.destroy');

    // Admin — solo rol admin
    Route::middleware('rol:admin')->group(function () {
        Route::get('/admin/usuarios',                    [AdminController::class, 'usuarios'])->name('admin.usuarios');
        Route::patch('/admin/usuarios/{user}/rol',       [AdminController::class, 'cambiarRol'])->name('admin.usuarios.rol');
        Route::delete('/admin/usuarios/{user}/eliminar', [AdminController::class, 'eliminarUsuario'])->name('admin.usuarios.eliminar');
    });
});

require __DIR__.'/auth.php';