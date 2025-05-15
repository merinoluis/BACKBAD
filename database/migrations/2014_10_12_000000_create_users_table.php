<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombreUsuario', 50)->unique();
            $table->string('email', 100)->nullable()->unique();
            $table->string('password', 300);
            $table->string('nombreRol', 50);
            $table->unsignedInteger('idEstado');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('nombreRol')->references('nombreRol')->on('roles');
            $table->foreign('idEstado')->references('idEstado')->on('estado_usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
