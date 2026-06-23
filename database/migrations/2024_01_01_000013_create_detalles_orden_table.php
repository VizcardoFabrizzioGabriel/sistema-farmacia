<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_orden', function (Blueprint $table) {
            $table->id('id_detalle_orden');
            $table->unsignedBigInteger('id_orden');
            $table->unsignedBigInteger('id_producto');
            $table->integer('cantidad');
            $table->timestamps();

            $table->foreign('id_orden')->references('id_orden')->on('ordenes_compra')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('productos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_orden');
    }
};