<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabla de Facturación
 * Registra los cobros por consultas y servicios de la clínica.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura', 20)->unique(); // FAC-2026-0001
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('consulta_id')->nullable()->constrained('consultas')->onDelete('set null');
            $table->foreignId('creado_por')->constrained('usuarios')->onDelete('cascade');

            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('impuesto', 10, 2)->default(0);   // ITBIS 18%
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->decimal('saldo_pendiente', 10, 2)->default(0);

            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'seguro', 'otro'])->nullable();
            $table->enum('estado', ['pendiente', 'pagada', 'parcial', 'anulada'])->default('pendiente');
            $table->string('seguro_medico', 100)->nullable();
            $table->string('numero_autorizacion', 50)->nullable(); // para seguros
            $table->text('notas')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('paciente_id');
            $table->index('consulta_id');
            $table->index(['estado', 'fecha_emision']);
        });

        Schema::create('factura_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained('facturas')->onDelete('cascade');
            $table->string('descripcion', 200);
            $table->string('tipo', 50)->default('consulta'); // consulta, medicamento, procedimiento, otro
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factura_items');
        Schema::dropIfExists('facturas');
    }
};
