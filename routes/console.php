<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/**
 * SISGECLIN — Comandos de consola personalizados
 * @version 3.0
 */

// Limpiar todos los cachés
Artisan::command('sisgeclin:clear', function () {
    $this->call('config:clear');
    $this->call('view:clear');
    $this->call('cache:clear');
    $this->call('route:clear');
    $this->info('SISGECLIN: todos los caches limpiados correctamente.');
})->purpose('Limpia todos los caches del sistema SISGECLIN');

// ── Scheduler ─────────────────────────────────────────────────────────────────
// Backup diario a las 2:00 AM, conservando los últimos 7 días
Schedule::command('db:backup --keep=7')->dailyAt('02:00');

// Marcar recetas vencidas automáticamente cada día a medianoche
Schedule::call(function () {
    \App\Models\Receta::where('estado', 'activa')
        ->whereNotNull('fecha_vencimiento')
        ->where('fecha_vencimiento', '<', today())
        ->update(['estado' => 'vencida']);
})->daily()->name('recetas:vencer');

// Marcar citas inasistidas (retrasadas por más de 2 horas) cada hora
Schedule::command('citas:check-inasistidas')->hourly();

// Revisar alertas de inventario (vencimientos y stock) cada noche
Schedule::command('inventario:check-alertas')->dailyAt('00:00');

// Cerrar automáticamente consultas olvidadas en estado en_curso al final del día
Schedule::command('consultas:close-orphans')->dailyAt('23:59');
