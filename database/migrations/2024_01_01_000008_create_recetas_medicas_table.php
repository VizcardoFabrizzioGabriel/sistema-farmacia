<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recetas_medicas', function (Blueprint $table) {
            $table->id('id_receta');
            $table->string('codigo_receta', 50)->unique();
            $table->string('dni_medico', 15);
            $table->string('nombre_medico', 150);
            $table->date('fecha_emision');
            $table->string('url_imagen', 255)->nullable();
            $table->boolean('estado_validacion')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recetas_medicas');
    }
};