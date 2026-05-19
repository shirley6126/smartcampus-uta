<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->string('icono', 10)->default(' '); // Emoji para la vista
            $table->string('color', 20)->default('blue'); // Color del nodo

            // Clave foránea a sí misma → esto forma el árbol
            // parent_id = null significa que es nodo raíz (sin padre)
            $table->foreignId('parent_id')
                  ->nullable()
                  ->constrained('categorias_documentos')
                  ->onDelete('cascade');

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_documentos');
    }
};