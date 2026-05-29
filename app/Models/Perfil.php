<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Perfil extends Model
{
     protected $table = 'perfiles';
     
    protected $fillable = [
        'user_id',
        'cedula',
        'telefono',
        'carrera',
        'semestre',
        'paralelo',
        'departamento',
        'cargo',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}