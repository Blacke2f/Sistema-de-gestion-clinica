<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Crea el usuario administrador por defecto si no existe.
 * Seguro: usa updateOrCreate para no duplicar ni perder datos.
 *
 * Credenciales:
 *   Usuario : admin
 *   Password: Admin123
 *   Rol     : admin
 *
 * Ejecutar con:
 *   php artisan db:seed --class=AdminSeeder
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── Tabla usuarios (modelo Usuario) ──────────────────────────────────
        DB::table('usuarios')->updateOrInsert(
            // Condición de búsqueda (no duplica si ya existe)
            ['username' => 'admin'],
            // Datos a insertar / actualizar
            [
                'nombre_completo' => 'Administrador Sistema',
                'email'           => 'admin@sisgeclin.local',
                'username'        => 'admin',
                'password'        => Hash::make('Admin123'),
                'rol'             => 'admin',
                'activo'          => 1,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]
        );

        $this->command->info('✓ Usuario admin creado / verificado — usuario: admin | password: Admin123');
    }
}
