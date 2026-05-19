<?php

namespace App\DataStructures;

/**
 * Lista Doblemente Enlazada (Doubly Linked List).
 * 
 * Ventaja sobre la lista simple: se puede recorrer hacia adelante Y hacia atrás.
 * Aplicación en SmartCampus: historial de trámites navegable
 * (el funcionario puede ver el trámite anterior y el siguiente fácilmente).
 * 
 *  ←→  cabeza ←→ nodo1 ←→ nodo2 ←→ ... ←→ cola  ←→
 */
class ListaDoble
{
    private ?NodoDoble $cabeza;  // Primer nodo (trámite más antiguo)
    private ?NodoDoble $cola;    // Último nodo  (trámite más reciente)
    private int $tamanio;

    public function __construct()
    {
        $this->cabeza  = null;
        $this->cola    = null;
        $this->tamanio = 0;
    }

    /**
     * Inserta un nuevo elemento al FINAL de la lista.
     * Los trámites se agregan al final (orden cronológico).
     * Complejidad: O(1) porque tenemos referencia directa a la cola.
     */
    public function insertarAlFinal(mixed $dato): void
    {
        $nuevoNodo = new NodoDoble($dato);

        if ($this->estaVacia()) {
            $this->cabeza = $nuevoNodo;
            $this->cola   = $nuevoNodo;
        } else {
            // El nuevo nodo apunta hacia atrás al nodo actual de la cola
            $nuevoNodo->anterior = $this->cola;
            // El nodo actual de la cola apunta hacia adelante al nuevo nodo
            $this->cola->siguiente = $nuevoNodo;
            // La cola de la lista ahora es el nuevo nodo
            $this->cola = $nuevoNodo;
        }

        $this->tamanio++;
    }

    /**
     * Inserta al INICIO (para trámites urgentes que van primero).
     * Complejidad: O(1).
     */
    public function insertarAlInicio(mixed $dato): void
    {
        $nuevoNodo = new NodoDoble($dato);

        if ($this->estaVacia()) {
            $this->cabeza = $nuevoNodo;
            $this->cola   = $nuevoNodo;
        } else {
            $nuevoNodo->siguiente    = $this->cabeza;
            $this->cabeza->anterior  = $nuevoNodo;
            $this->cabeza            = $nuevoNodo;
        }

        $this->tamanio++;
    }

    /**
     * Recorre la lista de CABEZA a COLA (más antiguo → más reciente).
     */
    public function toArray(): array
    {
        $resultado = [];
        $actual    = $this->cabeza;

        while ($actual !== null) {
            $resultado[] = $actual->dato;
            $actual = $actual->siguiente; // Avanzamos →
        }

        return $resultado;
    }

    /**
     * Recorre la lista de COLA a CABEZA (más reciente → más antiguo).
     * Esto es único de la lista doble — imposible con lista simple.
     */
    public function toArrayInverso(): array
    {
        $resultado = [];
        $actual    = $this->cola; // Empezamos desde el final

        while ($actual !== null) {
            $resultado[] = $actual->dato;
            $actual = $actual->anterior; // Retrocedemos ←
        }

        return $resultado;
    }

    /**
     * Busca trámites por estado y retorna un array filtrado.
     * Complejidad: O(n) — recorre toda la lista.
     */
    public function filtrarPorEstado(string $estado): array
    {
        $resultado = [];
        $actual    = $this->cabeza;

        while ($actual !== null) {
            if ($actual->dato->estado === $estado) {
                $resultado[] = $actual->dato;
            }
            $actual = $actual->siguiente;
        }

        return $resultado;
    }

    public function estaVacia(): bool { return $this->cabeza === null; }
    public function tamanio(): int    { return $this->tamanio; }
    public function obtenerCabeza()   { return $this->cabeza?->dato; }
    public function obtenerCola()     { return $this->cola?->dato; }
}