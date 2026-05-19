<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();

            // Tipo del documento
            $table->enum('tipo', ['pdf', 'word', 'excel', 'imagen', 'enlace', 'otro'])
                  ->default('otro');

            $table->string('url_externa')->nullable(); // Enlace a Drive, repositorio, etc.

            // En qué rama del árbol vive este documento
            $table->foreignId('categoria_id')
                  ->constrained('categorias_documentos')
                  ->onDelete('cascade');

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};