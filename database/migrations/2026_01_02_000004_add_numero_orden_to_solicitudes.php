<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_inventario', function (Blueprint $table) {
            $table->string('numero_orden', 20)->nullable()->unique()->after('id');
            $table->string('numero_factura', 50)->nullable()->after('telefono_proveedor');
            $table->date('fecha_entrega_esperada')->nullable()->after('numero_factura');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_inventario', function (Blueprint $table) {
            $table->dropColumn(['numero_orden', 'numero_factura', 'fecha_entrega_esperada']);
        });
    }
};
