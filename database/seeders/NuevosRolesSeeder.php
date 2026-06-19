<?php

namespace Database\Seeders;

use App\Models\PermisosPorRol;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder de usuarios para los nuevos roles.
 * Crea un usuario de prueba por cada rol nuevo y aplica sus permisos por defecto.
 *
 * Ejecutar con: php artisan db:seed --class=NuevosRolesSeeder
 */
class NuevosRolesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creando usuarios para nuevos roles...');

        $usuarios = [
            [
                'username'        => 'enfermera',
                'email'           => 'enfermera@sisgeclin.com',
                'nombre_completo' => 'María Castillo (Enfermera)',
                'rol'             => 'enfermera',
            ],
            [
                'username'        => 'farmacia',
                'email'           => 'farmacia@sisgeclin.com',
                'nombre_completo' => 'Carmen Díaz (Farmacéutica)',
                'rol'             => 'farmaceutico',
            ],
            [
                'username'        => 'contador',
                'email'           => 'contador@sisgeclin.com',
                'nombre_completo' => 'Jorge Santana (Contador)',
                'rol'             => 'contador',
            ],
            [
                'username'        => 'laboratorio',
                'email'           => 'laboratorio@sisgeclin.com',
                'nombre_completo' => 'Ana Vargas (Laboratorista)',
                'rol'             => 'laboratorista',
            ],
        ];

        foreach ($usuarios as $data) {
            // Evitar duplicados
            if (Usuario::where('username', $data['username'])->exists()) {
                $this->command->warn("  Usuario '{$data['username']}' ya existe, omitiendo.");
                continue;
            }

            $usuario = Usuario::create([
                'username'        => $data['username'],
                'email'           => $data['email'],
                'nombre_completo' => $data['nombre_completo'],
                'rol'             => $data['rol'],
                'password'        => Hash::make('Admin123'),
                'activo'          => true,
            ]);

            // Aplicar permisos por defecto del rol
            PermisosPorRol::aplicar($usuario->id, $usuario->rol);

            $this->command->info("  ✓ {$data['rol']}: {$data['username']} / Admin123");
        }

        $this->command->info('✓ Nuevos usuarios creados correctamente.');
        $this->command->newLine();
        $this->command->table(
            ['Usuario', 'Contraseña', 'Rol'],
            [
                ['enfermera',   'Admin123', 'Enfermera'],
                ['farmacia',    'Admin123', 'Farmacéutico'],
                ['contador',    'Admin123', 'Contador'],
                ['laboratorio', 'Admin123', 'Laboratorista'],
            ]
        );
    }
}
