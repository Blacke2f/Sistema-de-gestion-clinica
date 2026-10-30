<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Amplía el enum de roles en la tabla usuarios.
 * Agrega: enfermera, farmaceutico, contador, laboratorista
 *
 * En MySQL/MariaDB no se puede usar ALTER TABLE para modificar un ENUM
 * directamente con Blueprint, así que usamos SQL raw.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM(
            'admin',
            'medico',
            'recepcionista',
            'enfermera',
            'farmaceutico',
            'contador',
            'laboratorista'
        ) NOT NULL DEFAULT 'recepcionista'");
    }

    public function down(): void
    {
        // Revertir a los 3 roles originales
        // (solo si no hay usuarios con los nuevos roles)
        DB::statement("ALTER TABLE usuarios MODIFY COLUMN rol ENUM(
            'admin',
            'medico',
            'recepcionista'
        ) NOT NULL DEFAULT 'recepcionista'");
    }
};
