<?php

namespace App\Http\Controllers;

use App\DataStructures\Pila;
use App\Models\HistorialAccion;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    /**
     * Carga el historial en la Pila y lo muestra.
     * Las acciones más recientes quedan en la cima (LIFO).
     */
    public function index(Request $request)
    {
        $pila = new Pila();

        // Traemos los registros del más antiguo al más reciente
        // para apilarlos: el último en apilarse queda en la cima
        $query = HistorialAccion::with('user')
            ->orderBy('created_at', 'asc'); // Apilamos de viejo a nuevo

        // Filtro opcional por módulo
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        // Filtro por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $acciones = $query->get();

        // Apilamos cada acción — la última en entrar queda en la cima
        foreach ($acciones as $accion) {
            $pila->apilar($accion);
        }

        // toArray() retorna de cima hacia abajo = más reciente primero
        $historial = $pila->toArray();

        // Estadísticas rápidas
        $stats = [
            'total'    => HistorialAccion::count(),
            'hoy'      => HistorialAccion::whereDate('created_at', today())->count(),
            'turnos'   => HistorialAccion::where('modulo', 'turnos')->count(),
            'tramites' => HistorialAccion::where('modulo', 'tramites')->count(),
        ];

        // Para el filtro de usuarios
        $usuarios = \App\Models\User::select('id', 'name')->get();

        return view('historial.index', compact('historial', 'stats', 'usuarios'));
    }
}