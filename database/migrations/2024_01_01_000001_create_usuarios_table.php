<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario');
            $table->unsignedBigInteger('id_rol');
            $table->string('dni', 15)->unique();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('email', 150)->unique();
            $table->string('password_hash', 255);
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('id_rol')->references('id_rol')->on('roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};