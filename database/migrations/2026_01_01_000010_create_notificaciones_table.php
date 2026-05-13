<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('tipo', 50);          // consulta_nueva, paciente_nuevo, entrada_inventario, salida_inventario, mensaje_nuevo
            $table->string('titulo', 150);
            $table->text('mensaje');
            $table->string('icono', 50)->default('bx-bell');
            $table->string('color', 20)->default('primary');  // primary, success, danger, warning, info
            $table->string('url', 255)->nullable();
            $table->boolean('leida')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
