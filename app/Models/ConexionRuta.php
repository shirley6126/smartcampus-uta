<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConexionRuta extends Model
{
    protected $table = 'conexiones_ruta';

    protected $fillable = [
        'punto_origen_id',
        'punto_destino_id',
        'distancia_metros',
        'tiempo_minutos',
        'es_accesible',
        'user_id',
    ];

    protected $casts = [
        'es_accesible' => 'boolean',
    ];

    public function origen(): BelongsTo
    {
        return $this->belongsTo(PuntoRuta::class, 'punto_origen_id');
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(PuntoRuta::class, 'punto_destino_id');
    }
}