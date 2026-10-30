<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de Recetas Médicas
 * Cada receta pertenece a una consulta y tiene múltiples ítems de medicamentos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recetas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_receta', 20)->unique(); // REC-2026-0001
            $table->foreignId('consulta_id')->constrained('consultas')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('medico_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('empleado_id')->nullable()->constrained('nomina')->onDelete('set null');
            $table->text('diagnostico')->nullable();
            $table->text('indicaciones_generales')->nullable();
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable(); // validez de la receta
            $table->enum('estado', ['activa', 'dispensada', 'vencida', 'anulada'])->default('activa');
            $table->timestamps();
            $table->softDeletes();

            $table->index('consulta_id');
            $table->index('paciente_id');
            $table->index('numero_receta');
        });

        Schema::create('receta_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receta_id')->constrained('recetas')->onDelete('cascade');
            $table->foreignId('medicamento_id')->nullable()->constrained('inventario')->onDelete('set null');
            $table->string('nombre_medicamento', 150); // por si no está en inventario
            $table->string('presentacion', 100)->nullable(); // tabletas, jarabe, etc.
            $table->string('dosis', 100);           // ej: 500mg
            $table->string('frecuencia', 100);      // ej: cada 8 horas
            $table->string('duracion', 100);        // ej: 7 días
            $table->integer('cantidad')->default(1);
            $table->text('indicaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receta_items');
        Schema::dropIfExists('recetas');
    }
};
