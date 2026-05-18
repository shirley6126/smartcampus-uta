<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entrega extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'contenido_o_link',
        'archivo_path',
        'calificacion',
        'retroalimentacion',
        'fecha_entrega',
        'tarea_id',
        'user_id',
    ];
    
    // Una entrega pertenece a una tarea
    public function tarea(): BelongsTo
    {
        return $this->belongsTo(Tarea::class);
    }

    // Una entrega pertenece a un estudiante (usuario)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}