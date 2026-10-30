<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckCitasInasistidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'citas:check-inasistidas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca como No Asistió las citas programadas que tengan más de 2 horas de retraso';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limiteHora = Carbon::now()->subHours(2);
        
        $citasAfectadas = Cita::whereIn('estado', ['programada', 'confirmada'])
            ->where('fecha_hora', '<', $limiteHora)
            ->update(['estado' => 'no_asistio']);

        if ($citasAfectadas > 0) {
            Log::info("Se marcaron {$citasAfectadas} citas como 'No Asistió' por inasistencia automática.");
            $this->info("Exito: Se marcaron {$citasAfectadas} citas como 'No Asistió'.");
        } else {
            $this->info("No se encontraron citas retrasadas para marcar.");
        }
    }
}