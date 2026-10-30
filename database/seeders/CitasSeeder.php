<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pacientes = \App\Models\Paciente::pluck('id')->toArray();
        $medicos = \App\Models\Usuario::whereIn('rol', ['admin', 'medico'])->pluck('id')->toArray();
        $servicios = \App\Models\ServicioMedico::all();
        
        if (empty($pacientes) || empty($medicos) || $servicios->isEmpty()) {
            return;
        }

        $citas = [];
        $hoy = now();

        for ($i = 0; $i < 20; $i++) {
            $servicio = $servicios->random();
            $medico = $medicos[array_rand($medicos)];
            $paciente = $pacientes[array_rand($pacientes)];
            
            // Distribuir citas entre 5 días atrás y 10 días adelante
            $diasOffset = rand(-5, 10);
            $hora = rand(8, 17); // 8 AM a 5 PM
            $minutos = rand(0, 1) == 0 ? 0 : 30;
            
            $fechaHora = $hoy->copy()->addDays($diasOffset)->setTime($hora, $minutos, 0);
            
            // Determinar estado según fecha
            $estado = 'programada';
            if ($fechaHora < $hoy) {
                $estado = rand(0, 1) == 0 ? 'completada' : 'no_asistio';
            }

            $citas[] = [
                'paciente_id' => $paciente,
                'medico_id' => $medico,
                'empleado_id' => null,
                'servicio_id' => $servicio->id,
                'creado_por' => 1,
                'fecha_hora' => $fechaHora,
                'duracion_minutos' => $servicio->duracion_minutos,
                'motivo' => 'Prueba de ' . $servicio->nombre,
                'notas' => 'Cita generada para pruebas.',
                'estado' => $estado,
                'color' => '#0d6efd',
                'recordatorio_enviado' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \App\Models\Cita::insert($citas);
    }
}
