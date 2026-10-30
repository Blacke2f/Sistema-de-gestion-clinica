<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\ConsultasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\InventarioMovimientosController;
use App\Http\Controllers\MedicinaController;
use App\Http\Controllers\MensajesController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\SolicitudesInventarioController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login',   [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register',[AuthController::class, 'register'])->name('register.post')->middleware('guest');
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// ─── Authenticated routes ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Pacientes
    Route::prefix('pacientes')->name('pacientes.')->group(function () {
        Route::get('/',              [PacientesController::class, 'index'])->name('index')->middleware('permiso:pacientes,ver');
        Route::post('/',             [PacientesController::class, 'store'])->name('store')->middleware('permiso:pacientes,crear');
        Route::get('/{paciente}',    [PacientesController::class, 'show'])->name('show')->middleware('permiso:pacientes,ver');
        Route::put('/{paciente}',    [PacientesController::class, 'update'])->name('update')->middleware('permiso:pacientes,editar');
        Route::delete('/{paciente}', [PacientesController::class, 'destroy'])->name('destroy')->middleware('permiso:pacientes,eliminar');
        Route::get('/exportar/csv',  [PacientesController::class, 'exportar'])->name('exportar')->middleware('permiso:pacientes,ver');
    });

    // Consultas
    Route::prefix('consultas')->name('consultas.')->group(function () {
        Route::get('/',              [ConsultasController::class, 'index'])->name('index')->middleware('permiso:consultas,ver');
        Route::post('/',             [ConsultasController::class, 'store'])->name('store')->middleware('permiso:consultas,crear');
        Route::get('/{consulta}',    [ConsultasController::class, 'show'])->name('show')->middleware('permiso:consultas,ver');
        Route::put('/{consulta}',    [ConsultasController::class, 'update'])->name('update')->middleware('permiso:consultas,editar');
        Route::delete('/{consulta}', [ConsultasController::class, 'destroy'])->name('destroy')->middleware('permiso:consultas,eliminar');
        Route::get('/exportar/csv',  [ConsultasController::class, 'exportar'])->name('exportar')->middleware('permiso:consultas,ver');
    });

    // Medicina / Inventario
    Route::prefix('medicina')->name('medicina.')->group(function () {
        Route::get('/',             [MedicinaController::class, 'index'])->name('index')->middleware('permiso:medicina,ver');
        Route::post('/',            [MedicinaController::class, 'store'])->name('store')->middleware('permiso:medicina,crear');
        Route::get('/{medicina}',   [MedicinaController::class, 'show'])->name('show')->middleware('permiso:medicina,ver');
        Route::put('/{medicina}',   [MedicinaController::class, 'update'])->name('update')->middleware('permiso:medicina,editar');
        Route::delete('/{medicina}',[MedicinaController::class, 'destroy'])->name('destroy')->middleware('permiso:medicina,eliminar');
    });

    // Inventario Movimientos
    Route::prefix('inventario')->name('inventario.')->group(function () {
        Route::get('/',                          [InventarioMovimientosController::class, 'index'])->name('index')->middleware('permiso:inventario,ver');
        Route::post('/entrada',                  [InventarioMovimientosController::class, 'registrarEntrada'])->name('entrada')->middleware('permiso:inventario,crear');
        Route::post('/salida',                   [InventarioMovimientosController::class, 'registrarSalida'])->name('salida')->middleware('permiso:inventario,crear');
        Route::get('/medicamento/{medicamento}', [InventarioMovimientosController::class, 'getByMedicamento'])->name('byMedicamento')->middleware('permiso:inventario,ver');
        Route::get('/exportar/csv',              [InventarioMovimientosController::class, 'exportar'])->name('exportar')->middleware('permiso:inventario,ver');
        Route::get('/auditoria/pdf',             [InventarioMovimientosController::class, 'reporteAuditoria'])->name('auditoria')->middleware('permiso:inventario,ver');
    });

    // Mensajes
    Route::prefix('mensajes')->name('mensajes.')->group(function () {
        Route::get('/',             [MensajesController::class, 'index'])->name('index')->middleware('permiso:mensajes,ver');
        Route::post('/',            [MensajesController::class, 'store'])->name('store')->middleware('permiso:mensajes,crear');
        Route::post('/masivo',      [MensajesController::class, 'storeMasivo'])->name('masivo');
        Route::get('/{mensaje}',    [MensajesController::class, 'show'])->name('show')->middleware('permiso:mensajes,ver');
        Route::patch('/{mensaje}/read', [MensajesController::class, 'markAsRead'])->name('read');
        Route::delete('/{mensaje}', [MensajesController::class, 'destroy'])->name('destroy')->middleware('permiso:mensajes,eliminar');
    });

    // Perfil
    Route::prefix('perfil')->name('perfil.')->group(function () {
        Route::get('/',          [PerfilController::class, 'index'])->name('index');
        Route::put('/',          [PerfilController::class, 'update'])->name('update');
        Route::put('/password',  [PerfilController::class, 'password'])->name('password');
    });

    // Notificaciones
    Route::prefix('notificaciones')->name('notificaciones.')->group(function () {
        Route::get('/',                              [NotificacionesController::class, 'index'])->name('index');
        Route::get('/count',                         [NotificacionesController::class, 'count'])->name('count');
        Route::patch('/read-all',                    [NotificacionesController::class, 'markAllRead'])->name('readAll');
        Route::patch('/{notificacion}/read',         [NotificacionesController::class, 'markAsRead'])->name('read');
    });

    // Docs — serve manual and technical docs
    Route::get('/docs/{file}', function (string $file) {
        $allowed = ['manual_usuario.html', 'documentacion_tecnica.html', 'modelo_er.html', 'documentacion_codigo.html', 'docs.css', 'modelo_er.css'];
        if (!in_array($file, $allowed)) abort(404);
        $path = base_path("docs/{$file}");
        if (!file_exists($path)) abort(404);
        $mime = str_ends_with($file, '.css') ? 'text/css' : 'text/html';
        return response()->file($path, ['Content-Type' => $mime . '; charset=utf-8']);
    })->name('docs');

    // Búsqueda global AJAX
    Route::get('/buscar', function (\Illuminate\Http\Request $request) {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 2) return response()->json(['results' => []]);

        $results = [];

        // Pacientes
        \App\Models\Paciente::where('nombre', 'like', "%{$q}%")
            ->orWhere('apellido', 'like', "%{$q}%")
            ->orWhere('cedula', 'like', "%{$q}%")
            ->limit(4)->get()
            ->each(fn($p) => $results[] = [
                'tipo'  => 'Paciente',
                'icon'  => 'bxs-user-detail',
                'color' => '#0d6efd',
                'texto' => $p->nombre_completo,
                'sub'   => $p->cedula,
                'url'   => url('pacientes') . '?search=' . urlencode($q),
            ]);

        // Consultas
        \App\Models\Consulta::with('paciente')
            ->whereHas('paciente', fn($q2) => $q2->where('nombre', 'like', "%{$q}%")->orWhere('apellido', 'like', "%{$q}%"))
            ->orWhere('motivo', 'like', "%{$q}%")
            ->limit(3)->get()
            ->each(fn($c) => $results[] = [
                'tipo'  => 'Consulta',
                'icon'  => 'bx-clipboard',
                'color' => '#0dcaf0',
                'texto' => optional($c->paciente)->nombre_completo ?? 'Consulta',
                'sub'   => \Illuminate\Support\Str::limit($c->motivo, 40),
                'url'   => url('consultas'),
            ]);

        // Medicamentos
        \App\Models\Medicamento::where('nombre_medicamento', 'like', "%{$q}%")
            ->orWhere('proveedor', 'like', "%{$q}%")
            ->limit(4)->get()
            ->each(fn($m) => $results[] = [
                'tipo'  => 'Medicamento',
                'icon'  => 'bxs-capsule',
                'color' => '#fd7e14',
                'texto' => $m->nombre_medicamento,
                'sub'   => 'Stock: ' . $m->cantidad . ' ' . $m->unidad,
                'url'   => url('medicina'),
            ]);

        // Usuarios (solo admin)
        if (auth()->user()->isAdmin()) {
            \App\Models\Usuario::where('nombre_completo', 'like', "%{$q}%")
                ->orWhere('username', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->limit(3)->get()
                ->each(fn($u) => $results[] = [
                    'tipo'  => 'Usuario',
                    'icon'  => 'bxs-user-account',
                    'color' => '#6f42c1',
                    'texto' => $u->nombre_completo,
                    'sub'   => ucfirst($u->rol) . ' — ' . $u->username,
                    'url'   => url('usuarios'),
                ]);
        }

        return response()->json(['results' => array_slice($results, 0, 10)]);
    })->name('buscar');

    // Solicitudes de inventario (todos los roles)
    Route::prefix('solicitudes')->name('solicitudes.')->group(function () {
        Route::get('/',                          [SolicitudesInventarioController::class, 'index'])->name('index');
        Route::post('/',                         [SolicitudesInventarioController::class, 'store'])->name('store');
        Route::patch('/{solicitud}/atender',     [SolicitudesInventarioController::class, 'atender'])->name('atender');
    });

    // ─── Admin only ───────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {

        // Bitácora completa
        Route::get('/bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');

        // Nómina
        Route::prefix('nomina')->name('nomina.')->group(function () {
            Route::get('/',            [NominaController::class, 'index'])->name('index');
            Route::post('/',           [NominaController::class, 'store'])->name('store');
            Route::get('/{nomina}',    [NominaController::class, 'show'])->name('show');
            Route::put('/{nomina}',    [NominaController::class, 'update'])->name('update');
            Route::delete('/{nomina}', [NominaController::class, 'destroy'])->name('destroy');
            Route::get('/exportar/csv',[NominaController::class, 'exportar'])->name('exportar');
        });

        // Usuarios
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/',                      [UsuariosController::class, 'index'])->name('index');
            Route::post('/',                     [UsuariosController::class, 'store'])->name('store');
            Route::get('/{usuario}',             [UsuariosController::class, 'show'])->name('show');
            Route::put('/{usuario}',             [UsuariosController::class, 'update'])->name('update');
            Route::delete('/{usuario}',          [UsuariosController::class, 'destroy'])->name('destroy');
            Route::patch('/{usuario}/toggle',    [UsuariosController::class, 'toggleEstado'])->name('toggle');
            // Permisos granulares por usuario
            Route::get('/{usuario}/permisos',    [UsuariosController::class, 'editarPermisos'])->name('permisos');
            Route::post('/{usuario}/permisos',   [UsuariosController::class, 'guardarPermisos'])->name('permisos.guardar');
        });

        // Exportaciones avanzadas solo admin (consultas, pacientes, medicamentos, nomina)
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('/consultas',    [ExportController::class, 'exportarConsultas'])->name('consultas');
            Route::get('/pacientes',    [ExportController::class, 'exportarPacientes'])->name('pacientes');
            Route::get('/medicamentos', [ExportController::class, 'exportarMedicamentos'])->name('medicamentos');
            Route::get('/nomina',       [ExportController::class, 'exportarNomina'])->name('nomina');
        });
    });

    // Exportar movimientos — accesible para todos los roles autenticados
    Route::get('/export/movimientos', [ExportController::class, 'exportarMovimientos'])->name('export.movimientos');
});
