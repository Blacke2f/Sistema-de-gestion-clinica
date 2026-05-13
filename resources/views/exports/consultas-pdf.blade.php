<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, Helvetica, sans-serif; color: #1e293b; font-size: 10.5px; background: #fff; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table thead tr { background: #f8fafc; }
.data-table th {
    padding: 8px 9px;
    text-align: left;
    font-size: 9px;
    font-weight: 700;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    border-bottom: 2px solid #e2e8f0;
    white-space: nowrap;
}
.data-table td {
    padding: 7px 9px;
    font-size: 9.5px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.data-table tbody tr:nth-child(even) td { background: #f8fafc; }
.data-table tbody tr:last-child td { border-bottom: none; }

.status { padding: 2px 7px; border-radius: 20px; font-size: 8.5px; font-weight: 700; }
.status-pendiente  { background: #fef9c3; color: #854d0e; }
.status-en_proceso { background: #dbeafe; color: #1d4ed8; }
.status-completada { background: #dcfce7; color: #15803d; }
.status-cancelada  { background: #fee2e2; color: #b91c1c; }

.summary-table { width: 100%; border-collapse: collapse; background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
.summary-table td { padding: 8px 14px; font-size: 9.5px; color: #475569; border: none; white-space: nowrap; }
.summary-table td strong { color: #1e293b; font-size: 10.5px; }
.dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 4px; vertical-align: middle; }

.footer-table { width: 100%; border-collapse: collapse; margin-top: 14px; padding: 10px 0; border-top: 2px solid #e2e8f0; }
.footer-table td { font-size: 8.5px; color: #64748b; padding: 8px 14px; border: none; }
.footer-table td.right { text-align: right; color: #94a3b8; }
</style>
</head>
<body>

{{-- HEADER --}}
<table width="100%" style="border-collapse:collapse; background:#0d6efd; padding:0; margin:0;">
    <tr>
        <td style="padding:18px 22px 14px; vertical-align:top; border:none;">
            <div style="font-size:20px; font-weight:800; color:#fff;">Consultas Medicas</div>
            <div style="font-size:10px; color:rgba(255,255,255,0.85); margin-top:3px;">SISGECLIN &mdash; Sistema de Gestion Clinica</div>
        </td>
        <td style="padding:18px 22px 14px; vertical-align:top; text-align:right; border:none;">
            <div style="display:inline-block; background:rgba(255,255,255,0.25); color:#fff; padding:3px 10px; border-radius:20px; font-size:9px; font-weight:700; margin-bottom:5px;">{{ strtoupper($titulo) }}</div>
            <div style="font-size:9px; color:rgba(255,255,255,0.8); line-height:1.7;">
                Generado: {{ now()->format('d/m/Y H:i') }}<br>
                Por: {{ auth()->user()->nombre_completo }}<br>
                Total: {{ count($consultas) }} registros
            </div>
        </td>
    </tr>
</table>

{{-- SUMMARY BAR --}}
@php
    $porEstado   = $consultas->groupBy('estado');
    $pendientes  = $porEstado->get('pendiente',  collect())->count();
    $enProceso   = $porEstado->get('en_proceso', collect())->count();
    $completadas = $porEstado->get('completada', collect())->count();
    $canceladas  = $porEstado->get('cancelada',  collect())->count();
@endphp
<table class="summary-table">
    <tr>
        <td><span class="dot" style="background:#f59e0b;"></span>Pendientes: <strong>{{ $pendientes }}</strong></td>
        <td><span class="dot" style="background:#3b82f6;"></span>En Proceso: <strong>{{ $enProceso }}</strong></td>
        <td><span class="dot" style="background:#22c55e;"></span>Completadas: <strong>{{ $completadas }}</strong></td>
        <td><span class="dot" style="background:#ef4444;"></span>Canceladas: <strong>{{ $canceladas }}</strong></td>
    </tr>
</table>

{{-- DATA TABLE --}}
<table class="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Paciente</th>
            <th>Cedula</th>
            <th>Medico</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Motivo</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($consultas as $i => $c)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $c->paciente->nombre_completo }}</strong></td>
            <td style="color:#64748b; font-size:9px;">{{ $c->paciente->cedula }}</td>
            <td>{{ $c->medico_nombre }}</td>
            <td>{{ $c->fecha_hora->format('d/m/Y') }}</td>
            <td style="color:#94a3b8; font-size:9px;">{{ $c->fecha_hora->format('H:i') }}</td>
            <td style="color:#475569; max-width:120px;">{{ \Illuminate\Support\Str::limit($c->motivo, 40) }}</td>
            <td><span class="status status-{{ $c->estado }}">{{ $c->estado_texto }}</span></td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center; padding:24px; color:#94a3b8; font-style:italic;">Sin consultas en este periodo</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- FOOTER --}}
<table class="footer-table">
    <tr>
        <td><strong>SISGECLIN v2.5</strong> &mdash; Documento generado automaticamente &mdash; Informacion confidencial</td>
        <td class="right">&copy; {{ date('Y') }} &mdash; Todos los derechos reservados</td>
    </tr>
</table>

</body>
</html>
