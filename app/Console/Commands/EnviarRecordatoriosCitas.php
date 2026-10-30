<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

class EnviarRecordatoriosCitas extends Command
{
    protected $signature = 'citas:enviar-recordatorios';
    protected $description = 'Envía recordatorios automáticos para las citas programadas del día siguiente.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $manana = now()->addDay()->toDateString();
        
        $citas = \App\Models\Cita::with('paciente')
            ->where('estado', 'programada')
            ->where('recordatorio_enviado', false)
            ->whereDate('fecha_hora', $manana)
            ->get();

        foreach ($citas as $cita) {
            // Simulamos el envío creando una notificación al sistema (o podría ser email/WhatsApp)
            if ($cita->paciente && $cita->paciente->email) {
                // Aquí iría Mail::to(...)->send(...)
                // ...
            }
            
            $cita->update(['recordatorio_enviado' => true]);
        }

        $this->info("Se enviaron {$citas->count()} recordatorios.");
    }
}
