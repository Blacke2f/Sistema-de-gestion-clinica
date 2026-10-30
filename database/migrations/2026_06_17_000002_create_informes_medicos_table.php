<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de Informes Médicos Ocupacionales
 * Formato Maxical: evaluación por tipo de consulta con resultado de aptitud.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informes_medicos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_informe', 20)->unique(); // INF-2026-0001
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('consulta_id')->nullable()->constrained('consultas')->nullOnDelete();
            $table->foreignId('medico_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->foreignId('empleado_id')->nullable()->constrained('nomina')->nullOnDelete();

            // Datos del trabajador
            $table->string('nombre_empresa', 150)->nullable();
            $table->integer('edad')->nullable();
            $table->string('cargo', 120)->nullable();
            $table->string('antiguedad_cargo', 80)->nullable();

            // Tipo de evaluación
            $table->enum('tipo_consulta', [
                'preempleo',
                'prevacacional',
                'postvacacional',
                'egreso',
                'reubicacion',
                'otros',
            ])->nullable();
            $table->string('tipo_consulta_otros', 120)->nullable(); // descripción si es "otros"

            // Resultado
            $table->enum('aptitud', ['apto', 'no_apto', 'apto_condicionado'])->nullable();

            // Datos clínicos
            $table->text('diagnostico')->nullable();
            $table->text('estudios_paraclinicos')->nullable();
            $table->text('referencia')->nullable();
            $table->text('recomendaciones')->nullable();

            $table->date('fecha');
            $table->timestamps();
            $table->softDeletes();

            $table->index('paciente_id');
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informes_medicos');
    }
};
