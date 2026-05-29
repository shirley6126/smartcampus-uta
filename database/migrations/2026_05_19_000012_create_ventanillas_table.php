<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventanillas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');           // Ej: Ventanilla 1
            $table->boolean('activa')->default(true);
            $table->boolean('es_actual')->default(false); // Cuál es el turno actual
            $table->timestamps();
        });

        // Insertamos 3 ventanillas por defecto
        DB::table('ventanillas')->insert([
            ['nombre' => 'Ventanilla 1', 'activa' => true,  'es_actual' => true,  'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ventanilla 2', 'activa' => true,  'es_actual' => false, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Ventanilla 3', 'activa' => true,  'es_actual' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('ventanillas');
    }
};