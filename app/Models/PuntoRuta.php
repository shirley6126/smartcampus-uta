<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PuntoRuta extends Model
{
    protected $table = 'puntos_ruta';

    protected $fillable = [
        'nombre', 'descripcion', 'tipo', 'latitud',
    'longitud', 'user_id' ,

    ];

    public function getTipoLegibleAttribute(): string
    {
        return match($this->tipo) {
            'edificio'     => 'Edificio',
            'laboratorio'  => 'Laboratorio',
            'biblioteca'   => 'Biblioteca',
            'entrada'      => 'Entrada',
            'parqueadero'  => 'Parqueadero',
            'area_verde'   => 'Área verde',
            default        => 'Otro',
        };
    }

    public function conexionesOrigen(): HasMany
    {
        return $this->hasMany(ConexionRuta::class, 'punto_origen_id');
    }

    public function conexionesDestino(): HasMany
    {
        return $this->hasMany(ConexionRuta::class, 'punto_destino_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}