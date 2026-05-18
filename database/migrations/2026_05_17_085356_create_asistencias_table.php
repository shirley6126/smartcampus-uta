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
    Schema::create('asistencias', function (Blueprint $table) {
        $table->id();
        $table->date('fecha'); // El día de la clase
        
        // El estado puede ser: 'presente', 'ausente', 'atraso'
        $table->enum('estado', ['presente', 'ausente', 'atraso'])->default('presente'); 
        
        // Relaciones clave
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');       // El estudiante
        $table->foreignId('materia_id')->constrained('materias')->onDelete('cascade'); // La materia
        
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
