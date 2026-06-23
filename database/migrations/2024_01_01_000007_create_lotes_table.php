<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id('id_lote');
            $table->unsignedBigInteger('id_producto');
            $table->unsignedBigInteger('id_proveedor');
            $table->string('numero_lote', 50);
            $table->date('fecha_fabricacion');
            $table->date('fecha_vencimiento');
            $table->integer('cantidad_inicial');
            $table->integer('cantidad_actual');
            $table->string('estado', 20)->default('Activo');
            $table->timestamps();

            $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('cascade');
            $table->foreign('id_proveedor')->references('id_proveedor')->on('proveedores')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};