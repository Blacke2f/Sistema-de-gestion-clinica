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
.data-table tbody tr.row-low td { background: #fff5f5 !important; }
.data-table tbody tr:last-child td { border-bottom: none; }

.badge-ok  { background: #dcfce7; color: #15803d; padding: 2px 7px; border-radius: 20px; font-size: 8.5px; font-weight: 700; }
.badge-low { background: #fee2e2; color: #b91c1c; padding: 2px 7px; border-radius: 20px; font-size: 8.5px; font-weight: 700; }
.qty-low   { color: #dc2626; font-weight: 700; }
.qty-ok    { color: #16a34a; font-weight: 600; }

.summary-table { width: 100%; border-collapse: collapse; background: #f8fafc; border-bottom: 2px solid #e2e8f0; }
.summary-table td { padding: 8px 14px; font-size: 9.5px; color: #475569; border: none; white-space: nowrap; }
.summary-table td strong { color: #1e293b; font-size: 10.5px; }
.summary-table td.alert strong { color: #dc2626; }

.footer-table { width: 100%; border-collapse: collapse; margin-top: 14px; border-top: 2px solid #e2e8f0; }
.footer-table td { font-size: 8.5px; color: #64748b; padding: 8px 14px; border: none; }
.footer-table td.right { text-align: right; color: #94a3b8; }
</style>
</head>
<body>

{{-- HEADER --}}
<table width="100%" style="border-collapse:collapse; background:#198754; padding:0; margin:0;">
    <tr>
        <td style="padding:18px 22px 14px; vertical-align:top; border:none;">
            <div style="font-size:20px; font-weight:800; color:#fff;">Inventario de Medicamentos</div>
            <div style="font-size:10px; color:rgba(255,255,255,0.85); margin-top:3px;">SISGECLIN &mdash; Sistema de Gestion Clinica</div>
        </td>
        <td style="padding:18px 22px 14px; vertical-align:top; text-align:right; border:none;">
            <div style="display:inline-block; background:rgba(255,255,255,0.25); color:#fff; padding:3px 10px; border-radius:20px; font-size:9px; font-weight:700; margin-bottom:5px;">{{ strtoupper($titulo) }}</div>
            <div style="font-size:9px; color:rgba(255,255,255,0.8); line-height:1.7;">
                Generado: {{ now()->format('d/m/Y H:i') }}<br>
                Por: {{ auth()->user()->nombre_completo }}<br>
                Total: {{ count($medicamentos) }} medicamentos
            </div>
        </td>
    </tr>
</table>

{{-- SUMMARY BAR --}}
@php
    $stockBajoCount = $medicamentos->filter->tieneStockBajo()->count();
    $normalCount    = count($medicamentos) - $stockBajoCount;
@endphp
<table class="summary-table">
    <tr>
        <td>Total: <strong>{{ count($medicamentos) }}</strong></td>
        <td>Stock Normal: <strong>{{ $normalCount }}</strong></td>
        <td class="alert">Stock Bajo: <strong>{{ $stockBajoCount }}</strong></td>
    </tr>
</table>

{{-- DATA TABLE --}}
<table class="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Medicamento</th>
            <th>Cantidad</th>
            <th>Unidad</th>
            <th>Precio</th>
            <th>Stock Min.</th>
            <th>Vencimiento</th>
            <th>Proveedor</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($medicamentos as $i => $m)
        <tr class="{{ $m->tieneStockBajo() ? 'row-low' : '' }}">
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $m->nombre_medicamento }}</strong></td>
            <td class="{{ $m->tieneStockBajo() ? 'qty-low' : 'qty-ok' }}">{{ $m->cantidad }}</td>
            <td style="color:#64748b;">{{ $m->unidad }}</td>
            <td>RD$ {{ number_format($m->precio, 2) }}</td>
            <td style="color:#64748b;">{{ $m->stock_minimo }}</td>
            <td style="color:#64748b; font-size:9px;">{{ $m->fecha_vencimiento?->format('d/m/Y') ?? '-' }}</td>
            <td style="color:#64748b; font-size:9px;">{{ \Illuminate\Support\Str::limit($m->proveedor ?? '-', 20) }}</td>
            <td>
                @if($m->tieneStockBajo())
                    <span class="badge-low">Stock Bajo</span>
                @else
                    <span class="badge-ok">Normal</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" style="text-align:center; padding:24px; color:#94a3b8; font-style:italic;">Sin medicamentos registrados</td>
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
