<?php

namespace App\DataStructures;

/**
 * Implementación propia de una Cola (Queue) usando lista enlazada.
 * 
 * Principio FIFO: First In, First Out
 * El primero que llega es el primero en ser atendido.
 * 
 * Operaciones principales:
 *   encolar()   → agrega al FINAL de la fila
 *   desencolar() → saca del FRENTE de la fila
 */
class Cola
{
    private ?Nodo $frente;  // Apunta al primer elemento (el que se atiende primero)
    private ?Nodo $final;   // Apunta al último elemento (donde se agrega el nuevo)
    private int $tamanio;   // Cantidad de elementos en la cola

    public function __construct()
    {
        $this->frente  = null;
        $this->final   = null;
        $this->tamanio = 0;
    }

    /**
     * Agrega un elemento al FINAL de la cola.
     * Complejidad: O(1) — siempre insertamos al final directamente.
     */
    public function encolar(mixed $dato): void
    {
        $nuevoNodo = new Nodo($dato);

        if ($this->estaVacia()) {
            // Si la cola está vacía, frente y final apuntan al mismo nodo
            $this->frente = $nuevoNodo;
            $this->final  = $nuevoNodo;
        } else {
            // El último nodo actual apunta al nuevo, y final se mueve
            $this->final->siguiente = $nuevoNodo;
            $this->final = $nuevoNodo;
        }

        $this->tamanio++;
    }

    /**
     * Saca y retorna el elemento del FRENTE de la cola.
     * Complejidad: O(1) — siempre sacamos del frente directamente.
     */
    public function desencolar(): mixed
    {
        if ($this->estaVacia()) {
            return null; // No hay nada que sacar
        }

        $dato         = $this->frente->dato; // Guardamos el dato a retornar
        $this->frente = $this->frente->siguiente; // Frente avanza al siguiente

        // Si la cola quedó vacía, final también debe ser null
        if ($this->frente === null) {
            $this->final = null;
        }

        $this->tamanio--;
        return $dato;
    }

    /**
     * Retorna el elemento del frente SIN sacarlo de la cola.
     */
    public function verFrente(): mixed
    {
        return $this->estaVacia() ? null : $this->frente->dato;
    }

    /**
     * Convierte la cola a un array para poder mostrarla en la vista.
     */
    public function toArray(): array
    {
        $resultado = [];
        $actual    = $this->frente;

        while ($actual !== null) {
            $resultado[] = $actual->dato;
            $actual = $actual->siguiente;
        }

        return $resultado;
    }

    public function estaVacia(): bool { return $this->frente === null; }
    public function tamanio(): int    { return $this->tamanio; }
}