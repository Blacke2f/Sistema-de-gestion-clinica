<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agrega soporte de tipo manuscrito + imagen escaneada a
 * informes_medicos, gastos_medicos y examenes_laboratorio.
 * Segura: usa hasColumn para no duplicar columnas en BD ya existentes.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── informes_medicos ──────────────────────────────────────────────────
        Schema::table('informes_medicos', function (Blueprint $table) {
            if (!Schema::hasColumn('informes_medicos', 'tipo_documento')) {
                $table->enum('tipo_documento', ['digital', 'manuscrita'])
                      ->default('digital')
                      ->after('fecha');
            }
            if (!Schema::hasColumn('informes_medicos', 'imagen_escaneo')) {
                $table->string('imagen_escaneo', 500)
                      ->nullable()
                      ->after('tipo_documento');
            }
        });

        // ── gastos_medicos ────────────────────────────────────────────────────
        Schema::table('gastos_medicos', function (Blueprint $table) {
            if (!Schema::hasColumn('gastos_medicos', 'tipo_documento')) {
                $table->enum('tipo_documento', ['digital', 'manuscrita'])
                      ->default('digital')
                      ->after('fecha');
            }
            if (!Schema::hasColumn('gastos_medicos', 'imagen_escaneo')) {
                $table->string('imagen_escaneo', 500)
                      ->nullable()
                      ->after('tipo_documento');
            }
        });

        // ── examenes_laboratorio ──────────────────────────────────────────────
        Schema::table('examenes_laboratorio', function (Blueprint $table) {
            if (!Schema::hasColumn('examenes_laboratorio', 'tipo_documento')) {
                $table->enum('tipo_documento', ['digital', 'manuscrita'])
                      ->default('digital')
                      ->after('fecha');
            }
            if (!Schema::hasColumn('examenes_laboratorio', 'imagen_escaneo')) {
                $table->string('imagen_escaneo', 500)
                      ->nullable()
                      ->after('tipo_documento');
            }
        });
    }

    public function down(): void
    {
        foreach (['informes_medicos', 'gastos_medicos', 'examenes_laboratorio'] as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (Schema::hasColumn($tabla, 'imagen_escaneo')) {
                    $table->dropColumn('imagen_escaneo');
                }
                if (Schema::hasColumn($tabla, 'tipo_documento')) {
                    $table->dropColumn('tipo_documento');
                }
            });
        }
    }
};
