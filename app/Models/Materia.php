<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    use HasFactory;

    // Campos que permitimos llenar desde los formularios
    protected $fillable = [
        'codigo_materia',
        'nombre',
        'nivel',
        'paralelo',
    ];

    // Una materia tiene muchas tareas
    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }

    // Una materia tiene muchos registros de asistencia
    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }
}