<?php

namespace App\Services;

use App\Models\HistorialAccion;

/**
 * Servicio para registrar acciones en la bitácora (Pila LIFO).
 * Se llama desde los controladores cada vez que ocurre algo importante.
 * Así centralizamos toda la lógica de registro en un solo lugar.
 */
class HistorialService
{
    /**
     * Registra una acción en la base de datos.
     * Equivale a "apilar" un nuevo evento en la pila del historial.
     */
    public static function registrar(
        string $accion,
        string $modulo,
        string $entidadTipo,
        ?int   $entidadId      = null,
        ?array $datosAnteriores = null,
        ?array $datosNuevos    = null
    ): void {
        HistorialAccion::create([
            'accion'           => $accion,
            'modulo'           => $modulo,
            'entidad_tipo'     => $entidadTipo,
            'entidad_id'       => $entidadId,
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos'     => $datosNuevos,
            'ip'               => request()->ip(),
            'user_id'          => auth()->id(),
        ]);
    }
}