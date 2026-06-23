<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id('id_venta');
            $table->unsignedBigInteger('id_cliente')->nullable();
            $table->unsignedBigInteger('id_empleado');
            $table->unsignedBigInteger('id_receta')->nullable();
            $table->timestamp('fecha_hora');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuestos', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('metodo_pago', 50);
            $table->string('estado', 20)->default('Pendiente');
            $table->string('stripe_payment_id', 100)->nullable();
            $table->timestamps();

            $table->foreign('id_cliente')->references('id_cliente')->on('clientes')->onDelete('set null');
            $table->foreign('id_empleado')->references('id_empleado')->on('empleados')->onDelete('cascade');
            $table->foreign('id_receta')->references('id_receta')->on('recetas_medicas')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};