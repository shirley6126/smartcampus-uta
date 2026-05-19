<?php

namespace App\DataStructures;

/**
 * Implementación propia de una Pila (Stack) usando lista enlazada.
 *
 * Principio LIFO: Last In, First Out
 * La última acción registrada es la primera en mostrarse.
 * Ideal para bitácora de acciones: siempre vemos lo más reciente arriba.
 *
 * Operaciones:
 *   apilar()   → agrega encima (push)
 *   desapilar() → saca de arriba (pop)
 *   verCima()  → mira sin sacar (peek)
 */
class Pila
{
    private ?Nodo $cima;  // El elemento más reciente (top of stack)
    private int $tamanio;

    public function __construct()
    {
        $this->cima    = null;
        $this->tamanio = 0;
    }

    /**
     * Agrega un elemento en la CIMA de la pila (push).
     * Complejidad: O(1) — siempre insertamos arriba.
     */
    public function apilar(mixed $dato): void
    {
        $nuevoNodo = new Nodo($dato);

        // El nuevo nodo apunta al que era la cima anterior
        $nuevoNodo->siguiente = $this->cima;

        // El nuevo nodo ahora ES la cima
        $this->cima = $nuevoNodo;

        $this->tamanio++;
    }

    /**
     * Saca y retorna el elemento de la CIMA (pop).
     * Complejidad: O(1).
     */
    public function desapilar(): mixed
    {
        if ($this->estaVacia()) {
            return null;
        }

        $dato       = $this->cima->dato;     // Guardamos lo que vamos a retornar
        $this->cima = $this->cima->siguiente; // La cima baja al siguiente nodo
        $this->tamanio--;

        return $dato;
    }

    /**
     * Mira la cima SIN sacarla (peek).
     */
    public function verCima(): mixed
    {
        return $this->estaVacia() ? null : $this->cima->dato;
    }

    /**
     * Convierte la pila a array (de cima hacia abajo = más reciente primero).
     * Esto es la "bitácora": lo último que pasó aparece primero.
     */
    public function toArray(): array
    {
        $resultado = [];
        $actual    = $this->cima;

        while ($actual !== null) {
            $resultado[] = $actual->dato;
            $actual = $actual->siguiente;
        }

        return $resultado;
    }

    public function estaVacia(): bool { return $this->cima === null; }
    public function tamanio(): int    { return $this->tamanio; }
}