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
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            
            // Campos personalizados para las materias de la UTA
            $table->string('codigo_materia')->unique(); // Ej: UTA-FISE-SE-03
            $table->string('nombre');                  // Ej: Estructuras de Datos
            $table->integer('nivel');                   // Ej: 3
            $table->string('paralelo', 10);             // Ej: A
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};