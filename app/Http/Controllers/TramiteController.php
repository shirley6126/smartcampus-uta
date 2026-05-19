<?php

namespace App\Http\Controllers;

use App\DataStructures\ListaDoble;
use App\Models\Tramite;
use Illuminate\Http\Request;
use App\Services\HistorialService;

class TramiteController extends Controller
{
    /**
     * Carga todos los trámites en la Lista Doblemente Enlazada.
     * Los urgentes van al inicio, los normales al final.
     */
    private function construirLista(): ListaDoble
    {
        $lista = new ListaDoble();

        // Primero cargamos los urgentes → van al inicio de la lista
        $urgentes = Tramite::where('prioridad', 'urgente')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($urgentes as $tramite) {
            $lista->insertarAlInicio($tramite);
        }

        // Luego los normales → van al final
        $normales = Tramite::where('prioridad', 'normal')
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($normales as $tramite) {
            $lista->insertarAlFinal($tramite);
        }

        return $lista;
    }

    /**
     * Panel principal de trámites.
     */
    public function index(Request $request)
    {
        $lista = $this->construirLista();

        // Por defecto mostramos del más reciente al más antiguo (inverso)
        // El funcionario ve primero lo que llegó último
        $inverso  = $request->boolean('inverso', true);
        $tramites = $inverso ? $lista->toArrayInverso() : $lista->toArray();

        // Filtro por estado si se solicita
        if ($request->filled('estado')) {
            $tramites = $lista->filtrarPorEstado($request->estado);
        }

        $stats = [
            'pendientes'  => Tramite::where('estado', 'pendiente')->count(),
            'en_proceso'  => Tramite::where('estado', 'en_proceso')->count(),
            'resueltos'   => Tramite::where('estado', 'resuelto')->count(),
            'rechazados'  => Tramite::where('estado', 'rechazado')->count(),
            'total'       => Tramite::count(),
        ];

        return view('tramites.index', compact('tramites', 'stats', 'inverso'));
    }

    /**
     * Formulario para registrar nuevo trámite.
     */
    public function create()
    {
        return view('tramites.create');
    }

    /**
     * Guarda el trámite → equivale a insertar en la Lista Doble.
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo'      => 'required|string|max:255',
            'tipo'        => 'required|in:matricula,certificado,beca,convalidacion,retiro,otro',
            'descripcion' => 'nullable|string|max:1000',
            'prioridad'   => 'required|in:normal,urgente',
        ]);

        // Generamos el código único del trámite
        $total  = Tramite::whereYear('created_at', now()->year)->count() + 1;
        $codigo = 'TR-' . now()->year . '-' . str_pad($total, 3, '0', STR_PAD_LEFT);

        // Se asigna a la variable para poder registrar el ID en el historial
        $tramite = Tramite::create([
            'codigo'      => $codigo,
            'titulo'      => $request->titulo,
            'tipo'        => $request->tipo,
            'descripcion' => $request->descripcion,
            'prioridad'   => $request->prioridad,
            'estado'      => 'pendiente',
            'user_id'     => auth()->id(),
        ]);

        // Registro en el Historial de actividades
        HistorialService::registrar(
            "Trámite {$codigo} registrado: {$request->titulo}",
            'tramites', 'Tramite', $tramite->id,
            null,
            ['estado' => 'pendiente', 'tipo' => $request->tipo]
        );

        return redirect()->route('tramites.index')
            ->with('success', "Trámite {$codigo} registrado correctamente.");
    }

    /**
     * Ver detalle de un trámite específico.
     */
    public function show(Tramite $tramite)
    {
        // Buscamos el anterior y siguiente en la lista para la navegación
        $lista    = $this->construirLista();
        $todos    = $lista->toArray();
        $posicion = array_search($tramite->id, array_column($todos, 'id'));

        $anterior  = $posicion > 0 ? $todos[$posicion - 1] : null;
        $siguiente = $posicion < count($todos) - 1 ? $todos[$posicion + 1] : null;

        return view('tramites.show', compact('tramite', 'anterior', 'siguiente'));
    }

    /**
     * Actualiza el estado del trámite (flujo de trabajo).
     */
    public function actualizarEstado(Request $request, Tramite $tramite)
    {
        $request->validate([
            'estado'        => 'required|in:pendiente,en_proceso,resuelto,rechazado',
            'observaciones' => 'nullable|string|max:500',
        ]);

        // Guardamos el estado original antes de la actualización
        $estadoAnterior = $tramite->estado;

        $tramite->update([
            'estado'           => $request->estado,
            'observaciones'    => $request->observaciones,
            'fecha_resolucion' => in_array($request->estado, ['resuelto', 'rechazado']) ? now() : null,
        ]);

        // Registro en el Historial de actividades
        HistorialService::registrar(
            "Trámite {$tramite->codigo} cambió de estado",
            'tramites', 'Tramite', $tramite->id,
            ['estado' => $estadoAnterior],
            ['estado' => $request->estado]
        );

        return redirect()->route('tramites.show', $tramite)
            ->with('success', 'Estado del trámite actualizado.');
    }
}