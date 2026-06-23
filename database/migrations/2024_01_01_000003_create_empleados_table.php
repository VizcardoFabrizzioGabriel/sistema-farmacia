<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->id('id_empleado');
            $table->unsignedBigInteger('id_usuario')->unique();
            $table->date('fecha_ingreso');
            $table->string('turno', 20);
            $table->string('numero_colegiatura', 50)->nullable();
            $table->integer('caja_asignada')->nullable();
            $table->string('cargo', 50)->nullable();
            $table->timestamps();

            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};