<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'estado',
        'user_id',
        'materia_id',
    ];
    
    // La asistencia pertenece a una materia
    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    // La asistencia corresponde a un usuario (estudiante)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}