<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, Helvetica, sans-serif; color: #1e293b; font-size: 10.5px; background: #fff; }

.data-table { width: 100%; border-collapse: collapse; }
.data-table thead tr { background: linear-gradient(135deg, #6f42c1, #764ba2); color: #fff; }
.data-table th {
    padding: 9px 10px;
    text-align: left;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    border-bottom: 2px solid #5a32a3;
}
.data-table td {
    padding: 8px 10px;
    font-size: 9.5px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}
.data-table tbody tr:nth-child(even) td { background: #f8fafc; }
.data-table tbody tr:last-child td { border-bottom: none; }

.badge-ok  { background: #dcfce7; color: #15803d; padding: 2px 7px; border-radius: 20px; font-size: 8.5px; font-weight: 700; }
.badge-off { background: #f1f5f9; color: #475569; padding: 2px 7px; border-radius: 20px; font-size: 8.5px; font-weight: 700; }

.summary-table { width: 100%; border-collapse: collapse; background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
.summary-table td { padding: 8px 14px; font-size: 9.5px; color: #475569; border: none; white-space: nowrap; }
.summary-table td strong { color: #1e293b; font-size: 10.5px; }

.footer-table { width: 100%; border-collapse: collapse; margin-top: 14px; border-top: 2px solid #e2e8f0; }
.footer-table td { font-size: 8.5px; color: #64748b; padding: 8px 14px; border: none; }
.footer-table td.right { text-align: right; color: #94a3b8; }
</style>
</head>
<body>

{{-- HEADER --}}
<table width="100%" style="border-collapse:collapse; background:linear-gradient(135deg,#6f42c1,#764ba2); padding:0; margin:0;">
    <tr>
        <td style="padding:18px 22px 14px; vertical-align:top; border:none;">
            <div style="font-size:20px; font-weight:800; color:#fff;">Nómina del Personal</div>
            <div style="font-size:10px; color:rgba(255,255,255,0.85); margin-top:3px;">SISGECLIN &mdash; Sistema de Gestion Clinica</div>
        </td>
        <td style="padding:18px 22px 14px; vertical-align:top; text-align:right; border:none;">
            <div style="display:inline-block; background:rgba(255,255,255,0.25); color:#fff; padding:3px 10px; border-radius:20px; font-size:9px; font-weight:700; margin-bottom:5px;">NOMINA COMPLETA</div>
            <div style="font-size:9px; color:rgba(255,255,255,0.8); line-height:1.7;">
                Generado: {{ now()->format('d/m/Y H:i') }}<br>
                Por: {{ auth()->user()->nombre_completo }}<br>
                Total: {{ count($empleados) }} empleados
            </div>
        </td>
    </tr>
</table>

{{-- SUMMARY BAR --}}
@php
    $activos   = $empleados->where('estado', 'activo')->count();
    $inactivos = $empleados->where('estado', 'inactivo')->count();
    $totalSalarios = $empleados->sum('salario');
@endphp
<table class="summary-table">
    <tr>
        <td>Total: <strong>{{ count($empleados) }}</strong></td>
        <td>Activos: <strong>{{ $activos }}</strong></td>
        <td>Inactivos: <strong>{{ $inactivos }}</strong></td>
        <td>Masa Salarial: <strong>RD$ {{ number_format($totalSalarios, 2) }}</strong></td>
    </tr>
</table>

{{-- DATA TABLE --}}
<table class="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre Completo</th>
            <th>Cedula</th>
            <th>Cargo</th>
            <th>Departamento</th>
            <th>Salario</th>
            <th>Fecha Ingreso</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($empleados as $i => $e)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $e->nombre_completo }}</strong></td>
            <td style="color:#64748b; font-size:9px;">{{ $e->cedula }}</td>
            <td>{{ $e->cargo }}</td>
            <td style="color:#64748b;">{{ $e->departamento }}</td>
            <td><strong>RD$ {{ number_format($e->salario, 2) }}</strong></td>
            <td style="color:#64748b; font-size:9px;">{{ $e->fecha_ingreso?->format('d/m/Y') ?? '-' }}</td>
            <td>
                @if($e->estado === 'activo')
                    <span class="badge-ok">Activo</span>
                @else
                    <span class="badge-off">Inactivo</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center; padding:24px; color:#94a3b8; font-style:italic;">Sin empleados registrados</td>
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
