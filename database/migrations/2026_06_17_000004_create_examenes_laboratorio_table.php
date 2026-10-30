<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de Solicitudes de Exámenes de Laboratorio
 * Formato Maxical: checkboxes de exámenes estándar + campo "Otros".
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('examenes_laboratorio')) {
            Schema::create('examenes_laboratorio', function (Blueprint $table) {
                $table->id();
                $table->string('numero_solicitud', 20)->unique(); // EXA-2026-0001
                $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
                $table->foreignId('consulta_id')->nullable()->constrained('consultas')->nullOnDelete();
                $table->foreignId('medico_id')->nullable()->constrained('usuarios')->nullOnDelete();
                $table->foreignId('empleado_id')->nullable()->constrained('nomina')->nullOnDelete();

                // Exámenes estándar (boolean)
                $table->boolean('hematologia_completa')->default(false);
                $table->boolean('glicemia_ayunas')->default(false);
                $table->boolean('urea')->default(false);
                $table->boolean('creatinina')->default(false);
                $table->boolean('colesterol_total')->default(false);
                $table->boolean('trigliceridos')->default(false);
                $table->boolean('examen_orina')->default(false);
                $table->boolean('examen_heces')->default(false);

                // Campo libre para exámenes adicionales
                $table->text('otros')->nullable();

                $table->date('fecha');
                $table->timestamps();
                $table->softDeletes();

                $table->index('paciente_id');
                $table->index('fecha');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('examenes_laboratorio');
    }
};
