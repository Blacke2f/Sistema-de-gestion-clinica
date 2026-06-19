<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de Signos Vitales
 * Registra los signos vitales por consulta para seguimiento clínico.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signos_vitales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consulta_id')->constrained('consultas')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('registrado_por')->constrained('usuarios')->onDelete('cascade');

            // Signos vitales principales
            $table->decimal('presion_sistolica', 5, 1)->nullable();   // mmHg
            $table->decimal('presion_diastolica', 5, 1)->nullable();  // mmHg
            $table->decimal('frecuencia_cardiaca', 5, 1)->nullable(); // lpm
            $table->decimal('frecuencia_respiratoria', 5, 1)->nullable(); // rpm
            $table->decimal('temperatura', 4, 1)->nullable();         // °C
            $table->decimal('saturacion_oxigeno', 4, 1)->nullable();  // %
            $table->decimal('peso', 5, 2)->nullable();                // kg
            $table->decimal('talla', 5, 2)->nullable();               // cm
            $table->decimal('imc', 5, 2)->nullable();                 // calculado
            $table->decimal('glucosa', 6, 1)->nullable();             // mg/dL
            $table->text('observaciones')->nullable();

            $table->timestamps();

            $table->index('consulta_id');
            $table->index('paciente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signos_vitales');
    }
};
