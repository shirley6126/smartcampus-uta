<?php

namespace App\DataStructures;

/**
 * Nodo para Lista Circular.
 * La diferencia clave con el Nodo simple: en esta lista
 * el último nodo NO apunta a null — apunta de vuelta al primero.
 * Eso forma el ciclo que representa la rotación Round-Robin.
 */
class NodoCircular
{
    public mixed $dato;
    public ?NodoCircular $siguiente; // En lista circular, el último apunta al primero

    public function __construct(mixed $dato)
    {
        $this->dato      = $dato;
        $this->siguiente = null;
    }
}