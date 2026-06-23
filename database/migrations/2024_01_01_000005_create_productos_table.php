<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id('id_producto');
            $table->unsignedBigInteger('id_categoria');
            $table->string('codigo_barras', 50)->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio_venta', 10, 2);
            $table->boolean('es_controlado')->default(false);
            $table->boolean('requiere_receta')->default(false);
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo');
            $table->timestamps();

            $table->foreign('id_categoria')->references('id_categoria')->on('categorias')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};