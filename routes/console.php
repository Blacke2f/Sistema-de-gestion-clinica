<?php

use Illuminate\Support\Facades\Artisan;

/**
 * SISGECLIN — Comandos de consola personalizados
 *
 * Registra comandos Artisan adicionales para el sistema.
 * Ejecutar con: php artisan <nombre-comando>
 *
 * @version 2.5
 */

// Comando para limpiar todos los cachés del sistema de una vez
Artisan::command('sisgeclin:clear', function () {
    $this->call('config:clear');
    $this->call('view:clear');
    $this->call('cache:clear');
    $this->call('route:clear');
    $this->info('SISGECLIN: todos los caches limpiados correctamente.');
})->purpose('Limpia todos los caches del sistema SISGECLIN');
