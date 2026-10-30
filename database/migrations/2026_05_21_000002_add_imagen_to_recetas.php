<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega campo para imagen escaneada de receta manuscrita.
 * Permite subir foto/scan de la receta que escribió el médico a mano.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recetas', function (Blueprint $table) {
            $table->string('imagen_receta', 255)->nullable()->after('indicaciones_generales');
            $table->string('tipo_receta', 20)->default('digital')->after('imagen_receta');
            // tipo_receta: 'digital' = generada en sistema, 'manuscrita' = escaneada
        });
    }

    public function down(): void
    {
        Schema::table('recetas', function (Blueprint $table) {
            $table->dropColumn(['imagen_receta', 'tipo_receta']);
        });
    }
};
