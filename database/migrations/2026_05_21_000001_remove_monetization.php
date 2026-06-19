<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Elimina los módulos de monetización.
 * Esta es una clínica institucional — los medicamentos se reciben
 * por asignación/donación, no se cobran. Se mantiene el control
 * de stock y auditoría pero se elimina precio y facturación.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Quitar precio del inventario de medicamentos
        Schema::table('inventario', function (Blueprint $table) {
            $table->dropColumn('precio');
        });

        // Eliminar tablas de facturación
        Schema::dropIfExists('factura_items');
        Schema::dropIfExists('facturas');
    }

    public function down(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            $table->decimal('precio', 10, 2)->default(0)->after('unidad');
        });
    }
};
