<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\StockAlertMail;
use Illuminate\Support\Facades\Log;

class CheckInventoryStock extends Command
{
    protected $signature = 'inventario:check-stock';
    protected $description = 'Verifica si hay medicamentos con stock crítico y envía alerta';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando revisión de stock crítico...');

        $criticos = DB::table('inventario')
            ->whereColumn('cantidad', '<=', 'stock_minimo')
            ->get();

        if ($criticos->count() > 0) {
            $this->warn("Se encontraron {$criticos->count()} medicamentos con stock crítico.");
            
            try {
                Mail::to('farmacia@sisgeclin.com')->send(new StockAlertMail($criticos));
                $this->info('Alerta enviada correctamente por correo.');
                Log::info("Alerta de stock crítico enviada para {$criticos->count()} artículos.");
            } catch (\Exception $e) {
                $this->error('Error al enviar el correo: ' . $e->getMessage());
                Log::error('Error al enviar alerta de stock: ' . $e->getMessage());
            }
        } else {
            $this->info('El inventario está en niveles óptimos. No se requiere alerta.');
        }
    }
}
