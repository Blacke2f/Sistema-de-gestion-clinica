<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, Helvetica, sans-serif; color: #1e293b; font-size: 10.5px; background: #fff; }

/* ── Header ── */
.header {
    background: #667eea;
    padding: 18px 22px 14px;
    margin-bottom: 0;
}
.header h1 { font-size: 20px; font-weight: 800; color: #fff; }
.header p  { font-size: 10px; color: rgba(255,255,255,0.85); margin-top: 3px; }
.header-meta {
    font-size: 9px;
    color: rgba(255,255,255,0.8);
    text-align: right;
    margin-top: -28px;
}
.badge-periodo {
    display: inline-block;
    background: rgba(255,255,255,0.25);
    color: #fff;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.4px;
}

/* ── Stats table (3 columns side by side) ── */
.stats-table { width: 100%; border-collapse: collapse; margin: 0; }
.stats-table td {
    width: 33.33%;
    padding: 14px 10px;
    text-align: center;
    vertical-align: middle;
    border: none;
}
.stats-table td.card-entradas { background: #f0fdf4; border-bottom: 3px solid #22c55e; }
.stats-table td.card-salidas  { background: #fff1f2; border-bottom: 3px solid #f43f5e; }
.stats-table td.card-total    { background: #eff6ff; border-bottom: 3px solid #3b82f6; }
.stat-num { font-size: 28px; font-weight: 800; display: block; margin-bottom: 3px; }
.stat-num.green { color: #16a34a; }
.stat-num.red   { color: #e11d48; }
.stat-num.blue  { color: #2563eb; }
.stat-label { font-size: 9px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }

/* ── Divider ── */
.divider { height: 1px; background: #e2e8f0; margin: 0; }

/* ── Data table ── */
.data-table { width: 100%; border-collapse: collapse; margin-top: 0; }
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

.badge-e { background: #dcfce7; color: #15803d; padding: 2px 7px; border-radius: 20px; font-size: 8.5px; font-weight: 700; }
.badge-s { background: #fee2e2; color: #b91c1c; padding: 2px 7px; border-radius: 20px; font-size: 8.5px; font-weight: 700; }
.qty-e   { color: #16a34a; font-weight: 700; }
.qty-s   { color: #dc2626; font-weight: 700; }
.qty-new { font-weight: 700; }
.muted   { color: #94a3b8; font-size: 8.5px; }

/* ── Footer ── */
.footer {
    margin-top: 16px;
    padding: 10px 16px;
    background: #f8fafc;
    border-top: 2px solid #e2e8f0;
}
.footer-inner { width: 100%; }
.footer-inner td { font-size: 8.5px; color: #64748b; padding: 0; border: none; }
.footer-inner td.right { text-align: right; color: #94a3b8; }
</style>
</head>
<body>

{{-- HEADER --}}
<div class="header">
    <table width="100%" style="border-collapse:collapse;">
        <tr>
            <td style="vertical-align:top; border:none; padding:0;">
                <div style="font-size:20px; font-weight:800; color:#fff;">Movimientos de Inventario</div>
                <div style="font-size:10px; color:rgba(255,255,255,0.85); margin-top:3px;">SISGECLIN &mdash; Sistema de Gestion Clinica</div>
            </td>
            <td style="vertical-align:top; text-align:right; border:none; padding:0;">
                <div style="display:inline-block; background:rgba(255,255,255,0.25); color:#fff; padding:3px 10px; border-radius:20px; font-size:9px; font-weight:700; margin-bottom:5px;">{{ strtoupper($periodoLabel) }}</div>
                <div style="font-size:9px; color:rgba(255,255,255,0.8); line-height:1.7;">
                    Generado: {{ now()->format('d/m/Y H:i') }}<br>
                    Por: {{ auth()->user()->nombre_completo }}<br>
                    Registros: {{ count($movimientos) }}
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- STAT CARDS — real HTML table so mPDF renders columns correctly --}}
<table class="stats-table">
    <tr>
        <td class="card-entradas">
            <span class="stat-num green">{{ $totalEntradas }}</span>
            <span class="stat-label">&#8593; Total Entradas</span>
        </td>
        <td class="card-salidas">
            <span class="stat-num red">{{ $totalSalidas }}</span>
            <span class="stat-label">&#8595; Total Salidas</span>
        </td>
        <td class="card-total">
            <span class="stat-num blue">{{ count($movimientos) }}</span>
            <span class="stat-label">&#8644; Movimientos</span>
        </td>
    </tr>
</table>

<div class="divider"></div>

{{-- DATA TABLE --}}
<table class="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Medicamento</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Stock Ant.</th>
            <th>Stock Nuevo</th>
            <th>Registrado por</th>
            <th>Motivo</th>
        </tr>
    </thead>
    <tbody>
        @forelse($movimientos as $i => $m)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $m->created_at->format('d/m/Y') }}</td>
            <td class="muted">{{ $m->created_at->format('H:i') }}</td>
            <td><strong>{{ $m->medicamento->nombre_medicamento }}</strong></td>
            <td>
                @if($m->tipo_movimiento === 'entrada')
                    <span class="badge-e">&#8593; Entrada</span>
                @else
                    <span class="badge-s">&#8595; Salida</span>
                @endif
            </td>
            <td class="{{ $m->tipo_movimiento === 'entrada' ? 'qty-e' : 'qty-s' }}">
                {{ $m->tipo_movimiento === 'entrada' ? '+' : '-' }}{{ $m->cantidad }}
            </td>
            <td class="muted">{{ $m->cantidad_anterior }}</td>
            <td class="qty-new">{{ $m->cantidad_nueva }}</td>
            <td>{{ $m->usuario->nombre_completo }}</td>
            <td class="muted">{{ \Illuminate\Support\Str::limit($m->motivo ?? '-', 30) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="10" style="text-align:center; padding:24px; color:#94a3b8; font-style:italic;">
                Sin movimientos en este periodo
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- FOOTER --}}
<div class="footer">
    <table class="footer-inner" width="100%">
        <tr>
            <td><strong>SISGECLIN v2.5</strong> &mdash; Documento generado automaticamente &mdash; Informacion confidencial</td>
            <td class="right">&copy; {{ date('Y') }} &mdash; Todos los derechos reservados</td>
        </tr>
    </table>
</div>

</body>
</html>
