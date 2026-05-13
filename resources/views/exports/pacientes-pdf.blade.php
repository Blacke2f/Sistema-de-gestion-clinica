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

.badge-m    { background: #dbeafe; color: #1d4ed8; padding: 2px 7px; border-radius: 20px; font-size: 8.5px; font-weight: 700; }
.badge-f    { background: #fce7f3; color: #9d174d; padding: 2px 7px; border-radius: 20px; font-size: 8.5px; font-weight: 700; }
.badge-otro { background: #f3f4f6; color: #374151; padding: 2px 7px; border-radius: 20px; font-size: 8.5px; font-weight: 700; }
.blood      { background: #fee2e2; color: #b91c1c; padding: 2px 6px; border-radius: 4px; font-size: 8.5px; font-weight: 800; }

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
<table width="100%" style="border-collapse:collapse; background:#005a14; padding:0; margin:0;">
    <tr>
        <td style="padding:18px 22px 14px; vertical-align:top; border:none;">
            <div style="font-size:20px; font-weight:800; color:#fff;">Pacientes Registrados</div>
            <div style="font-size:10px; color:rgba(255,255,255,0.85); margin-top:3px;">SISGECLIN &mdash; Sistema de Gestion Clinica</div>
        </td>
        <td style="padding:18px 22px 14px; vertical-align:top; text-align:right; border:none;">
            <div style="display:inline-block; background:rgba(255,255,255,0.25); color:#fff; padding:3px 10px; border-radius:20px; font-size:9px; font-weight:700; margin-bottom:5px;">{{ strtoupper($titulo) }}</div>
            <div style="font-size:9px; color:rgba(255,255,255,0.8); line-height:1.7;">
                Generado: {{ now()->format('d/m/Y H:i') }}<br>
                Por: {{ auth()->user()->nombre_completo }}<br>
                Total: {{ count($pacientes) }} pacientes
            </div>
        </td>
    </tr>
</table>

{{-- SUMMARY BAR --}}
@php
    $masculinos = $pacientes->where('genero', 'M')->count();
    $femeninos  = $pacientes->where('genero', 'F')->count();
    $otros      = $pacientes->where('genero', 'Otro')->count();
@endphp
<table class="summary-table">
    <tr>
        <td>Total: <strong>{{ count($pacientes) }}</strong></td>
        <td>Masculino: <strong>{{ $masculinos }}</strong></td>
        <td>Femenino: <strong>{{ $femeninos }}</strong></td>
        @if($otros > 0)<td>Otro: <strong>{{ $otros }}</strong></td>@endif
    </tr>
</table>

{{-- DATA TABLE --}}
<table class="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre Completo</th>
            <th>Cedula</th>
            <th>Telefono</th>
            <th>Email</th>
            <th>Genero</th>
            <th>Tipo Sangre</th>
            <th>Registro</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pacientes as $i => $p)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $p->nombre_completo }}</strong></td>
            <td style="color:#64748b; font-size:9px;">{{ $p->cedula }}</td>
            <td style="color:#64748b;">{{ $p->telefono ?? '-' }}</td>
            <td style="color:#64748b; font-size:9px;">{{ $p->email ?? '-' }}</td>
            <td>
                @if($p->genero === 'M')
                    <span class="badge-m">Masc.</span>
                @elseif($p->genero === 'F')
                    <span class="badge-f">Fem.</span>
                @else
                    <span class="badge-otro">Otro</span>
                @endif
            </td>
            <td>
                @if($p->tipo_sangre)
                    <span class="blood">{{ $p->tipo_sangre }}</span>
                @else
                    <span style="color:#94a3b8;">-</span>
                @endif
            </td>
            <td style="color:#64748b; font-size:9px;">{{ $p->created_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align:center; padding:24px; color:#94a3b8; font-style:italic;">Sin pacientes registrados</td>
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
