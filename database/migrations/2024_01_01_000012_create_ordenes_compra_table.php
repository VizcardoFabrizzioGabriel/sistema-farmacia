<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->id('id_orden');
            $table->unsignedBigInteger('id_empleado');
            $table->unsignedBigInteger('id_proveedor');
            $table->date('fecha_emision');
            $table->string('estado', 20)->default('Borrador');
            $table->decimal('total_estimado', 10, 2);
            $table->timestamps();

            $table->foreign('id_empleado')->references('id_empleado')->on('empleados')->onDelete('cascade');
            $table->foreign('id_proveedor')->references('id_proveedor')->on('proveedores')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_compra');
    }
};