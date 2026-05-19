<?php

namespace App\DataStructures;

/**
 * Árbol N-ario para organizar categorías de documentos jerárquicamente.
 *
 * Estructura: cada nodo puede tener cero o más hijos.
 * El nodo raíz es una categoría virtual "Documentos UTA".
 *
 * Recorridos implementados:
 *   - Profundidad DFS (pre-order): padre → hijos de izquierda a derecha
 *   - Anchura BFS: nivel por nivel, de arriba hacia abajo
 *
 * Aplicación real: el árbol DFS muestra la jerarquía completa de categorías.
 * El BFS permite ver todos los elementos de un mismo nivel juntos.
 */
class ArbolDocumental
{
    private ?NodoArbol $raiz;

    public function __construct()
    {
        $this->raiz = null;
    }

    /**
     * Construye el árbol desde un array de categorías de la BD.
     * Usamos un mapa id → nodo para insertar hijos eficientemente.
     * Complejidad: O(n) — recorremos la lista una sola vez.
     *
     * @param array $categorias  Colección de CategoriaDocumento
     */
    public function construir(array $categorias): void
    {
        if (empty($categorias)) return;

        // Nodo raíz virtual que agrupa todo
        $nodosMap = []; // mapa: id => NodoArbol

        // Primera pasada: creamos todos los nodos
        foreach ($categorias as $categoria) {
            $nodosMap[$categoria->id] = new NodoArbol($categoria);
        }

        // Segunda pasada: enlazamos padres con hijos
        foreach ($categorias as $categoria) {
            $nodoActual = $nodosMap[$categoria->id];

            if ($categoria->parent_id === null) {
                // Sin padre → es nodo raíz (o uno de los varios nodos raíz)
                // Lo colgamos de una raíz virtual
                if ($this->raiz === null) {
                    // Creamos la raíz virtual
                    $this->raiz = new NodoArbol((object)[
                        'id'     => 0,
                        'nombre' => 'Documentos UTA',
                        'icono'  => '🏛️',
                        'color'  => 'indigo',
                        'parent_id' => null,
                    ]);
                }
                $nodoActual->nivel = 1;
                $this->raiz->agregarHijo($nodoActual);
            } else {
                // Tiene padre → lo colgamos del nodo padre correspondiente
                if (isset($nodosMap[$categoria->parent_id])) {
                    $nodoPadre = $nodosMap[$categoria->parent_id];
                    $nodoActual->nivel = $nodoPadre->nivel + 1;
                    $nodoPadre->agregarHijo($nodoActual);
                }
            }
        }
    }

    /**
     * Recorrido en Profundidad — DFS Pre-order.
     * Visita: nodo actual → luego sus hijos de izquierda a derecha.
     * Resultado: la jerarquía completa en orden natural (como un explorador de archivos).
     */
    public function recorridoProfundidad(): array
    {
        $resultado = [];
        $this->dfsRecursivo($this->raiz, $resultado);
        return $resultado;
    }

    /**
     * Función auxiliar recursiva para el DFS.
     * La recursividad es el mecanismo natural para recorrer árboles.
     */
    private function dfsRecursivo(?NodoArbol $nodo, array &$resultado): void
    {
        if ($nodo === null) return;

        $resultado[] = $nodo; // Visitamos el nodo actual primero (pre-order)

        // Luego visitamos cada hijo recursivamente
        foreach ($nodo->hijos as $hijo) {
            $this->dfsRecursivo($hijo, $resultado);
        }
    }

    /**
     * Recorrido en Anchura — BFS (Breadth First Search).
     * Visita nivel por nivel: raíz, luego nivel 1, luego nivel 2, etc.
     * Usa una cola interna para procesar nodos en orden.
     */
    public function recorridoAnchura(): array
    {
        if ($this->raiz === null) return [];

        $resultado = [];
        $cola      = [$this->raiz]; // Cola simple de nodos por procesar

        while (!empty($cola)) {
            $nodoActual = array_shift($cola); // Sacamos el primero de la cola
            $resultado[] = $nodoActual;

            // Agregamos todos sus hijos al final de la cola
            foreach ($nodoActual->hijos as $hijo) {
                $cola[] = $hijo;
            }
        }

        return $resultado;
    }

    /**
     * Busca un nodo por nombre en todo el árbol (DFS).
     * Retorna el primer nodo que coincida, o null si no existe.
     */
    public function buscar(string $nombre): ?NodoArbol
    {
        return $this->buscarRecursivo($this->raiz, strtolower($nombre));
    }

    private function buscarRecursivo(?NodoArbol $nodo, string $nombre): ?NodoArbol
    {
        if ($nodo === null) return null;

        if (strtolower($nodo->dato->nombre) === $nombre) {
            return $nodo;
        }

        foreach ($nodo->hijos as $hijo) {
            $encontrado = $this->buscarRecursivo($hijo, $nombre);
            if ($encontrado !== null) return $encontrado;
        }

        return null;
    }

    public function getRaiz(): ?NodoArbol { return $this->raiz; }
    public function estaVacio(): bool     { return $this->raiz === null; }
}