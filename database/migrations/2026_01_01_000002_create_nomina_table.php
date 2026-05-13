<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nomina', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80);
            $table->string('apellido', 80);
            $table->string('cedula', 20)->unique();
            $table->string('cargo', 100);
            $table->string('departamento', 100);
            $table->decimal('salario', 10, 2)->default(0);
            $table->date('fecha_ingreso');
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomina');
    }
};
