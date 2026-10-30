<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CitasController;
use App\Http\Controllers\ConsultasController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamenLaboratorioController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\GastoMedicoController;
use App\Http\Controllers\InformeMedicoController;
use App\Http\Controllers\InventarioMovimientosController;
use App\Http\Controllers\MedicinaController;
use App\Http\Controllers\MensajesController;
use App\Http\Controllers\NominaController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\PacientesController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\RecetasController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\SignosVitalesController;
use App\Http\Controllers\SolicitudesInventarioController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Support\Facades\Route;

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login',   [AuthController::class, 'login'])->name('login.post')->middleware('guest');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register',[AuthController::class, 'register'])->name('register.post')->middleware('guest');
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');
Route::get('/logout',   fn() => redirect()->route('login'));  // Evita 405 si el navegador recarga tras logout

// ─── Authenticated routes ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Reportes
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/',              [ReportesController::class, 'index'])->name('index')->middleware('permiso:reportes,ver');
        Route::get('/datos',         [ReportesController::class, 'datos'])->name('datos')->middleware('permiso:reportes,ver');
        Route::post('/exportar/pdf',   [ReportesController::class, 'exportarPDF'])->name('exportar.pdf')->middleware('permiso:reportes,ver');
        Route::post('/exportar/excel', [ReportesController::class, 'exportarExcel'])->name('exportar.excel')->middleware('permiso:reportes,ver');
        
        // Fallback GET: previene error 405 si el usuario retrocede (Atrás) en el navegador
        Route::get('/exportar/pdf',    fn() => redirect()->route('reportes.index'));
        Route::get('/exportar/excel',  fn() => redirect()->route('reportes.index'));
    });

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
        Route::get('/comprobante/{movimiento}/pdf', [InventarioMovimientosController::class, 'comprobanteEntrega'])->name('comprobante')->middleware('permiso:inventario,ver');
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

    // Búsqueda global AJAX (Opciones del sistema, Inventario, Movimientos, Pacientes, Expedientes, Clínico, Documentos y Admin)
    Route::get('/buscar', function (\Illuminate\Http\Request $request) {
        $q = trim($request->get('q', ''));
        if (mb_strlen($q) < 2) return response()->json(['results' => []]);

        $user    = auth()->user();
        $results = [];

        $norm = function (string $s): string {
            $s = mb_strtolower($s, 'UTF-8');
            return strtr($s, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n']);
        };
        $qNorm = $norm($q);

        // 0. Opciones y Módulos del Sistema (permite buscar pantallas y funciones directamente)
        $opcionesSistema = [
            ['perm' => null,        'texto' => 'Panel de Control (Dashboard)',     'sub' => 'Resumen general, estadísticas y alertas de hoy',          'url' => url('/'),                     'icon' => 'bxs-dashboard',      'color' => '#0d6efd', 'keys' => 'dashboard panel inicio control estadisticas resumen'],
            ['perm' => 'medicina',  'texto' => 'Catálogo de Medicamentos',         'sub' => 'Inventario de medicinas, stock, proveedores y vencimiento', 'url' => url('medicina'),              'icon' => 'bxs-capsule',        'color' => '#fd7e14', 'keys' => 'medicamentos medicinas inventario farmacia catalogo stock pastillas insumos'],
            ['perm' => 'inventario','texto' => 'Movimientos de Inventario',        'sub' => 'Entradas, salidas, dispensación de recetas y entregas',    'url' => url('inventario'),            'icon' => 'bx-transfer',        'color' => '#dc3545', 'keys' => 'movimientos inventario entradas salidas dispensar entregar kardex'],
            ['perm' => 'inventario','texto' => 'Reporte de Auditoría de Inventario','sub' => 'PDF de control y balance de medicamentos',                'url' => url('inventario/auditoria/pdf') . '?fecha_inicio=' . now()->subDays(30)->format('Y-m-d') . '&fecha_fin=' . now()->format('Y-m-d'), 'icon' => 'bx-shield-check', 'color' => '#198754', 'keys' => 'auditoria inventario balance reporte pdf control'],
            ['perm' => 'solicitudes','texto'=> 'Solicitudes de Reposición',        'sub' => 'Órdenes de pedido de medicamentos con stock bajo',         'url' => url('solicitudes'),           'icon' => 'bx-send',            'color' => '#ffc107', 'keys' => 'solicitudes reposicion pedidos ordenes compras stock bajo'],
            ['perm' => 'pacientes', 'texto' => 'Gestión de Pacientes',             'sub' => 'Registro, búsqueda, alergias y expedientes clínicos',      'url' => url('pacientes'),             'icon' => 'bxs-user-detail',    'color' => '#0d6efd', 'keys' => 'pacientes personas cedula registro expediente alergias'],
            ['perm' => 'consultas', 'texto' => 'Consultas Médicas',                'sub' => 'Atención médica, diagnósticos y signos vitales',           'url' => url('consultas'),             'icon' => 'bx-clipboard',       'color' => '#0dcaf0', 'keys' => 'consultas medicas atencion diagnostico evolucion signos vitales'],
            ['perm' => 'citas',     'texto' => 'Agenda de Citas',                  'sub' => 'Calendario de citas programadas y confirmación',           'url' => url('citas'),                 'icon' => 'bx-calendar-event',  'color' => '#20c997', 'keys' => 'citas agenda calendario turnos programar'],
            ['perm' => 'recetas',   'texto' => 'Recetas Médicas',                  'sub' => 'Emisión, impresión y dispensación de récipe médico',       'url' => url('recetas'),               'icon' => 'bx-file-blank',      'color' => '#198754', 'keys' => 'recetas recipe prescripcion medicamentos indicaciones'],
            ['perm' => 'informes',  'texto' => 'Informes Médicos Ocupacionales',   'sub' => 'Evaluaciones pre-empleo, vacacionales y aptitud',          'url' => url('informes-medicos'),      'icon' => 'bx-id-card',         'color' => '#0d6efd', 'keys' => 'informes medicos ocupacionales preempleo aptitud constancia'],
            ['perm' => 'gastos',    'texto' => 'Gastos Médicos',                   'sub' => 'Registro y comprobantes de gastos de atención médica',     'url' => url('gastos-medicos'),        'icon' => 'bx-receipt',         'color' => '#d63384', 'keys' => 'gastos medicos facturas costos montos recibos'],
            ['perm' => 'examenes',  'texto' => 'Exámenes de Laboratorio',          'sub' => 'Solicitudes de hematología, química sanguínea y análisis', 'url' => url('examenes-laboratorio'),  'icon' => 'bx-test-tube',       'color' => '#6610f2', 'keys' => 'examenes laboratorio sangre orina heces hematologia glicemia'],
            ['perm' => 'reportes',  'texto' => 'Reportes y Estadísticas',          'sub' => 'Gráficas analíticas y exportación PDF / Excel',            'url' => url('reportes'),              'icon' => 'bx-bar-chart-alt-2', 'color' => '#0dcaf0', 'keys' => 'reportes estadisticas graficas exportar excel pdf metricas'],
            ['perm' => 'mensajes',  'texto' => 'Mensajería Interna',               'sub' => 'Bandeja de mensajes y comunicados del personal',           'url' => url('mensajes'),              'icon' => 'bxs-envelope',       'color' => '#0d6efd', 'keys' => 'mensajes correo comunicados chat bandeja'],
            ['perm' => 'admin',     'texto' => 'Nómina del Personal',              'sub' => 'Médicos, enfermeras y personal administrativo',            'url' => url('nomina'),                'icon' => 'bxs-group',          'color' => '#0ca678', 'keys' => 'nomina empleados personal medicos cargos salarios'],
            ['perm' => 'admin',     'texto' => 'Usuarios y Permisos',              'sub' => 'Cuentas de acceso, roles y permisos por módulo',           'url' => url('usuarios'),              'icon' => 'bxs-user-account',   'color' => '#6f42c1', 'keys' => 'usuarios cuentas roles permisos accesos contraseñas'],
            ['perm' => 'admin',     'texto' => 'Bitácora de Auditoría',            'sub' => 'Historial de acciones y seguridad del sistema',            'url' => url('bitacora'),              'icon' => 'bx-history',         'color' => '#495057', 'keys' => 'bitacora auditoria historial seguridad logs registros'],
            ['perm' => null,        'texto' => 'Mi Perfil y Contraseña',           'sub' => 'Configuración de tu cuenta de usuario',                    'url' => url('perfil'),                'icon' => 'bxs-user-circle',    'color' => '#0d6efd', 'keys' => 'perfil cuenta clave contrasena configuracion'],
        ];

        foreach ($opcionesSistema as $op) {
            if ($op['perm'] === 'admin' && !$user->isAdmin()) continue;
            if ($op['perm'] && $op['perm'] !== 'admin' && !$user->puedeEn($op['perm'], 'ver')) continue;

            $haystack = $norm($op['texto'] . ' ' . $op['sub'] . ' ' . $op['keys']);
            if (str_contains($haystack, $qNorm)) {
                $results[] = [
                    'categoria' => 'Opciones',
                    'tipo'      => 'Opción / Módulo',
                    'icon'      => $op['icon'],
                    'color'     => $op['color'],
                    'texto'     => $op['texto'],
                    'sub'       => $op['sub'],
                    'url'       => $op['url'],
                ];
            }
        }

        $matchPaciente = function ($q2) use ($q) {
            $q2->where('nombre', 'like', "%{$q}%")
               ->orWhere('apellido', 'like', "%{$q}%")
               ->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$q}%"])
               ->orWhere('cedula', 'like', "%{$q}%");
        };

        // 1. Medicamentos en Inventario
        try {
            if ($user->puedeEn('medicina', 'ver')) {
                \App\Models\Medicamento::where(function ($sub) use ($q) {
                        $sub->where('nombre_medicamento', 'like', "%{$q}%")
                            ->orWhere('proveedor', 'like', "%{$q}%")
                            ->orWhere('descripcion', 'like', "%{$q}%")
                            ->orWhere('unidad', 'like', "%{$q}%");
                    })
                    ->limit(5)->get()
                    ->each(fn($m) => $results[] = [
                        'categoria'   => 'Inventario',
                        'tipo'        => 'Medicamento',
                        'icon'        => 'bxs-capsule',
                        'color'       => '#fd7e14',
                        'texto'       => $m->nombre_medicamento,
                        'sub'         => 'Stock: ' . $m->cantidad . ' ' . $m->unidad . ($m->proveedor ? ' · Prov: ' . $m->proveedor : ''),
                        'url'         => url('medicina') . '?search=' . urlencode($m->nombre_medicamento),
                        'extra_url'   => url('inventario') . '?search=' . urlencode($m->nombre_medicamento),
                        'extra_label' => 'Movimientos',
                    ]);
            }
        } catch (\Throwable $e) {}

        // 2. Movimientos de Inventario y Solicitudes
        try {
            if ($user->puedeEn('inventario', 'ver')) {
                \App\Models\InventarioMovimiento::with(['medicamento', 'paciente'])
                    ->where(function ($sub) use ($q, $matchPaciente) {
                        $sub->where('motivo', 'like', "%{$q}%")
                            ->orWhere('tipo_movimiento', 'like', "%{$q}%")
                            ->orWhere('recibido_por_nombre', 'like', "%{$q}%")
                            ->orWhere('observaciones_entrega', 'like', "%{$q}%")
                            ->orWhereHas('medicamento', fn($m) => $m->where('nombre_medicamento', 'like', "%{$q}%"))
                            ->orWhereHas('paciente', $matchPaciente);
                    })
                    ->latest()
                    ->limit(3)->get()
                    ->each(fn($mov) => $results[] = [
                        'categoria' => 'Inventario',
                        'tipo'      => 'Movimiento (' . ucfirst($mov->tipo_movimiento) . ')',
                        'icon'      => $mov->tipo_movimiento === 'entrada' ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt',
                        'color'     => $mov->tipo_movimiento === 'entrada' ? '#198754' : '#dc3545',
                        'texto'     => ($mov->medicamento?->nombre_medicamento ?? 'Medicamento') . ' (' . ($mov->tipo_movimiento === 'entrada' ? '+' : '-') . $mov->cantidad . ')',
                        'sub'       => ($mov->created_at ? $mov->created_at->format('d/m/Y H:i') . ' · ' : '') . \Illuminate\Support\Str::limit($mov->motivo ?? '', 42),
                        'url'       => url('inventario') . '?periodo=mensual&search=' . urlencode($mov->medicamento?->nombre_medicamento ?? $q),
                    ]);
            }
            if ($user->puedeEn('solicitudes', 'ver') || $user->isAdmin()) {
                \App\Models\SolicitudInventario::with('medicamento')
                    ->where(function ($sub) use ($q) {
                        $sub->where('numero_orden', 'like', "%{$q}%")
                            ->orWhere('proveedor_sugerido', 'like', "%{$q}%")
                            ->orWhereHas('medicamento', fn($m) => $m->where('nombre_medicamento', 'like', "%{$q}%"));
                    })
                    ->latest()
                    ->limit(2)->get()
                    ->each(fn($sol) => $results[] = [
                        'categoria' => 'Inventario',
                        'tipo'      => 'Solicitud',
                        'icon'      => 'bx-send',
                        'color'     => '#d97706',
                        'texto'     => ($sol->numero_orden ?: 'Solicitud #' . $sol->id) . ' — ' . ($sol->medicamento?->nombre_medicamento ?? 'Medicamento'),
                        'sub'       => 'Cant: ' . $sol->cantidad_solicitada . ' · Estado: ' . ucfirst($sol->estado),
                        'url'       => url('solicitudes') . '?search=' . urlencode($sol->numero_orden ?: $q),
                    ]);
            }
        } catch (\Throwable $e) {}

        // 3. Pacientes (con botón rápido a su Expediente Clínico en la misma fila)
        try {
            if ($user->puedeEn('pacientes', 'ver')) {
                \App\Models\Paciente::where(function ($sub) use ($q) {
                        $sub->where('nombre', 'like', "%{$q}%")
                            ->orWhere('apellido', 'like', "%{$q}%")
                            ->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$q}%"])
                            ->orWhere('cedula', 'like', "%{$q}%")
                            ->orWhere('telefono', 'like', "%{$q}%")
                            ->orWhere('alergias', 'like', "%{$q}%");
                    })
                    ->limit(4)->get()
                    ->each(function ($p) use (&$results, $q) {
                        $results[] = [
                            'categoria'   => 'Pacientes',
                            'tipo'        => 'Paciente',
                            'icon'        => 'bxs-user-detail',
                            'color'       => '#0d6efd',
                            'texto'       => $p->nombre_completo,
                            'sub'         => 'Cédula: ' . $p->cedula . ($p->telefono ? ' · Tel: ' . $p->telefono : ''),
                            'url'         => url('pacientes') . '?search=' . urlencode($p->cedula ?: $q),
                            'extra_url'   => url('expediente/' . $p->id),
                            'extra_label' => 'Ver Expediente',
                        ];
                    });
            }
        } catch (\Throwable $e) {}

        // 4. Consultas y Citas
        try {
            if ($user->puedeEn('consultas', 'ver')) {
                \App\Models\Consulta::with('paciente')
                    ->where(function ($sub) use ($q, $matchPaciente) {
                        $sub->whereHas('paciente', $matchPaciente)
                            ->orWhere('motivo', 'like', "%{$q}%")
                            ->orWhere('diagnostico', 'like', "%{$q}%");
                    })
                    ->latest('fecha_hora')
                    ->limit(3)->get()
                    ->each(fn($c) => $results[] = [
                        'categoria' => 'Clínico',
                        'tipo'      => 'Consulta',
                        'icon'      => 'bx-clipboard',
                        'color'     => '#0dcaf0',
                        'texto'     => optional($c->paciente)->nombre_completo ?? 'Consulta #' . $c->id,
                        'sub'       => ($c->fecha_hora ? $c->fecha_hora->format('d/m/Y') . ' · ' : '') . \Illuminate\Support\Str::limit($c->motivo ?: ($c->diagnostico ?? 'Consulta médica'), 42),
                        'url'       => url('consultas') . '?search=' . urlencode($q),
                    ]);
            }

            if ($user->puedeEn('citas', 'ver')) {
                \App\Models\Cita::with('paciente')
                    ->where(function ($sub) use ($q, $matchPaciente) {
                        $sub->whereHas('paciente', $matchPaciente)
                            ->orWhere('motivo', 'like', "%{$q}%");
                    })
                    ->latest('fecha_hora')
                    ->limit(2)->get()
                    ->each(fn($ct) => $results[] = [
                        'categoria' => 'Clínico',
                        'tipo'      => 'Cita',
                        'icon'      => 'bx-calendar-event',
                        'color'     => '#20c997',
                        'texto'     => optional($ct->paciente)->nombre_completo ?? 'Cita #' . $ct->id,
                        'sub'       => ($ct->fecha_hora ? $ct->fecha_hora->format('d/m/Y H:i') . ' — ' : '') . \Illuminate\Support\Str::limit($ct->motivo ?? 'Cita programada', 35),
                        'url'       => url('citas') . '?search=' . urlencode($q),
                    ]);
            }
        } catch (\Throwable $e) {}

        // 5. Recetas, Informes, Gastos y Laboratorio
        try {
            if ($user->puedeEn('recetas', 'ver')) {
                \App\Models\Receta::with('paciente')
                    ->where(function ($sub) use ($q, $matchPaciente) {
                        $sub->where('numero_receta', 'like', "%{$q}%")
                            ->orWhere('diagnostico', 'like', "%{$q}%")
                            ->orWhereHas('paciente', $matchPaciente)
                            ->orWhereHas('items', fn($it) => $it->where('nombre_medicamento', 'like', "%{$q}%"));
                    })
                    ->latest()
                    ->limit(3)->get()
                    ->each(fn($r) => $results[] = [
                        'categoria' => 'Clínico',
                        'tipo'      => 'Receta',
                        'icon'      => 'bx-file-blank',
                        'color'     => '#198754',
                        'texto'     => $r->numero_receta . ' — ' . (optional($r->paciente)->nombre_completo ?? 'Paciente'),
                        'sub'       => 'Emitida: ' . ($r->fecha_emision ? $r->fecha_emision->format('d/m/Y') : '') . ' · ' . ucfirst($r->estado),
                        'url'       => url('recetas') . '?search=' . urlencode($r->numero_receta ?: $q),
                    ]);
            }

            if ($user->puedeEn('informes', 'ver')) {
                \App\Models\InformeMedico::with('paciente')
                    ->where(function ($sub) use ($q, $matchPaciente) {
                        $sub->where('numero_informe', 'like', "%{$q}%")
                            ->orWhere('tipo_consulta', 'like', "%{$q}%")
                            ->orWhere('nombre_empresa', 'like', "%{$q}%")
                            ->orWhereHas('paciente', $matchPaciente);
                    })
                    ->latest()
                    ->limit(2)->get()
                    ->each(fn($inf) => $results[] = [
                        'categoria' => 'Clínico',
                        'tipo'      => 'Informe',
                        'icon'      => 'bx-id-card',
                        'color'     => '#0d6efd',
                        'texto'     => $inf->numero_informe . ' — ' . (optional($inf->paciente)->nombre_completo ?? 'Paciente'),
                        'sub'       => 'Informe médico ocupacional',
                        'url'       => url('informes-medicos') . '?search=' . urlencode($inf->numero_informe ?: $q),
                    ]);
            }

            if ($user->puedeEn('gastos', 'ver')) {
                \App\Models\GastoMedico::with('paciente')
                    ->where(function ($sub) use ($q, $matchPaciente) {
                        $sub->where('numero_gasto', 'like', "%{$q}%")
                            ->orWhere('nombre_empresa', 'like', "%{$q}%")
                            ->orWhereHas('paciente', $matchPaciente);
                    })
                    ->latest()
                    ->limit(2)->get()
                    ->each(fn($gas) => $results[] = [
                        'categoria' => 'Clínico',
                        'tipo'      => 'Gasto Médico',
                        'icon'      => 'bx-receipt',
                        'color'     => '#d63384',
                        'texto'     => $gas->numero_gasto . ' — ' . (optional($gas->paciente)->nombre_completo ?? 'Paciente'),
                        'sub'       => 'Comprobante de gasto médico',
                        'url'       => url('gastos-medicos') . '?search=' . urlencode($gas->numero_gasto ?: $q),
                    ]);
            }

            if ($user->puedeEn('examenes', 'ver')) {
                \App\Models\ExamenLaboratorio::with('paciente')
                    ->where(function ($sub) use ($q, $matchPaciente) {
                        $sub->where('numero_solicitud', 'like', "%{$q}%")
                            ->orWhere('otros', 'like', "%{$q}%")
                            ->orWhereHas('paciente', $matchPaciente);
                    })
                    ->latest()
                    ->limit(2)->get()
                    ->each(fn($ex) => $results[] = [
                        'categoria' => 'Clínico',
                        'tipo'      => 'Laboratorio',
                        'icon'      => 'bx-test-tube',
                        'color'     => '#6610f2',
                        'texto'     => $ex->numero_solicitud . ' — ' . (optional($ex->paciente)->nombre_completo ?? 'Paciente'),
                        'sub'       => 'Solicitud de examen de laboratorio',
                        'url'       => url('examenes-laboratorio') . '?search=' . urlencode($ex->numero_solicitud ?: $q),
                    ]);
            }
        } catch (\Throwable $e) {}

        // 6. Nómina y Usuarios (solo admin)
        try {
            if ($user->isAdmin()) {
                \App\Models\Empleado::where(function ($sub) use ($q) {
                        $sub->where('nombre', 'like', "%{$q}%")
                            ->orWhere('apellido', 'like', "%{$q}%")
                            ->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$q}%"])
                            ->orWhere('cedula', 'like', "%{$q}%")
                            ->orWhere('cargo', 'like', "%{$q}%");
                    })
                    ->limit(2)->get()
                    ->each(fn($emp) => $results[] = [
                        'categoria' => 'Admin',
                        'tipo'      => 'Nómina',
                        'icon'      => 'bxs-group',
                        'color'     => '#0ca678',
                        'texto'     => $emp->nombre_completo,
                        'sub'       => $emp->cargo . ' — ' . $emp->cedula,
                        'url'       => url('nomina') . '?search=' . urlencode($emp->cedula ?: $q),
                    ]);

                \App\Models\Usuario::where(function ($sub) use ($q) {
                        $sub->where('nombre_completo', 'like', "%{$q}%")
                            ->orWhere('username', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    })
                    ->limit(2)->get()
                    ->each(fn($u) => $results[] = [
                        'categoria' => 'Admin',
                        'tipo'      => 'Usuario',
                        'icon'      => 'bxs-user-account',
                        'color'     => '#6f42c1',
                        'texto'     => $u->nombre_completo,
                        'sub'       => ucfirst($u->rol) . ' — @' . $u->username,
                        'url'       => url('usuarios') . '?search=' . urlencode($u->username),
                    ]);
            }
        } catch (\Throwable $e) {}

        return response()->json(['results' => array_slice($results, 0, 25)]);
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

    // ─── Citas / Agenda ───────────────────────────────────────────────────────
    Route::prefix('citas')->name('citas.')->group(function () {
        Route::get('/',                          [CitasController::class, 'index'])->name('index');
        Route::post('/',                         [CitasController::class, 'store'])->name('store');
        Route::get('/calendario',                [CitasController::class, 'calendario'])->name('calendario');
        Route::get('/{cita}',                    [CitasController::class, 'show'])->name('show');
        Route::put('/{cita}',                    [CitasController::class, 'update'])->name('update');
        Route::delete('/{cita}',                 [CitasController::class, 'destroy'])->name('destroy');
        Route::patch('/{cita}/estado',           [CitasController::class, 'cambiarEstado'])->name('estado');
        Route::post('/{cita}/convertir',         [CitasController::class, 'convertirAConsulta'])->name('convertir');
    });

    // ─── Expediente Clínico ───────────────────────────────────────────────────
    Route::prefix('expediente')->name('expediente.')->group(function () {
                Route::get('/{paciente}',        [ExpedienteController::class, 'show'])->name('show');
        Route::get('/{paciente}/resumen',[ExpedienteController::class, 'resumen'])->name('resumen');
    });

    // ─── Signos Vitales ───────────────────────────────────────────────────────
    Route::prefix('signos-vitales')->name('signos.')->group(function () {
        Route::post('/',                              [SignosVitalesController::class, 'store'])->name('store');
        Route::get('/consulta/{consulta}',            [SignosVitalesController::class, 'porConsulta'])->name('porConsulta');
        Route::get('/paciente/{paciente}/historial',  [SignosVitalesController::class, 'historialPaciente'])->name('historial');
    });

    // ─── Recetas Médicas ──────────────────────────────────────────────────────
    Route::prefix('recetas')->name('recetas.')->group(function () {
        Route::get('/',                                [RecetasController::class, 'index'])->name('index');
        Route::post('/',                               [RecetasController::class, 'store'])->name('store');
        Route::get('/{receta}',                        [RecetasController::class, 'show'])->name('show');
        Route::get('/{receta}/imprimir',               [RecetasController::class, 'imprimir'])->name('imprimir');
                Route::patch('/{receta}/anular',               [RecetasController::class, 'anular'])->name('anular');
        Route::post('/{receta}/dispensar',             [RecetasController::class, 'dispensar'])->name('dispensar');
        Route::get('/consulta/{consulta}',             [RecetasController::class, 'porConsulta'])->name('porConsulta');
        Route::get('/paciente/{paciente}/consultas',   [RecetasController::class, 'consultasPorPaciente'])->name('consultasPorPaciente');
        Route::get('/paciente/{paciente}/activas',     [RecetasController::class, 'recetasActivasPaciente'])->name('activasPorPaciente');
    });

    // ─── Reportes Estadísticos ────────────────────────────────────────────────


    // ─── Informes Médicos Ocupacionales ───────────────────────────────────────
    Route::prefix('informes-medicos')->name('informes-medicos.')->group(function () {
        Route::get('/',                                    [InformeMedicoController::class, 'index'])->name('index')->middleware('permiso:informes,ver');
        Route::post('/',                                   [InformeMedicoController::class, 'store'])->name('store')->middleware('permiso:informes,crear');
        Route::get('/paciente/{paciente}',                 [InformeMedicoController::class, 'porPaciente'])->name('porPaciente')->middleware('permiso:informes,ver');
        Route::get('/{informeMedico}',                     [InformeMedicoController::class, 'show'])->name('show')->middleware('permiso:informes,ver')->whereNumber('informeMedico');
        Route::put('/{informeMedico}',                     [InformeMedicoController::class, 'update'])->name('update')->middleware('permiso:informes,editar')->whereNumber('informeMedico');
        Route::delete('/{informeMedico}',                  [InformeMedicoController::class, 'destroy'])->name('destroy')->middleware('permiso:informes,eliminar')->whereNumber('informeMedico');
        Route::get('/{informeMedico}/imprimir',            [InformeMedicoController::class, 'imprimir'])->name('imprimir')->middleware('permiso:informes,ver')->whereNumber('informeMedico');
    });

    // ─── Gastos Médicos ───────────────────────────────────────────────────────
    Route::prefix('gastos-medicos')->name('gastos-medicos.')->group(function () {
        Route::get('/',                                    [GastoMedicoController::class, 'index'])->name('index')->middleware('permiso:gastos,ver');
        Route::post('/',                                   [GastoMedicoController::class, 'store'])->name('store')->middleware('permiso:gastos,crear');
        Route::get('/paciente/{paciente}',                 [GastoMedicoController::class, 'porPaciente'])->name('porPaciente')->middleware('permiso:gastos,ver');
        Route::get('/{gastoMedico}',                       [GastoMedicoController::class, 'show'])->name('show')->middleware('permiso:gastos,ver')->whereNumber('gastoMedico');
        Route::put('/{gastoMedico}',                       [GastoMedicoController::class, 'update'])->name('update')->middleware('permiso:gastos,editar')->whereNumber('gastoMedico');
        Route::delete('/{gastoMedico}',                    [GastoMedicoController::class, 'destroy'])->name('destroy')->middleware('permiso:gastos,eliminar')->whereNumber('gastoMedico');
        Route::get('/{gastoMedico}/imprimir',              [GastoMedicoController::class, 'imprimir'])->name('imprimir')->middleware('permiso:gastos,ver')->whereNumber('gastoMedico');
    });

    // ─── Exámenes de Laboratorio ──────────────────────────────────────────────
    Route::prefix('examenes-laboratorio')->name('examenes-laboratorio.')->group(function () {
        Route::get('/',                                    [ExamenLaboratorioController::class, 'index'])->name('index')->middleware('permiso:examenes,ver');
        Route::post('/',                                   [ExamenLaboratorioController::class, 'store'])->name('store')->middleware('permiso:examenes,crear');
        Route::get('/paciente/{paciente}',                 [ExamenLaboratorioController::class, 'porPaciente'])->name('porPaciente')->middleware('permiso:examenes,ver');
        Route::get('/{examenLaboratorio}',                 [ExamenLaboratorioController::class, 'show'])->name('show')->middleware('permiso:examenes,ver')->whereNumber('examenLaboratorio');
        Route::put('/{examenLaboratorio}',                 [ExamenLaboratorioController::class, 'update'])->name('update')->middleware('permiso:examenes,editar')->whereNumber('examenLaboratorio');
        Route::delete('/{examenLaboratorio}',              [ExamenLaboratorioController::class, 'destroy'])->name('destroy')->middleware('permiso:examenes,eliminar')->whereNumber('examenLaboratorio');
        Route::get('/{examenLaboratorio}/imprimir',        [ExamenLaboratorioController::class, 'imprimir'])->name('imprimir')->middleware('permiso:examenes,ver')->whereNumber('examenLaboratorio');
    });
});

