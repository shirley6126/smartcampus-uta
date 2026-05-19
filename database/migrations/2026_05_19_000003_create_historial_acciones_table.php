<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_acciones', function (Blueprint $table) {
            $table->id();
            $table->string('accion');          // Ej: "Turno T-001 llamado"
            $table->string('modulo');          // turnos | tramites | auth
            $table->string('entidad_tipo');    // Turno | Tramite | User
            $table->unsignedBigInteger('entidad_id')->nullable(); // ID del registro afectado
            $table->json('datos_anteriores')->nullable(); // Estado antes del cambio
            $table->json('datos_nuevos')->nullable();     // Estado después del cambio
            $table->string('ip')->nullable();             // IP de quien hizo la acción
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_acciones');
    }
};