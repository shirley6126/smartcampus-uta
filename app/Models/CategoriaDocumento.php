<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaDocumento extends Model
{
    protected $table = 'categorias_documentos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'icono',
        'color',
        'parent_id',
        'user_id',
    ];

    // Relación padre (esta categoría pertenece a otra categoría)
    public function padre(): BelongsTo
    {
        return $this->belongsTo(CategoriaDocumento::class, 'parent_id');
    }

    // Relación hijos (subcategorías)
    public function hijos(): HasMany
    {
        return $this->hasMany(CategoriaDocumento::class, 'parent_id');
    }

    // Documentos dentro de esta categoría
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'categoria_id');
    }

    // Clases de Tailwind según el color
    public function getClasesColorAttribute(): string
    {
        return match($this->color) {
            'blue'   => 'bg-blue-100 text-blue-700 border-blue-200',
            'green'  => 'bg-green-100 text-green-700 border-green-200',
            'purple' => 'bg-purple-100 text-purple-700 border-purple-200',
            'red'    => 'bg-red-100 text-red-700 border-red-200',
            'amber'  => 'bg-amber-100 text-amber-700 border-amber-200',
            'indigo' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            default  => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    }
}