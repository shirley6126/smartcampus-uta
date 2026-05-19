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
     */
    private function construirGrafo(): Grafo
    {
        $grafo = new Grafo();

        // Agrega todos los puntos del campus como vértices
        foreach (PuntoRuta::all() as $punto) {
            $grafo->agregarVertice($punto->id, $punto);
        }

        // Agrega todas las conexiones como aristas con peso = distancia en metros
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

        $stats = [
            'vertices' => $grafo->totalVertices(),
            'aristas'  => $conexiones->count(),
            'puntos'   => $puntos->count(),
        ];

        return view('rutas.index', compact('puntos', 'conexiones', 'stats'));
    }

    /**
     * Calcula la ruta entre dos puntos del campus.
     * Usa Dijkstra para la ruta más corta por distancia.
     * Usa BFS para la ruta con menos paradas.
     */
    public function calcularRuta(Request $request)
    {
        $request->validate([
            'origen_id'  => 'required|exists:puntos_ruta,id',
            'destino_id' => 'required|exists:puntos_ruta,id|different:origen_id',
            'algoritmo'  => 'required|in:dijkstra,bfs',
        ]);

        $grafo     = $this->construirGrafo();
        $origen    = PuntoRuta::find($request->origen_id);
        $destino   = PuntoRuta::find($request->destino_id);
        $resultado = null;
        $camino    = [];
        $puntosDelCamino = [];

        if ($request->algoritmo === 'dijkstra') {
            $resultado = $grafo->dijkstra($request->origen_id, $request->destino_id);
            if ($resultado) {
                $camino = $resultado['camino'];
            }
        } else {
            $camino = $grafo->bfs($request->origen_id, $request->destino_id) ?? [];
            if (!empty($camino)) {
                // Calculamos distancia total del camino BFS
                $distanciaTotal = 0;
                for ($i = 0; $i < count($camino) - 1; $i++) {
                    $conexion = ConexionRuta::where(function ($q) use ($camino, $i) {
                        $q->where('punto_origen_id', $camino[$i])
                          ->where('punto_destino_id', $camino[$i + 1]);
                    })->orWhere(function ($q) use ($camino, $i) {
                        $q->where('punto_origen_id', $camino[$i + 1])
                          ->where('punto_destino_id', $camino[$i]);
                    })->first();

                    if ($conexion) $distanciaTotal += $conexion->distancia_metros;
                }

                $resultado = [
                    'camino'    => $camino,
                    'distancia' => $distanciaTotal,
                    'tiempo'    => (int) ceil($distanciaTotal / 80),
                ];
            }
        }

        // Convertimos IDs del camino a objetos PuntoRuta para la vista
        if (!empty($camino)) {
            foreach ($camino as $id) {
                $puntosDelCamino[] = $grafo->getVertice($id);
            }
        }

        HistorialService::registrar(
            "Ruta calculada: {$origen->nombre} → {$destino->nombre} ({$request->algoritmo})",
            'rutas', 'Ruta', null,
            null,
            ['origen' => $origen->nombre, 'destino' => $destino->nombre, 'algoritmo' => $request->algoritmo]
        );

        $puntos = PuntoRuta::orderBy('nombre')->get();
        $conexiones = ConexionRuta::with(['origen', 'destino'])->get();
        $stats = [
            'vertices' => $grafo->totalVertices(),
            'aristas'  => $conexiones->count(),
            'puntos'   => $puntos->count(),
        ];

        return view('rutas.index', compact(
            'puntos', 'conexiones', 'stats',
            'origen', 'destino', 'resultado', 'puntosDelCamino'
        ));
    }

    /**
     * Registra un nuevo punto del campus (vértice del grafo).
     */
    public function storePunto(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'tipo'        => 'required|in:edificio,laboratorio,biblioteca,entrada,parqueadero,area_verde,otro',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $punto = PuntoRuta::create([
            'nombre'      => $request->nombre,
            'tipo'        => $request->tipo,
            'descripcion' => $request->descripcion,
            'user_id'     => auth()->id(),
        ]);

        HistorialService::registrar(
            "Punto '{$punto->nombre}' agregado al grafo del campus",
            'rutas', 'PuntoRuta', $punto->id
        );

        return redirect()->route('rutas.index')
            ->with('success', "Punto '{$punto->nombre}' registrado correctamente.");
    }

    /**
     * Registra una conexión entre dos puntos (arista del grafo).
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

        // Verificamos que no exista ya esta conexión
        $existe = ConexionRuta::where(function ($q) use ($request) {
            $q->where('punto_origen_id', $request->punto_origen_id)
              ->where('punto_destino_id', $request->punto_destino_id);
        })->orWhere(function ($q) use ($request) {
            $q->where('punto_origen_id', $request->punto_destino_id)
              ->where('punto_destino_id', $request->punto_origen_id);
        })->exists();

        if ($existe) {
            return back()->withErrors(['punto_destino_id' => 'Ya existe una conexión entre estos dos puntos.']);
        }

        ConexionRuta::create([
            'punto_origen_id'  => $request->punto_origen_id,
            'punto_destino_id' => $request->punto_destino_id,
            'distancia_metros' => $request->distancia_metros,
            'tiempo_minutos'   => $request->tiempo_minutos,
            'es_accesible'     => $request->boolean('es_accesible', true),
            'user_id'          => auth()->id(),
        ]);

        return redirect()->route('rutas.index')
            ->with('success', 'Conexión registrada. Arista agregada al grafo.');
    }
}