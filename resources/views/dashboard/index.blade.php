@extends('layouts.app')

@section('title', 'Panel de Control')
@section('nav-title', 'Panel de Control')

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
</div>

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
    <li onclick="location.href='{{ route('medicina.index') }}'" style="cursor:pointer;" class="{{ $stats['inventario'] > 0 ? 'alert-box' : '' }}">
        <i class='bx bxs-capsule'></i>
        <span class="text">
            <h3>{{ $stats['inventario'] }}</h3>
            <p>Alertas de Stock</p>
        </span>
    </li>
</ul>

<div class="table-data">
    <div class="order">
        <div class="head">
            <h3>Consultas Recientes</h3>
            <a href="{{ route('consultas.index') }}" style="color: var(--primary); font-size: 13px;">Ver todas</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Paciente</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consultasRecientes as $c)
                    <tr>
                        <td>
                            <div class="cell-user">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($c->paciente->nombre_completo) }}&background=0d6efd&color=fff&size=32" alt="">
                                <span>{{ $c->paciente->nombre_completo }}</span>
                            </div>
                        </td>
                        <td>{{ $c->fecha_hora->format('d/m/Y H:i') }}</td>
                        <td><span class="status status-{{ $c->estado }}">{{ $c->estado_texto }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center; padding:30px; color:var(--text-muted);">No hay consultas recientes</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="todo">
        <div class="head">
            <h3>Acciones Rápidas</h3>
        </div>
        <ul class="todo-list">
            <li class="completed" onclick="location.href='{{ route('pacientes.index') }}'" style="cursor:pointer;">
                <p><i class='bx bx-plus-circle'></i> Registrar Paciente</p>
            </li>
            <li class="completed" onclick="location.href='{{ route('consultas.index') }}'" style="cursor:pointer;">
                <p><i class='bx bx-plus-circle'></i> Nueva Consulta</p>
            </li>
            <li class="not-completed" onclick="location.href='{{ route('medicina.index') }}'" style="cursor:pointer;">
                <p><i class='bx bx-package'></i> Ver Inventario</p>
            </li>
            <li class="completed" onclick="location.href='{{ route('mensajes.index') }}'" style="cursor:pointer;">
                <p><i class='bx bx-envelope'></i> Mensajes ({{ $stats['mensajes'] }} nuevos)</p>
            </li>
        </ul>
    </div>
</div>

@if(auth()->user()->isAdmin() && $bitacora)
<div class="table-container" style="margin-top:24px;">
    <div class="table-header">
        <h3><i class='bx bx-history'></i> Bitácora del Sistema</h3>
        <span class="badge badge-info">Últimos 15 registros</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Detalles</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bitacora as $b)
                    @php
                        $badgeClass = 'badge-primary';
                        if (str_contains($b->accion, 'ELIMINAR'))   $badgeClass = 'badge-danger';
                        elseif (str_contains($b->accion, 'CREAR'))  $badgeClass = 'badge-success';
                        elseif (str_contains($b->accion, 'EDITAR') || str_contains($b->accion, 'ACTUALIZAR')) $badgeClass = 'badge-warning';
                        elseif (str_contains($b->accion, 'INICIO') || str_contains($b->accion, 'CIERRE'))     $badgeClass = 'badge-info';
                        elseif (str_contains($b->accion, 'DESACTIVAR')) $badgeClass = 'badge-danger';
                        elseif (str_contains($b->accion, 'ACTIVAR'))    $badgeClass = 'badge-success';
                    @endphp
                    <tr>
                        <td>
                            <i class='bx bx-calendar'></i> {{ $b->created_at->format('d/m/Y') }}<br>
                            <small><i class='bx bx-time-five'></i> {{ $b->created_at->format('H:i:s') }}</small>
                        </td>
                        <td>
                            <div class="cell-user">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($b->usuario->nombre_completo) }}&background=0d6efd&color=fff&size=32" alt="">
                                <span>{{ $b->usuario->nombre_completo }}</span>
                            </div>
                        </td>
                        <td><span class="badge {{ $badgeClass }}">{{ $b->accion }}</span></td>
                        <td>{{ $b->detalles ?? '-' }}</td>
                        <td><code>{{ $b->ip }}</code></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="empty-row">
                        <div class="empty-state"><i class='bx bx-history'></i><p>No hay registros en la bitácora</p></div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
