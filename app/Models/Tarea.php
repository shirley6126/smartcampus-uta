<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarea extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descripcion',
        'fecha_entrega',
        'materia_id', // Importante incluir la llave foránea para poder enlazarla a la materia
    ];
    
    // Una tarea pertenece a una materia
    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    // Una tarea puede tener muchas entregas
    public function entregas(): HasMany
    {
        return $this->hasMany(Entrega::class);
    }
}