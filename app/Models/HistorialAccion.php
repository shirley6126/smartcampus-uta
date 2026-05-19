<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialAccion extends Model
{
    protected $table = 'historial_acciones';

    protected $fillable = [
        'accion',
        'modulo',
        'entidad_tipo',
        'entidad_id',
        'datos_anteriores',
        'datos_nuevos',
        'ip',
        'user_id',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos'     => 'array',
    ];

    // Icono según el módulo para mostrar en la vista
    public function getIconoModuloAttribute(): string
    {
        return match($this->modulo) {
            'turnos'   => '🎫',
            'tramites' => '📄',
            'auth'     => '🔐',
            default    => '📋',
        };
    }

    // Color según el módulo
    public function getColorModuloAttribute(): string
    {
        return match($this->modulo) {
            'turnos'   => 'bg-blue-100 text-blue-700',
            'tramites' => 'bg-purple-100 text-purple-700',
            'auth'     => 'bg-green-100 text-green-700',
            default    => 'bg-gray-100 text-gray-700',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}