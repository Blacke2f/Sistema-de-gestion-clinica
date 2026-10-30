<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ActualizarEstadoCitas extends Command
{
    protected $signature = 'citas:actualizar-estados';
    protected $description = 'Marca automáticamente como "no_asistio" las citas cuya hora programada ya pasó sin ser atendidas.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $citasPasadas = \App\Models\Cita::where('estado', 'programada')
            ->where('fecha_hora', '<', now()->subHours(2)) // 2 horas de gracia
            ->get();

        foreach ($citasPasadas as $cita) {
            $cita->update(['estado' => 'no_asistio']);
        }

        $this->info("Se marcaron {$citasPasadas->count()} citas como 'no asistió'.");
    }
}
