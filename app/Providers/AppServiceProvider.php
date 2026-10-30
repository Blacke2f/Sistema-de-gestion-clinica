<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Paginación personalizada
        Paginator::defaultView('partials.pagination');

        // Detectar URL base dinámicamente para funcionar desde cualquier IP
        // Esto permite acceso desde localhost Y desde la red local sin cambiar .env
        if (isset($_SERVER['HTTP_HOST'])) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host   = $_SERVER['HTTP_HOST'];
            
            if (php_sapi_name() === 'cli-server') {
                URL::forceRootUrl("{$scheme}://{$host}");
            } else {
                URL::forceRootUrl("{$scheme}://{$host}/SISGECLIN/public");
            }
        }
    }
}
