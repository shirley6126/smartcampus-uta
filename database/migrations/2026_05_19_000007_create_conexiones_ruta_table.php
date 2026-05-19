<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cada fila representa una arista (conexión) entre dos puntos del campus
        Schema::create('conexiones_ruta', function (Blueprint $table) {
            $table->id();

            // Los dos extremos de la arista
            $table->foreignId('punto_origen_id')
                  ->constrained('puntos_ruta')->onDelete('cascade');
            $table->foreignId('punto_destino_id')
                  ->constrained('puntos_ruta')->onDelete('cascade');

            $table->integer('distancia_metros');    // Peso de la arista
            $table->integer('tiempo_minutos');      // Tiempo caminando aprox.
            $table->boolean('es_accesible')->default(true); // Ruta accesible

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conexiones_ruta');
    }
};