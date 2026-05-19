<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tramite extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'titulo',
        'descripcion',
        'tipo',
        'estado',
        'prioridad',
        'observaciones',
        'fecha_resolucion',
        'user_id',
    ];

    protected $casts = [
        'fecha_resolucion' => 'datetime',
    ];

    // Formato legible del tipo
    public function getTipoLegibleAttribute(): string
    {
        return match($this->tipo) {
            'matricula'      => 'Matrícula',
            'certificado'    => 'Certificado',
            'beca'           => 'Beca',
            'convalidacion'  => 'Convalidación',
            'retiro'         => 'Retiro de materia',
            default          => 'Otro',
        };
    }

    // Color del badge según estado
    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            'pendiente'  => 'bg-yellow-100 text-yellow-800',
            'en_proceso' => 'bg-blue-100 text-blue-800',
            'resuelto'   => 'bg-green-100 text-green-800',
            'rechazado'  => 'bg-red-100 text-red-800',
            default      => 'bg-gray-100 text-gray-800',
        };
    }

    // El trámite pertenece a un usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}