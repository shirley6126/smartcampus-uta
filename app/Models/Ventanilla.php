<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ventanilla extends Model
{
    protected $fillable = ['nombre', 'activa', 'es_actual'];

    protected $casts = ['activa' => 'boolean', 'es_actual' => 'boolean'];
}