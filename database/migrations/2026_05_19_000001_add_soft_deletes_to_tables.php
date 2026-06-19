<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega soft deletes a las tablas principales.
 * Los registros eliminados quedan en BD con deleted_at != null.
 * Esto garantiza trazabilidad y cumplimiento con regulaciones de datos médicos.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Pacientes
        Schema::table('pacientes', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        // Consultas
        Schema::table('consultas', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        // Nómina
        Schema::table('nomina', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        // Inventario
        Schema::table('inventario', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes',  fn($t) => $t->dropSoftDeletes());
        Schema::table('consultas',  fn($t) => $t->dropSoftDeletes());
        Schema::table('nomina',     fn($t) => $t->dropSoftDeletes());
        Schema::table('inventario', fn($t) => $t->dropSoftDeletes());
    }
};
