@extends('layouts.app')

@section('title', 'Bitácora del Sistema')
@section('nav-title', 'Bitácora')

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Bitácora del Sistema</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Bitácora</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <form method="GET" action="{{ route('bitacora.index') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <select name="accion" class="form-control-sm" onchange="this.form.submit()">
                <option value="">Todas las acciones</option>
                @foreach(['INICIO SESIÓN','CIERRE SESIÓN','CREAR USUARIO','EDITAR USUARIO','ELIMINAR USUARIO','ACTIVAR USUARIO','DESACTIVAR USUARIO','ENTRADA INVENTARIO','SALIDA INVENTARIO','ACTUALIZAR PERFIL','CAMBIAR CONTRASEÑA'] as $a)
                    <option value="{{ $a }}" {{ request('accion') === $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
            <select name="usuario_id" class="form-control-sm" onchange="this.form.submit()">
                <option value="">Todos los usuarios</option>
                @foreach(\App\Models\Usuario::orderBy('nombre_completo')->get() as $u)
                    <option value="{{ $u->id }}" {{ request('usuario_id') == $u->id ? 'selected' : '' }}>{{ $u->nombre_completo }}</option>
                @endforeach
            </select>
            <input type="date" name="fecha" class="form-control-sm" value="{{ request('fecha') }}" onchange="this.form.submit()">
            @if(request()->hasAny(['accion','usuario_id','fecha']))
                <a href="{{ route('bitacora.index') }}" class="btn-export"><i class='bx bx-refresh'></i> Limpiar</a>
            @endif
        </form>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h3><i class='bx bx-history'></i> Registro de Actividad</h3>
        <span class="badge badge-info">{{ $bitacora->total() }} registros</span>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha/Hora</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Tabla</th>
                    <th>Detalles</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bitacora as $i => $b)
                    @php
                        $badgeClass = 'badge-primary';
                        if (str_contains($b->accion, 'ELIMINAR'))    $badgeClass = 'badge-danger';
                        elseif (str_contains($b->accion, 'CREAR'))   $badgeClass = 'badge-success';
                        elseif (str_contains($b->accion, 'EDITAR') || str_contains($b->accion, 'ACTUALIZAR')) $badgeClass = 'badge-warning';
                        elseif (str_contains($b->accion, 'INICIO') || str_contains($b->accion, 'CIERRE'))     $badgeClass = 'badge-info';
                        elseif (str_contains($b->accion, 'DESACTIVAR')) $badgeClass = 'badge-danger';
                        elseif (str_contains($b->accion, 'ACTIVAR'))    $badgeClass = 'badge-success';
                        elseif (str_contains($b->accion, 'ENTRADA'))    $badgeClass = 'badge-success';
                        elseif (str_contains($b->accion, 'SALIDA'))     $badgeClass = 'badge-danger';
                    @endphp
                    <tr>
                        <td><span class="cell-num">{{ $bitacora->firstItem() + $i }}</span></td>
                        <td>
                            <div class="cell-date">
                                <i class='bx bx-calendar'></i> {{ $b->created_at->format('d/m/Y') }}<br>
                                <small><i class='bx bx-time-five'></i> {{ $b->created_at->format('H:i:s') }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="cell-user">
                                <img src="" data-avatar="{{ $b->usuario->nombre_completo }}" data-bg="#0d6efd" data-size="32" alt="">
                                <div>
                                    <span>{{ $b->usuario->nombre_completo }}</span><br>
                                    <small class="text-muted">{{ $b->usuario->username }}</small>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge {{ $badgeClass }}">{{ $b->accion }}</span></td>
                        <td><code>{{ $b->tabla ?? '-' }}</code></td>
                        <td style="max-width:200px;font-size:12px;">{{ $b->detalles ?? '-' }}</td>
                        <td><code style="font-size:11px;">{{ $b->ip }}</code></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-row">
                        <div class="empty-state"><i class='bx bx-history'></i><p>No hay registros en la bitácora</p></div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">{{ $bitacora->links('partials.pagination') }}</div>
</div>
@endsection
