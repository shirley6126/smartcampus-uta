<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('cedula', 10)->nullable();
            $table->string('telefono', 15)->nullable();

            // Campos para estudiante
            $table->string('carrera')->nullable();
            $table->integer('semestre')->nullable();
            $table->string('paralelo', 5)->nullable();

            // Campos para empleado
            $table->string('departamento')->nullable();
            $table->string('cargo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfiles');
    }
};