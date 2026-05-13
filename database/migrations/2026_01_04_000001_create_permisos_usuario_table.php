<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: Crear tabla de permisos por usuario
 *
 * Permite al administrador definir exactamente qué puede hacer cada usuario
 * en cada módulo del sistema (ver, crear, editar, eliminar).
 * Los administradores siempre tienen todos los permisos y no se pueden restringir.
 *
 * @version 2.5
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos_usuario', function (Blueprint $table) {
            $table->id();

            // Usuario al que aplican estos permisos
            $table->foreignId('usuario_id')
                  ->constrained('usuarios')
                  ->cascadeOnDelete(); // si se elimina el usuario, se eliminan sus permisos

            // Módulo del sistema al que aplica el permiso
            // Valores posibles: pacientes, consultas, medicina, inventario,
            //                   mensajes, nomina, usuarios, solicitudes, bitacora
            $table->string('modulo', 50);

            // Permisos granulares por módulo
            $table->boolean('puede_ver')      ->default(false); // acceder a la vista del módulo
            $table->boolean('puede_crear')    ->default(false); // crear nuevos registros
            $table->boolean('puede_editar')   ->default(false); // editar registros existentes
            $table->boolean('puede_eliminar') ->default(false); // eliminar registros

            // Cada usuario solo puede tener un registro por módulo
            $table->unique(['usuario_id', 'modulo']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos_usuario');
    }
};
