@extends('layouts.app')

@section('title', 'Panel de Control')
@section('nav-title', 'Panel de Control')

@push('styles')
<style>
.dash-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; align-items: start; }
.dash-chart-card { background: var(--bg-card); border-radius: 14px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,.07); }
.dash-chart-card h3 { font-size: 14px; font-weight: 700; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
.cita-item { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--border-color); }
.cita-item:last-child { border-bottom: none; }
.cita-hora { font-size: 13px; font-weight: 700; color: var(--primary); min-width: 44px; }
.cita-info { flex: 1; min-width: 0; }
.cita-info .nombre { font-size: 13px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cita-info .motivo { font-size: 11px; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.box-info li.alert-box { border-left: 4px solid #dc3545; }
.quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.qa-btn { background: var(--bg-body); border: 1px solid var(--border-color); border-radius: 10px; padding: 12px; display: flex; align-items: center; gap: 10px; cursor: pointer; text-decoration: none; color: var(--text-primary); transition: all .2s; font-size: 13px; font-weight: 600; }
.qa-btn:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
.qa-btn i { font-size: 20px; }
.farm-alert-item { display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 8px 0; border-bottom: 1px solid var(--border-color); font-size: 12.5px; }
.farm-alert-item:last-child { border-bottom: none; }

/* ── Orden en móvil ── */
@media (max-width: 768px) {
    /* El grid pasa a una sola columna */
    .dash-grid {
        grid-template-columns: 1fr !important;
    }
    /* Columna derecha (citas + acciones rápidas) sube al top */
    .dash-col-right  { order: -1; }
    /* Columna izquierda (gráfica + tablas) baja */
    .dash-col-left   { order: 1;  }
    /* Dentro de la columna derecha, el card de acciones rápidas sube */
    .dash-acciones-rapidas { order: -1; }
    /* Card de citas va después */
    .dash-citas-hoy  { order: 1; }
    /* Mensajes al final */
    .dash-mensajes   { order: 2; }

    /* La columna derecha usa flex para reordenar sus hijos */
    .dash-col-right {
        display: flex !important;
        flex-direction: column;
        gap: 16px;
    }
}
</style>
@endpush

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Panel de Control</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Dashboard</a></li>
        </ul>
    </div>
    <div class="head-actions" style="font-size:13px;color:var(--text-muted);">
        <i class='bx bx-calendar'></i> {{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
    </div>
</div>

{{-- Tarjetas KPI --}}
<ul class="box-info">
    <li onclick="location.href='{{ route('pacientes.index') }}'" style="cursor:pointer;">
        <i class='bx bxs-user-detail'></i>
        <span class="text">
            <h3>{{ $stats['pacientes'] }}</h3>
            <p>Pacientes Registrados</p>
        </span>
    </li>
    <li onclick="location.href='{{ route('consultas.index') }}'" style="cursor:pointer;">
        <i class='bx bxs-calendar-check'></i>
        <span class="text">
            <h3>{{ $stats['consultas'] }}</h3>
            <p>Consultas Hoy</p>
        </span>
    </li>
    <li onclick="location.href='{{ route('citas.index') }}'" style="cursor:pointer;">
        <i class='bx bx-calendar-event'></i>
        <span class="text">
            <h3>{{ $stats['citas_hoy'] }}</h3>
            <p>Citas Hoy</p>
        </span>
    </li>
    <li onclick="location.href='{{ route('medicina.index') }}'" style="cursor:pointer;" class="{{ ($stats['inventario'] + ($stats['por_vencer'] ?? 0)) > 0 ? 'alert-box' : '' }}">
        <i class='bx bxs-capsule'></i>
        <span class="text">
            <h3>{{ $stats['inventario'] }}</h3>
            <p>Alertas de Stock @if(($stats['por_vencer'] ?? 0) > 0)<small style="color:#dc3545;font-weight:700;">(+{{ $stats['por_vencer'] }} por vencer)</small>@endif</p>
        </span>
    </li>
</ul>

<div class="dash-grid">
    {{-- Columna izquierda --}}
    <div class="dash-col-left" style="display:flex;flex-direction:column;gap:20px;">

        {{-- Gráfica consultas 7 días --}}
        <div class="dash-chart-card">
            <h3><i class='bx bx-line-chart' style="color:var(--primary);"></i> Consultas — Últimos 7 días
                <a href="{{ route('reportes.index') }}" style="margin-left:auto;font-size:12px;color:var(--primary);font-weight:400;">Ver reportes completos →</a>
            </h3>
            <canvas id="chart-semana" style="max-height:200px;"></canvas>
        </div>

        {{-- Consultas recientes --}}
        <div class="table-container">
            <div class="head">
                <h3><i class='bx bx-clipboard'></i> Consultas Recientes</h3>
                <a href="{{ route('consultas.index') }}" style="color:var(--primary);font-size:13px;">Ver todas</a>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Fecha</th>
                        <th>Médico</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consultasRecientes as $c)
                    <tr style="cursor:pointer;" onclick="location.href='{{ route('expediente.show', $c->paciente_id) }}'">
                        <td>
                            <div class="cell-user">
                                <img src="" data-avatar="{{ $c->paciente->nombre_completo }}" data-bg="#0d6efd" data-size="32" alt="">
                                <span>{{ $c->paciente->nombre_completo }}</span>
                            </div>
                        </td>
                        <td>{{ $c->fecha_hora->format('d/m/Y H:i') }}</td>
                        <td>{{ $c->medico_nombre }}</td>
                        <td><span class="status status-{{ $c->estado }}">{{ $c->estado_texto }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;padding:30px;color:var(--text-muted);">No hay consultas recientes</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Bitácora (solo admin) --}}
        @if(auth()->user()->isAdmin() && $bitacora)
        <div class="table-container">
            <div class="head">
                <h3><i class='bx bx-history'></i> Bitácora del Sistema</h3>
                <a href="{{ route('bitacora.index') }}" style="color:var(--primary);font-size:13px;">Ver completa</a>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr><th>Fecha/Hora</th><th>Usuario</th><th>Acción</th><th>IP</th></tr>
                    </thead>
                    <tbody>
                        @foreach($bitacora as $b)
                        @php
                            $bc = 'badge-primary';
                            if (str_contains($b->accion,'ELIMINAR'))  $bc = 'badge-danger';
                            elseif (str_contains($b->accion,'CREAR')) $bc = 'badge-success';
                            elseif (str_contains($b->accion,'EDITAR'))$bc = 'badge-warning';
                            elseif (str_contains($b->accion,'INICIO'))$bc = 'badge-info';
                        @endphp
                        <tr>
                            <td><small>{{ $b->created_at->format('d/m H:i') }}</small></td>
                            <td>{{ $b->usuario->nombre_completo }}</td>
                            <td><span class="badge {{ $bc }}">{{ $b->accion }}</span></td>
                            <td><code style="font-size:10px;">{{ $b->ip }}</code></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- Columna derecha --}}
    <div class="dash-col-right" style="display:flex;flex-direction:column;gap:16px;">

        {{-- Acciones rápidas — orden -1 en móvil para aparecer primero --}}
        <div class="dash-chart-card dash-acciones-rapidas">
            <h3><i class='bx bx-zap' style="color:#ffc107;"></i> Acciones Rápidas</h3>
            <div class="quick-actions">
                <a href="{{ route('pacientes.index') }}" class="qa-btn"><i class='bx bx-user-plus'></i> Nuevo Paciente</a>
                <a href="{{ route('citas.index') }}" class="qa-btn"><i class='bx bx-calendar-plus'></i> Nueva Cita</a>
                <a href="{{ route('consultas.index') }}" class="qa-btn"><i class='bx bx-clipboard'></i> Nueva Consulta</a>
                <a href="{{ route('recetas.index') }}" class="qa-btn"><i class='bx bx-file-plus'></i> Nueva Receta</a>
                <a href="{{ route('inventario.index') }}" class="qa-btn"><i class='bx bx-transfer'></i> Inventario</a>
                <a href="{{ route('reportes.index') }}" class="qa-btn"><i class='bx bx-bar-chart-alt-2'></i> Reportes</a>
            </div>
        </div>

        {{-- Citas de hoy --}}
        <div class="dash-chart-card dash-citas-hoy">
            <h3><i class='bx bx-calendar-event' style="color:#0d6efd;"></i> Citas de Hoy
                <a href="{{ route('citas.index') }}" style="margin-left:auto;font-size:12px;color:var(--primary);font-weight:400;">Ver agenda →</a>
            </h3>
            @forelse($citasHoy as $cita)
            <div class="cita-item">
                <div class="cita-hora">{{ $cita->fecha_hora->format('H:i') }}</div>
                <div class="cita-info">
                    <div class="nombre">
                        <a href="{{ route('expediente.show', $cita->paciente_id) }}" style="color:var(--text-primary);text-decoration:none;" title="Ver expediente clínico">
                            {{ $cita->paciente->nombre_completo }}
                        </a>
                    </div>
                    <div class="motivo">{{ \Illuminate\Support\Str::limit($cita->motivo, 32) }}</div>
                </div>
                <span class="badge badge-{{ $cita->estado_color }}" style="font-size:10px;">{{ $cita->estado_texto }}</span>
                <a href="{{ route('consultas.index') }}?search={{ urlencode($cita->paciente->cedula) }}"
                   class="action-btn view" style="padding:4px 7px;font-size:12px;" title="Atender / Abrir Consultas del paciente">
                    <i class='bx bx-notepad'></i>
                </a>
            </div>
            @empty
            <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px;">
                <i class='bx bx-calendar-x' style="font-size:28px;display:block;margin-bottom:6px;opacity:.4;"></i>
                Sin citas programadas para hoy
            </div>
            @endforelse
        </div>

        {{-- Alertas de Farmacia (Stock bajo y próximos a vencer) --}}
        @if(isset($alertasFarmacia) && $alertasFarmacia->count() > 0)
        <div class="dash-chart-card" style="border-left:4px solid #dc3545;">
            <h3>
                <i class='bx bx-error-circle' style="color:#dc3545;"></i> Alertas de Farmacia
                <a href="{{ route('medicina.index') }}" style="margin-left:auto;font-size:12px;color:var(--primary);font-weight:400;">Catálogo →</a>
            </h3>
            @foreach($alertasFarmacia as $med)
                @php
                    $diasVenc = $med->fecha_vencimiento ? (int) now()->startOfDay()->diffInDays($med->fecha_vencimiento->startOfDay(), false) : null;
                @endphp
                <div class="farm-alert-item">
                    <div style="min-width:0;flex:1;">
                        <div style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $med->nombre_medicamento }}">
                            {{ $med->nombre_medicamento }}
                        </div>
                        <small style="color:var(--text-muted);font-size:11px;">
                            Stock: <strong style="color:{{ $med->tieneStockBajo() ? '#dc3545' : 'inherit' }};">{{ $med->cantidad }} {{ $med->unidad }}</strong> (mín: {{ $med->stock_minimo }})
                        </small>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:3px;">
                        @if($med->cantidad == 0)
                            <span class="badge badge-danger" style="font-size:10px;">Sin stock</span>
                        @elseif($med->tieneStockBajo())
                            <span class="badge badge-warning" style="font-size:10px;">Stock bajo</span>
                        @endif
                        @if($diasVenc !== null && $diasVenc <= 60)
                            <span class="badge {{ $diasVenc < 0 ? 'badge-danger' : 'badge-warning' }}" style="font-size:9.5px;">
                                {{ $diasVenc < 0 ? 'Vencido' : 'Vence en '.$diasVenc.'d' }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
            <div style="margin-top:10px;display:flex;justify-content:space-between;align-items:center;">
                <a href="{{ route('inventario.index') }}" style="font-size:11.5px;color:var(--primary);font-weight:600;text-decoration:none;">
                    <i class='bx bx-plus-circle'></i> Registrar Entrada
                </a>
                <a href="{{ route('solicitudes.index') }}" style="font-size:11.5px;color:var(--text-muted);text-decoration:none;">
                    Solicitudes →
                </a>
            </div>
        </div>
        @endif

        {{-- Mensajes sin leer --}}
        @if($stats['mensajes'] > 0)
        <div class="dash-chart-card dash-mensajes" style="border-left:4px solid #0d6efd;">
            <h3><i class='bx bxs-envelope' style="color:#0d6efd;"></i> Mensajes sin leer</h3>
            <p style="font-size:28px;font-weight:800;color:#0d6efd;margin:0;">{{ $stats['mensajes'] }}</p>
            <a href="{{ route('mensajes.index') }}" style="font-size:13px;color:var(--primary);">Ver mensajes →</a>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
const isDark    = document.body.classList.contains('dark-mode') || localStorage.getItem('darkMode') === 'true';
const gridColor = isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.06)';
Chart.defaults.color       = isDark ? '#94a3b8' : '#6c757d';
Chart.defaults.borderColor = gridColor;

new Chart(document.getElementById('chart-semana'), {
    type: 'bar',
    data: {
        labels: @json($labels7dias),
        datasets: [{
            label: 'Consultas',
            data: @json($data7dias),
            backgroundColor: 'rgba(13,110,253,.7)',
            borderColor: '#0d6efd',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: gridColor } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
