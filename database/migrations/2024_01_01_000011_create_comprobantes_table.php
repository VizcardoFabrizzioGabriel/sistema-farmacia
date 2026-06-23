<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id('id_comprobante');
            $table->unsignedBigInteger('id_venta')->unique();
            $table->string('tipo', 20);
            $table->string('numero_serie', 50)->unique();
            $table->string('url_pdf', 255)->nullable();
            $table->timestamps();

            $table->foreign('id_venta')->references('id_venta')->on('ventas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};