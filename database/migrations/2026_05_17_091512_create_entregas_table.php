<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            
            // Contenido de la entrega
            $table->text('contenido_o_link')->nullable(); // Enlace a GitHub, Google Drive o texto de la entrega
            $table->string('archivo_path')->nullable();   // Por si suben un documento (.pdf, .zip) directamente
            
            // Gestión de la calificación
            $table->decimal('calificacion', 4, 2)->nullable(); // Ej: 08.50 o 10.00 (máximo de 4 dígitos, 2 decimales)
            $table->text('retroalimentacion')->nullable();      // Comentarios del docente
            $table->dateTime('fecha_entrega');                 // Cuándo le dio clic el alumno a "Enviar"

            // Relaciones (Llaves foráneas)
            // 1. A qué tarea corresponde esta entrega
            $table->foreignId('tarea_id')->constrained('tareas')->onDelete('cascade');
            
            // 2. Qué estudiante (usuario) hizo la entrega
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entregas');
    }
};