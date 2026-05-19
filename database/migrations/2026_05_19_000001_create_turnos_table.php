<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turnos', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_turno');              // Número visible: T-001, T-002...
            $table->string('nombre_solicitante');         // Nombre del estudiante/persona
            $table->string('cedula', 10);                // Cédula de identidad
            $table->string('motivo');                    // Por qué pide el turno
            
            // Estado del turno dentro de la cola
            $table->enum('estado', [
                'en_espera',   // Está esperando en la fila
                'en_atencion', // Lo están atendiendo ahora
                'atendido',    // Ya fue atendido
                'cancelado',   // Se fue sin ser atendido
            ])->default('en_espera');
            
            $table->string('ventanilla')->nullable();    // Ventanilla que lo atiende
            $table->timestamp('llamado_at')->nullable(); // Cuándo fue llamado
            $table->timestamp('atendido_at')->nullable();// Cuándo terminó
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turnos');
    }
};