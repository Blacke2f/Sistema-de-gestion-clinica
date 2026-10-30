<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Consulta;
use App\Models\Bitacora;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CloseOrphanConsultas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consultas:close-orphans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cierra automáticamente las consultas que se quedaron en curso de días anteriores.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Buscar consultas "en_curso" o "en_proceso" cuya fecha sea anterior a hoy
        $consultasHuerfanas = Consulta::whereIn('estado', ['en_curso', 'en_proceso', 'pendiente'])
            ->whereDate('fecha_hora', '<', Carbon::today())
            ->get();

        if ($consultasHuerfanas->isEmpty()) {
            $this->info('No hay consultas huérfanas por cerrar.');
            return;
        }

        $count = 0;
        foreach ($consultasHuerfanas as $consulta) {
            // Pasarla a completada para que no quede abierta eternamente
            $consulta->update(['estado' => 'completada']);

            // Dejar constancia en la bitácora
            Bitacora::registrar(
                1, // ID de un admin/sistema
                'CIERRE AUTOMÁTICO DE CONSULTA',
                'consultas',
                $consulta->id,
                "La consulta #{$consulta->id} fue cerrada automáticamente por el sistema al finalizar la jornada."
            );
            $count++;
        }

        Log::info("Se cerraron automáticamente {$count} consultas huérfanas.");
        $this->info("Exito: Se cerraron {$count} consultas.");
    }
}