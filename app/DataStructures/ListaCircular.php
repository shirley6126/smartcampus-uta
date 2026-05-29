<?php

namespace App\DataStructures;

/**
 * Lista Circular (Circular Linked List).
 *
 * Propiedad clave: el último nodo apunta de vuelta al primero,
 * formando un ciclo sin fin. Nunca hay un nodo con siguiente = null.
 *
 * Aplicación en SmartCampus: rotación Round-Robin de ventanillas.
 * Cada vez que se llama al siguiente turno, la lista avanza
 * automáticamente a la siguiente ventanilla. Al llegar al final,
 * regresa a la primera — ciclo infinito.
 *
 *  Ventanilla 1 → Ventanilla 2 → Ventanilla 3 → (vuelve a Ventanilla 1)
 */
class ListaCircular
{
    private ?NodoCircular $cabeza;  // Primer nodo de la lista
    private ?NodoCircular $actual;  // Ventanilla que está atendiendo AHORA
    private int $tamanio;

    public function __construct()
    {
        $this->cabeza  = null;
        $this->actual  = null;
        $this->tamanio = 0;
    }

    /**
     * Agrega una ventanilla al final de la lista circular.
     * Después de insertar, el último nodo apunta de vuelta a la cabeza.
     */
    public function agregar(mixed $dato): void
    {
        $nuevoNodo = new NodoCircular($dato);

        if ($this->estaVacia()) {
            $this->cabeza  = $nuevoNodo;
            $this->actual  = $nuevoNodo;
            // Punto clave: se apunta a sí mismo → ya es circular
            $nuevoNodo->siguiente = $nuevoNodo;
        } else {
            // Encontramos el último nodo (el que apunta a la cabeza)
            $ultimo = $this->cabeza;
            while ($ultimo->siguiente !== $this->cabeza) {
                $ultimo = $ultimo->siguiente;
            }

            // El nuevo nodo apunta a la cabeza (mantiene el ciclo)
            $nuevoNodo->siguiente = $this->cabeza;
            // El antiguo último ahora apunta al nuevo nodo
            $ultimo->siguiente = $nuevoNodo;
        }

        $this->tamanio++;
    }

    /**
     * Avanza al siguiente nodo y lo retorna — operación Round-Robin.
     * Complejidad: O(1) — solo movemos el puntero actual.
     */
    public function avanzar(): mixed
    {
        if ($this->estaVacia()) return null;

        $this->actual = $this->actual->siguiente;
        return $this->actual->dato;
    }

    /**
     * Retorna el dato del nodo actual SIN avanzar.
     */
    public function verActual(): mixed
    {
        return $this->estaVacia() ? null : $this->actual->dato;
    }

    /**
     * Posiciona el puntero actual en el nodo que tenga ese nombre.
     * Sirve para restaurar el estado desde la BD.
     * Complejidad: O(n)
     */
    public function posicionarEn(string $nombre): void
    {
        if ($this->estaVacia()) return;

        $nodo = $this->cabeza;
        $intentos = 0;

        // Recorremos máximo n veces para evitar loop infinito
        while ($intentos < $this->tamanio) {
            if ($nodo->dato === $nombre) {
                $this->actual = $nodo;
                return;
            }
            $nodo = $nodo->siguiente;
            $intentos++;
        }
    }

    /**
     * Convierte la lista a array para mostrarla en la vista.
     * Recorre exactamente n nodos (un ciclo completo).
     */
    public function toArray(): array
    {
        if ($this->estaVacia()) return [];

        $resultado = [];
        $nodo      = $this->cabeza;
        $intentos  = 0;

        while ($intentos < $this->tamanio) {
            $resultado[] = $nodo->dato;
            $nodo = $nodo->siguiente;
            $intentos++;
        }

        return $resultado;
    }

    public function estaVacia(): bool { return $this->cabeza === null; }
    public function tamanio(): int    { return $this->tamanio; }
}