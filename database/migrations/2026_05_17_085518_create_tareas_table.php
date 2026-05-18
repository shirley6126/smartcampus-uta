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
    Schema::create('tareas', function (Blueprint $table) {
        $table->id();
        $table->string('titulo');
        $table->text('descripcion')->nullable(); // nullable por si solo suben un título o archivo adjunto
        $table->dateTime('fecha_entrega');       // Incluye hora límite de entrega
        
        // Relación: Una tarea pertenece a una materia
        // onDelete('cascade') borra las tareas si se llega a eliminar la materia
        $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade'); 
        
        $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
