<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Crea el usuario administrador por defecto si no existe,
 * y garantiza que las credenciales iniciales estén activas.
 * Seguro: usa updateOrInsert para no duplicar ni perder datos.
 *
 * Credenciales por defecto:
 *   Usuario   : admin
 *   Contraseña: admin123
 *   Rol       : admin
 *
 * Ejecutar con:
 *   php artisan db:seed --class=AdminSeeder --force
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->updateOrInsert(
            ['username' => 'admin'],
            [
                'username'        => 'admin',
                'email'           => 'admin@sisgeclin.com',
                'password'        => Hash::make('admin123'),
                'nombre_completo' => 'Administrador del Sistema',
                'rol'             => 'admin',
                'activo'          => 1,
                'deleted_at'      => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]
        );

        if ($this->command) {
            $this->command->info('✓ Usuario admin creado / actualizado — usuario: admin | contraseña: admin123');
        }
    }
}
