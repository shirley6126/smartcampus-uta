<?php

namespace App\DataStructures;

/**
 * Nodo para Lista Doblemente Enlazada.
 * A diferencia del Nodo simple, este tiene DOS punteros:
 *   - siguiente: apunta al nodo de adelante
 *   - anterior:  apunta al nodo de atrás
 * 
 * Esto permite navegar en AMBAS direcciones por la lista.
 */
class NodoDoble
{
    public mixed $dato;
    public ?NodoDoble $siguiente; // Puntero hacia adelante →
    public ?NodoDoble $anterior;  // Puntero hacia atrás  ←

    public function __construct(mixed $dato)
    {
        $this->dato      = $dato;
        $this->siguiente = null;
        $this->anterior  = null;
    }
}