<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar paciente_id y telefono_proveedor a inventario_movimientos
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->unsignedBigInteger('paciente_id')->nullable()->after('consulta_id');
            $table->string('telefono_proveedor', 30)->nullable()->after('motivo');
            $table->foreign('paciente_id')->references('id')->on('pacientes')->nullOnDelete();
        });

        // Agregar telefono_proveedor a inventario (tabla de medicamentos)
        Schema::table('inventario', function (Blueprint $table) {
            $table->string('telefono_proveedor', 30)->nullable()->after('proveedor');
        });
    }

    public function down(): void
    {
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->dropForeign(['paciente_id']);
            $table->dropColumn(['paciente_id', 'telefono_proveedor']);
        });
        Schema::table('inventario', function (Blueprint $table) {
            $table->dropColumn('telefono_proveedor');
        });
    }
};
