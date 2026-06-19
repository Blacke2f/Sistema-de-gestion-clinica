<?php
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:dash', function () {
    $user = Usuario::first();
    Auth::login($user);
    $req = Request::create('/', 'GET');
    $res = app()->handle($req);
    $this->info('STATUS: ' . $res->getStatusCode());
    if ($res->getStatusCode() >= 400) {
        $this->info('CONTENT: ' . substr(strip_tags($res->getContent()), 0, 500));
    }
});

use Illuminate\Support\Facades\Schedule;

Schedule::command('inventario:check-stock')->dailyAt('06:00');

// Ejecutar cada hora para marcar citas no asistidas
Schedule::command('citas:actualizar-estados')->hourly();

// Ejecutar diariamente a las 08:00 AM para enviar recordatorios de las citas de mañana
// PAUSADO TEMPORALMENTE (Opción 3)
// Schedule::command('citas:enviar-recordatorios')->dailyAt('08:00');

// Respaldos automáticos de la base de datos
Schedule::command('backup:run --only-db')->dailyAt('03:00');
Schedule::command('backup:clean')->dailyAt('04:00');
