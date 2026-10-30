<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de Gastos Médicos
 * Registra los gastos de atención médica vinculados a un paciente/consulta.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('gastos_medicos')) {
            Schema::create('gastos_medicos', function (Blueprint $table) {
                $table->id();
                $table->string('numero_gasto', 20)->unique(); // GAS-2026-0001
                $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
                $table->foreignId('consulta_id')->nullable()->constrained('consultas')->nullOnDelete();
                $table->foreignId('medico_id')->nullable()->constrained('usuarios')->nullOnDelete();
                $table->foreignId('empleado_id')->nullable()->constrained('nomina')->nullOnDelete();

                $table->string('nombre_empresa', 150)->nullable();
                $table->date('fecha');
                $table->text('descripcion')->nullable(); // descripción del gasto / motivo

                $table->timestamps();
                $table->softDeletes();

                $table->index('paciente_id');
                $table->index('fecha');
            });
        }

        // Tabla de ítems/líneas del gasto
        if (!Schema::hasTable('gasto_medico_items')) {
            Schema::create('gasto_medico_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('gasto_medico_id')->constrained('gastos_medicos')->cascadeOnDelete();
                $table->string('concepto', 200);
                $table->decimal('monto', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gasto_medico_items');
        Schema::dropIfExists('gastos_medicos');
    }
};
