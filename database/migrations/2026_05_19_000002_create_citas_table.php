<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de Citas Médicas (Agenda)
 * Permite programar citas antes de que se conviertan en consultas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('medico_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('empleado_id')->nullable()->constrained('nomina')->onDelete('set null');
            $table->foreignId('consulta_id')->nullable()->constrained('consultas')->onDelete('set null'); // si se convierte en consulta
            $table->foreignId('creado_por')->constrained('usuarios')->onDelete('cascade');
            $table->dateTime('fecha_hora');
            $table->integer('duracion_minutos')->default(30);
            $table->string('motivo', 255);
            $table->text('notas')->nullable();
            $table->enum('estado', ['programada', 'confirmada', 'en_curso', 'completada', 'cancelada', 'no_asistio'])->default('programada');
            $table->string('color', 20)->default('#0d6efd'); // color en el calendario
            $table->boolean('recordatorio_enviado')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fecha_hora', 'estado']);
            $table->index('paciente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
