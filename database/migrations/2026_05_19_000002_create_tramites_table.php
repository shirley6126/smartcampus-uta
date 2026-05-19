<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tramites', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();      // Ej: TR-2026-001
            $table->string('titulo');                // Título del trámite
            $table->text('descripcion')->nullable(); // Descripción detallada
            
            // Tipo de trámite (lo que el estudiante necesita)
            $table->enum('tipo', [
                'matricula',
                'certificado',
                'beca',
                'convalidacion',
                'retiro',
                'otro',
            ]);

            // Estado del trámite — avanza linealmente
            $table->enum('estado', [
                'pendiente',    // Recién creado, esperando revisión
                'en_proceso',   // Alguien lo está atendiendo
                'resuelto',     // Finalizado con éxito
                'rechazado',    // Fue negado con observación
            ])->default('pendiente');

            $table->enum('prioridad', ['normal', 'urgente'])->default('normal');
            $table->text('observaciones')->nullable(); // Comentarios del funcionario
            $table->timestamp('fecha_resolucion')->nullable();

            // Quién lo solicitó
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramites');
    }
};