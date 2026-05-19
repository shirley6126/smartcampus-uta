<?php

namespace App\DataStructures;

/**
 * Grafo no dirigido con pesos — representado como Lista de Adyacencia.
 *
 * Representación elegida: Lista de Adyacencia (array asociativo).
 * Justificación: el campus universitario tiene pocos caminos entre muchos puntos
 * (grafo disperso), por lo que la lista de adyacencia consume menos memoria
 * que una matriz de adyacencia y es igual de eficiente para BFS y Dijkstra.
 *
 * Estructura interna:
 *   $adyacencia = [
 *     idPunto => [ [vecino => id, peso => metros], ... ],
 *     ...
 *   ]
 *
 * Algoritmos implementados:
 *   - BFS  → ruta con menos conexiones (saltos)
 *   - Dijkstra → ruta de menor distancia en metros (óptima por peso)
 */
class Grafo
{
    // Lista de adyacencia: verticeId => array de [vecino, peso]
    private array $adyacencia = [];

    // Datos de cada vértice (objeto PuntoRuta)
    private array $vertices = [];

    /**
     * Agrega un vértice (punto del campus) al grafo.
     */
    public function agregarVertice(int $id, mixed $dato): void
    {
        $this->vertices[$id]    = $dato;
        $this->adyacencia[$id] ??= []; // Inicializa lista vacía si no existe
    }

    /**
     * Agrega una arista bidireccional entre dos puntos.
     * Bidireccional porque los caminos del campus se recorren en ambos sentidos.
     *
     * @param int $peso  Distancia en metros entre los dos puntos
     */
    public function agregarArista(int $origenId, int $destinoId, int $peso): void
    {
        // Origen → Destino
        $this->adyacencia[$origenId][] = [
            'vecino' => $destinoId,
            'peso'   => $peso,
        ];

        // Destino → Origen (bidireccional)
        $this->adyacencia[$destinoId][] = [
            'vecino' => $origenId,
            'peso'   => $peso,
        ];
    }

    /**
     * BFS — Ruta con menor número de conexiones (no considera distancia).
     * Útil para saber cuántos "saltos" hay entre dos puntos.
     *
     * Complejidad: O(V + E) donde V = vértices, E = aristas.
     *
     * @return array|null  Array de IDs del camino, o null si no existe ruta
     */
    public function bfs(int $origenId, int $destinoId): ?array
    {
        if (!isset($this->vertices[$origenId]) || !isset($this->vertices[$destinoId])) {
            return null;
        }

        $visitados  = [$origenId => true];
        $padres     = [$origenId => null]; // Para reconstruir el camino
        $cola       = [$origenId];         // Cola del BFS

        while (!empty($cola)) {
            $actual = array_shift($cola); // Sacamos el primero (FIFO)

            if ($actual === $destinoId) {
                return $this->reconstruirCamino($padres, $destinoId);
            }

            foreach ($this->adyacencia[$actual] ?? [] as $arista) {
                $vecino = $arista['vecino'];
                if (!isset($visitados[$vecino])) {
                    $visitados[$vecino] = true;
                    $padres[$vecino]    = $actual;
                    $cola[]             = $vecino;
                }
            }
        }

        return null; // No hay camino
    }

    /**
     * Dijkstra — Ruta de menor distancia total en metros.
     * Es el algoritmo más adecuado para el mapa del campus porque
     * los caminos tienen diferentes distancias (pesos).
     *
     * Complejidad: O((V + E) log V) con cola de prioridad.
     *
     * @return array|null  ['camino' => [...ids], 'distancia' => metros, 'tiempo' => min]
     */
    public function dijkstra(int $origenId, int $destinoId): ?array
    {
        if (!isset($this->vertices[$origenId]) || !isset($this->vertices[$destinoId])) {
            return null;
        }

        // Inicializamos distancias en infinito para todos los vértices
        $distancias = [];
        $padres     = [];
        $visitados  = [];

        foreach ($this->vertices as $id => $dato) {
            $distancias[$id] = PHP_INT_MAX; // "Infinito"
            $padres[$id]     = null;
        }
        $distancias[$origenId] = 0;

        // Cola de prioridad simulada con un array ordenado
        $colaPrioridad = [[$origenId, 0]]; // [verticeId, distanciaAcumulada]

        while (!empty($colaPrioridad)) {
            // Extraemos el vértice con menor distancia acumulada
            usort($colaPrioridad, fn($a, $b) => $a[1] <=> $b[1]);
            [$actual, $distActual] = array_shift($colaPrioridad);

            if (isset($visitados[$actual])) continue;
            $visitados[$actual] = true;

            if ($actual === $destinoId) break; // Llegamos al destino

            foreach ($this->adyacencia[$actual] ?? [] as $arista) {
                $vecino      = $arista['vecino'];
                $nuevaDist   = $distActual + $arista['peso'];

                if ($nuevaDist < $distancias[$vecino]) {
                    $distancias[$vecino] = $nuevaDist;
                    $padres[$vecino]     = $actual;
                    $colaPrioridad[]     = [$vecino, $nuevaDist];
                }
            }
        }

        if ($distancias[$destinoId] === PHP_INT_MAX) return null;

        $camino = $this->reconstruirCamino($padres, $destinoId);

        return [
            'camino'    => $camino,
            'distancia' => $distancias[$destinoId],
            'tiempo'    => (int) ceil($distancias[$destinoId] / 80), // ~80m/min caminando
        ];
    }

    /**
     * Reconstruye el camino desde el array de padres (uso interno).
     */
    private function reconstruirCamino(array $padres, int $destinoId): array
    {
        $camino  = [];
        $actual  = $destinoId;

        while ($actual !== null) {
            array_unshift($camino, $actual); // Insertamos al inicio
            $actual = $padres[$actual] ?? null;
        }

        return $camino;
    }

    /**
     * DFS — Recorre todos los vértices alcanzables desde un punto.
     * Útil para verificar conectividad del grafo del campus.
     */
    public function dfs(int $origenId): array
    {
        $visitados = [];
        $resultado = [];
        $this->dfsRecursivo($origenId, $visitados, $resultado);
        return $resultado;
    }

    private function dfsRecursivo(int $actual, array &$visitados, array &$resultado): void
    {
        $visitados[$actual] = true;
        $resultado[]        = $actual;

        foreach ($this->adyacencia[$actual] ?? [] as $arista) {
            if (!isset($visitados[$arista['vecino']])) {
                $this->dfsRecursivo($arista['vecino'], $visitados, $resultado);
            }
        }
    }

    public function getVertice(int $id): mixed   { return $this->vertices[$id] ?? null; }
    public function getVertices(): array          { return $this->vertices; }
    public function getAdyacencia(): array        { return $this->adyacencia; }
    public function totalVertices(): int          { return count($this->vertices); }
    public function estaVacio(): bool             { return empty($this->vertices); }
}