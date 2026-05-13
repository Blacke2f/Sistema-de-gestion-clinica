<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_medicamento', 150);
            $table->text('descripcion')->nullable();
            $table->integer('cantidad')->default(0);
            $table->string('unidad', 30)->default('unidades');
            $table->decimal('precio', 10, 2)->default(0);
            $table->date('fecha_vencimiento')->nullable();
            $table->string('proveedor', 150)->nullable();
            $table->integer('stock_minimo')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};
