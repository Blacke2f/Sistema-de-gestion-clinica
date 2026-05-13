<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicamento_id')->constrained('inventario')->cascadeOnDelete();
            $table->foreignId('solicitado_por')->constrained('usuarios')->cascadeOnDelete();
            $table->integer('cantidad_solicitada');
            $table->string('proveedor_sugerido', 150)->nullable();
            $table->string('telefono_proveedor', 30)->nullable();
            $table->text('notas')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'completada'])->default('pendiente');
            $table->foreignId('atendida_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('atendida_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_inventario');
    }
};
