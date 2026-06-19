<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicioMedicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicios = [
            ['nombre' => 'Consulta General', 'descripcion' => 'Evaluación médica general de rutina.', 'duracion_minutos' => 20, 'precio' => 30.00],
            ['nombre' => 'Consulta Primera Vez', 'descripcion' => 'Apertura de expediente y evaluación inicial exhaustiva.', 'duracion_minutos' => 45, 'precio' => 50.00],
            ['nombre' => 'Control / Seguimiento', 'descripcion' => 'Revisión periódica de tratamiento o evolución.', 'duracion_minutos' => 15, 'precio' => 20.00],
            ['nombre' => 'Procedimiento Menor', 'descripcion' => 'Curaciones, retiro de puntos, etc.', 'duracion_minutos' => 30, 'precio' => 40.00],
            ['nombre' => 'Examen Físico Completo', 'descripcion' => 'Revisión médica completa para certificados médicos.', 'duracion_minutos' => 40, 'precio' => 60.00],
        ];

        foreach ($servicios as $servicio) {
            \App\Models\ServicioMedico::create($servicio);
        }
    }
}
