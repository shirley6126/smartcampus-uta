<?php

namespace App\DataStructures;

/**
 * Nodo genérico para usar en listas enlazadas, colas y pilas.
 * Cada nodo guarda un dato y apunta al siguiente nodo.
 */
class Nodo
{
    public mixed $dato;       // El dato que guarda este nodo (puede ser cualquier tipo)
    public ?Nodo $siguiente;  // Referencia al siguiente nodo (null si es el último)

    public function __construct(mixed $dato)
    {
        $this->dato      = $dato;
        $this->siguiente = null; // Por defecto no apunta a nadie
    }
}