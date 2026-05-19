<?php

namespace App\DataStructures;

/**
 * Nodo para el Árbol de Categorías de Documentos.
 *
 * A diferencia de los nodos de Cola y Pila (que tenían un solo "siguiente"),
 * el NodoArbol puede tener MÚLTIPLES hijos — es un árbol N-ario.
 *
 * Esto representa perfectamente una jerarquía de carpetas:
 *   Documentos Académicos
 *     └── Certificados
 *           └── Certificado de matrícula
 *           └── Certificado de notas
 *     └── Formularios
 *           └── Formulario de retiro
 */
class NodoArbol
{
    public mixed $dato;    // Objeto CategoriaDocumento
    public array $hijos;   // Array de NodoArbol (hijos de este nodo)
    public int   $nivel;   // Profundidad en el árbol (raíz = 0)

    public function __construct(mixed $dato, int $nivel = 0)
    {
        $this->dato   = $dato;
        $this->hijos  = [];   // Sin hijos al inicio
        $this->nivel  = $nivel;
    }

    /**
     * Agrega un NodoArbol como hijo de este nodo.
     */
    public function agregarHijo(NodoArbol $hijo): void
    {
        $this->hijos[] = $hijo;
    }

    /**
     * Indica si este nodo es una hoja (no tiene hijos = es documento final).
     */
    public function esHoja(): bool
    {
        return empty($this->hijos);
    }
}