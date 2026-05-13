<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Agregar campos de firma a movimientos de inventario
 *
 * Añade los campos necesarios para registrar quién entrega y quién recibe
 * los medicamentos en cada movimiento de salida.
 * Esto permite generar comprobantes de entrega con firma para auditoría.
 *
 * @version 2.5
 */
return new class extends Migration
{
    /**
     * Ejecutar la migración — agrega los campos de firma.
     */
    public function up(): void
    {
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            // Nombre de la persona que entrega el medicamento (quien lo despacha)
            $table->string('entregado_por_nombre', 150)->nullable()->after('paciente_id');

            // Nombre de la persona que recibe el medicamento (paciente u otro)
            $table->string('recibido_por_nombre', 150)->nullable()->after('entregado_por_nombre');

            // Cédula de la persona que recibe (para identificación)
            $table->string('recibido_por_cedula', 20)->nullable()->after('recibido_por_nombre');

            // Observaciones adicionales sobre la entrega
            $table->text('observaciones_entrega')->nullable()->after('recibido_por_cedula');
        });
    }

    /**
     * Revertir la migración — elimina los campos de firma.
     */
    public function down(): void
    {
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->dropColumn([
                'entregado_por_nombre',
                'recibido_por_nombre',
                'recibido_por_cedula',
                'observaciones_entrega',
            ]);
        });
    }
};
