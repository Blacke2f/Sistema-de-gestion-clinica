<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla de items de solicitud (múltiples medicamentos por solicitud)
        Schema::create('solicitud_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes_inventario')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->constrained('inventario')->cascadeOnDelete();
            $table->integer('cantidad_solicitada');
            $table->string('proveedor_sugerido', 150)->nullable();
            $table->string('telefono_proveedor', 30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_items');
    }
};
