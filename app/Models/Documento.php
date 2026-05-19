<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo',
        'url_externa',
        'categoria_id',
        'user_id',
    ];

    // Icono según tipo de archivo
    public function getIconoTipoAttribute(): string
    {
        return match($this->tipo) {
            'pdf'    => '📕',
            'word'   => '📘',
            'excel'  => '📗',
            'imagen' => '🖼️',
            'enlace' => '🔗',
            default  => '📄',
        };
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaDocumento::class, 'categoria_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}