<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Turno extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_turno',
        'nombre_solicitante',
        'cedula',
        'motivo',
        'estado',
        'ventanilla',
        'llamado_at',
        'atendido_at',
        'user_id',
    ];

    protected $casts = [
        'llamado_at'  => 'datetime',
        'atendido_at' => 'datetime',
    ];

    // Formato del número de turno: T-001, T-002...
    public function getNumeroFormateadoAttribute(): string
    {
        return 'T-' . str_pad($this->numero_turno, 3, '0', STR_PAD_LEFT);
    }

    // El turno pertenece a un usuario (quien lo registró)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}