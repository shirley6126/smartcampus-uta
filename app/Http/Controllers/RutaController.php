<?php

namespace App\Http\Controllers;

use App\DataStructures\Grafo;
use App\Models\ConexionRuta;
use App\Models\PuntoRuta;
use App\Services\HistorialService;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    /**
     * Construye el grafo completo desde la base de datos.
     * Carga todos los vértices (puntos) y aristas (conexiones).
     */
    private function construirGrafo(): Grafo
    {
        $grafo = new Grafo();

        foreach (PuntoRuta::all() as $punto) {
            $grafo->agregarVertice($punto->id, $punto);
        }

        foreach (ConexionRuta::all() as $conexion) {
            $grafo->agregarArista(
                $conexion->punto_origen_id,
                $conexion->punto_destino_id,
                $conexion->distancia_metros
            );
        }

        return $grafo;
    }

    /**
     * Panel principal del módulo de rutas.
     */
    public function index()
    {
        $grafo      = $this->construirGrafo();
        $puntos     = PuntoRuta::orderBy('nombre')->get();
        $conexiones = ConexionRuta::with(['origen', 'destino'])->get();

        // Convertimos puntos a JSON para Leaflet
        $puntosJson = $puntos->map(fn($p) => [
            'id'          => $p->id,
            'nombre'      => $p->nombre,
            'tipo'        => $p->tipo_legible,
            'latitud'     => $p->latitud,
            'longitud'    => $p->longitud,
            'tiene_coords' => $p->latitud && $p->longitud,
        ])->toJson();

        // Convertimos conexiones a JSON para dibujar líneas en el mapa
        $conexionesJson = $conexiones->filter(
            fn($c) => $c->origen->latitud && $c->destino->latitud
        )->map(fn($c) => [
            'origen_lat'  => $c->origen->latitud,
            'origen_lng'  => $c->origen->longitud,
            'destino_lat' => $c->destino->latitud,
            'destino_lng' => $c->destino->longitud,
            'distancia'   => $c->distancia_metros,
        ])->values()->toJson();

        $stats = [
            'vertices' => $grafo->totalVertices(),
            'aristas'  => $conexiones->count(),
            'puntos'   => $puntos->count(),
        ];

        // rutaJson vacío por defecto (no hay ruta calculada aún)
        $rutaJson = '[]';

        return view('rutas.index', compact(
            'puntos', 'conexiones', 'stats',
            'puntosJson', 'conexionesJson', 'rutaJson'
        ));
    }

    /**
     * Calcula la ruta óptima entre dos puntos.
     * Dijkstra → menor distancia en metros.
     * BFS      → menor número de paradas intermedias.
     */
    public function calcularRuta(Request $request)
    {
        $request->validate([
            'origen_id'  => 'required|exists:puntos_ruta,id',
            'destino_id' => 'required|exists:puntos_ruta,id|different:origen_id',
            'algoritmo'  => 'required|in:dijkstra,bfs',
        ]);

        $grafo   = $this->construirGrafo();
        $origen  = PuntoRuta::find($request->origen_id);
        $destino = PuntoRuta::find($request->destino_id);

        $resultado       = null;
        $camino          = [];
        $puntosDelCamino = [];
        $rutaJson        = '[]';

        // ── Ejecutar el algoritmo seleccionado ──
        if ($request->algoritmo === 'dijkstra') {

            $resultado = $grafo->dijkstra($request->origen_id, $request->destino_id);
            if ($resultado) {
                $camino = $resultado['camino'];
            }

        } else {
            // BFS
            $camino = $grafo->bfs($request->origen_id, $request->destino_id) ?? [];

            if (!empty($camino)) {
                // BFS no calcula distancia — la calculamos recorriendo las aristas
                $distanciaTotal = 0;

                for ($i = 0; $i < count($camino) - 1; $i++) {
                    $conexion = ConexionRuta::where(function ($q) use ($camino, $i) {
                        $q->where('punto_origen_id', $camino[$i])
                          ->where('punto_destino_id', $camino[$i + 1]);
                    })->orWhere(function ($q) use ($camino, $i) {
                        $q->where('punto_origen_id', $camino[$i + 1])
                          ->where('punto_destino_id', $camino[$i]);
                    })->first();

                    if ($conexion) {
                        $distanciaTotal += $conexion->distancia_metros;
                    }
                }

                $resultado = [
                    'camino'    => $camino,
                    'distancia' => $distanciaTotal,
                    'tiempo'    => (int) ceil($distanciaTotal / 80), // ~80m por minuto caminando
                ];
            }
        }

        // ── Convertir IDs del camino a objetos PuntoRuta ──
        if (!empty($camino)) {
            foreach ($camino as $id) {
                $punto = $grafo->getVertice($id);
                if ($punto) {
                    $puntosDelCamino[] = $punto;
                }
            }

            // JSON para dibujar la ruta en naranja en Leaflet
            $rutaJson = collect($puntosDelCamino)
                ->filter(fn($p) => $p->latitud && $p->longitud)
                ->map(fn($p) => [
                    'lat'    => (float) $p->latitud,
                    'lng'    => (float) $p->longitud,
                    'nombre' => $p->nombre,
                ])
                ->values()
                ->toJson();
        }

        // ── Registrar en el historial ──
        HistorialService::registrar(
            "Ruta calculada: {$origen->nombre} → {$destino->nombre} ({$request->algoritmo})",
            'rutas',
            'Ruta',
            null,
            null,
            [
                'origen'    => $origen->nombre,
                'destino'   => $destino->nombre,
                'algoritmo' => $request->algoritmo,
                'distancia' => $resultado['distancia'] ?? null,
            ]
        );

        // ── Preparar datos para la vista ──
        $puntos     = PuntoRuta::orderBy('nombre')->get();
        $conexiones = ConexionRuta::with(['origen', 'destino'])->get();

        $puntosJson = $puntos->map(fn($p) => [
            'id'           => $p->id,
            'nombre'       => $p->nombre,
            'tipo'         => $p->tipo_legible,
            'latitud'      => $p->latitud,
            'longitud'     => $p->longitud,
            'tiene_coords' => $p->latitud && $p->longitud,
        ])->toJson();

        $conexionesJson = $conexiones->filter(
            fn($c) => $c->origen->latitud && $c->destino->latitud
        )->map(fn($c) => [
            'origen_lat'  => $c->origen->latitud,
            'origen_lng'  => $c->origen->longitud,
            'destino_lat' => $c->destino->latitud,
            'destino_lng' => $c->destino->longitud,
            'distancia'   => $c->distancia_metros,
        ])->values()->toJson();

        $stats = [
            'vertices' => $grafo->totalVertices(),
            'aristas'  => $conexiones->count(),
            'puntos'   => $puntos->count(),
        ];

        return view('rutas.index', compact(
            'puntos', 'conexiones', 'stats',
            'puntosJson', 'conexionesJson', 'rutaJson',
            'origen', 'destino', 'resultado', 'puntosDelCamino'
        ));
    }

    /**
     * Registra un nuevo punto del campus → agrega un vértice al grafo.
     */
    public function storePunto(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'tipo'        => 'required|in:edificio,laboratorio,biblioteca,entrada,parqueadero,area_verde,otro',
            'descripcion' => 'nullable|string|max:255',
            'latitud'     => 'nullable|numeric|between:-90,90',
            'longitud'    => 'nullable|numeric|between:-180,180',
        ]);

        $punto = PuntoRuta::create([
            'nombre'      => $request->nombre,
            'tipo'        => $request->tipo,
            'descripcion' => $request->descripcion,
            'latitud'     => $request->latitud ?: null,
            'longitud'    => $request->longitud ?: null,
            'user_id'     => auth()->id(),
        ]);

        HistorialService::registrar(
            "Punto '{$punto->nombre}' agregado al grafo del campus",
            'rutas',
            'PuntoRuta',
            $punto->id,
            null,
            ['tipo' => $punto->tipo, 'lat' => $punto->latitud, 'lng' => $punto->longitud]
        );

        return redirect()->route('rutas.index')
            ->with('success', "Punto '{$punto->nombre}' registrado correctamente.");
    }

    /**
     * Registra una conexión entre dos puntos → agrega una arista al grafo.
     */
    public function storeConexion(Request $request)
    {
        $request->validate([
            'punto_origen_id'  => 'required|exists:puntos_ruta,id',
            'punto_destino_id' => 'required|exists:puntos_ruta,id|different:punto_origen_id',
            'distancia_metros' => 'required|integer|min:1|max:9999',
            'tiempo_minutos'   => 'required|integer|min:1',
            'es_accesible'     => 'boolean',
        ]);

        // Verificamos que no exista ya esta conexión en ninguna dirección
        $existe = ConexionRuta::where(function ($q) use ($request) {
            $q->where('punto_origen_id', $request->punto_origen_id)
              ->where('punto_destino_id', $request->punto_destino_id);
        })->orWhere(function ($q) use ($request) {
            $q->where('punto_origen_id', $request->punto_destino_id)
              ->where('punto_destino_id', $request->punto_origen_id);
        })->exists();

        if ($existe) {
            return back()->withErrors([
                'punto_destino_id' => 'Ya existe una conexión entre estos dos puntos.',
            ]);
        }

        $conexion = ConexionRuta::create([
            'punto_origen_id'  => $request->punto_origen_id,
            'punto_destino_id' => $request->punto_destino_id,
            'distancia_metros' => $request->distancia_metros,
            'tiempo_minutos'   => $request->tiempo_minutos,
            'es_accesible'     => $request->boolean('es_accesible', true),
            'user_id'          => auth()->id(),
        ]);

        $origen  = PuntoRuta::find($request->punto_origen_id);
        $destino = PuntoRuta::find($request->punto_destino_id);

        HistorialService::registrar(
            "Conexion registrada: {$origen->nombre} ↔ {$destino->nombre} ({$request->distancia_metros}m)",
            'rutas',
            'ConexionRuta',
            $conexion->id,
            null,
            ['distancia' => $request->distancia_metros, 'tiempo' => $request->tiempo_minutos]
        );

        return redirect()->route('rutas.index')
            ->with('success', "Conexion registrada: {$origen->nombre} ↔ {$destino->nombre}.");
    }
    /**
 * Elimina un punto del campus (vértice del grafo).
 * También elimina en cascada todas sus conexiones por el onDelete('cascade').
 */
public function destroyPunto(PuntoRuta $punto)
{
    $nombre = $punto->nombre;
    $punto->delete();

    HistorialService::registrar(
        "Punto '{$nombre}' eliminado del grafo",
        'rutas', 'PuntoRuta', null
    );

    return redirect()->route('rutas.index')
        ->with('success', "Punto '{$nombre}' eliminado correctamente.");
}

/**
 * Elimina una conexión específica (arista del grafo).
 */
public function destroyConexion(ConexionRuta $conexion)
{
    $desc = "{$conexion->origen->nombre} ↔ {$conexion->destino->nombre}";
    $conexion->delete();

    return redirect()->route('rutas.index')
        ->with('success', "Conexion '{$desc}' eliminada.");
}
}