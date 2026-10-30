<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Medicamento;
use App\Models\Usuario;
use App\Models\Mensaje;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckInventarioAlertas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventario:check-alertas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Escanea el inventario en busca de medicamentos por agotarse o vencerse y envia alertas.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limiteVencimiento = Carbon::now()->addDays(30)->toDateString();

        $medicamentosAlerta = Medicamento::where('cantidad', '<=', \DB::raw('stock_minimo'))
            ->orWhereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', $limiteVencimiento)
            ->get();

        if ($medicamentosAlerta->isEmpty()) {
            $this->info('Inventario sano. No hay alertas.');
            return;
        }

        $admin = Usuario::where('rol', 'admin')->first();
        if (!$admin) {
            $this->error('No hay administrador para notificar.');
            return;
        }

        $mensajeContenido = "El sistema ha detectado " . $medicamentosAlerta->count() . " alerta(s) de inventario:\n\n";
        
        foreach ($medicamentosAlerta as $med) {
            if ($med->cantidad <= $med->stock_minimo) {
                $mensajeContenido .= "- [STOCK BAJO] {$med->nombre_medicamento}: Quedan {$med->cantidad} {$med->unidad} (Mínimo: {$med->stock_minimo})\n";
            }
            if ($med->fecha_vencimiento && $med->fecha_vencimiento <= $limiteVencimiento) {
                $fechaStr = Carbon::parse($med->fecha_vencimiento)->format('d/m/Y');
                $mensajeContenido .= "- [VENCIMIENTO] {$med->nombre_medicamento}: Vence el {$fechaStr}\n";
            }
        }

        // Crear mensaje en sistema
        Mensaje::create([
            'remitente_id' => $admin->id, // Sistema
            'destinatario_id' => $admin->id,
            'asunto' => '⚠️ ALERTA AUTOMÁTICA DE INVENTARIO',
            'contenido' => $mensajeContenido,
            'leido' => 0,
            'es_masivo' => 0,
            'created_at' => Carbon::now()
        ]);

        Log::info('Alerta de inventario generada con ' . $medicamentosAlerta->count() . ' items.');
        $this->info('Alerta enviada correctamente al administrador.');
    }
}