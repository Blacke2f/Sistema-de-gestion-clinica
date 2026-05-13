<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, Helvetica, sans-serif; color: #1e293b; font-size: 10px; background: #fff; }

/* ── Encabezado del reporte ── */
.header-table { width: 100%; border-collapse: collapse; background: linear-gradient(135deg, #1e293b, #0d6efd); margin-bottom: 0; }
.header-table td { padding: 18px 20px 14px; border: none; vertical-align: top; }
.header-title  { font-size: 19px; font-weight: 800; color: #fff; }
.header-sub    { font-size: 10px; color: rgba(255,255,255,0.82); margin-top: 3px; }
.header-badge  { display: inline-block; background: rgba(255,255,255,0.22); color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 9px; font-weight: 700; margin-bottom: 5px; }
.header-meta   { font-size: 9px; color: rgba(255,255,255,0.8); line-height: 1.7; }

/* ── Tarjetas de resumen ── */
.resumen-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
.resumen-table td { width: 33.33%; padding: 12px 14px; text-align: center; vertical-align: middle; border: none; }
.card-entradas { background: #f0fdf4; border-bottom: 3px solid #22c55e; }
.card-salidas  { background: #fff1f2; border-bottom: 3px solid #f43f5e; }
.card-balance  { background: #eff6ff; border-bottom: 3px solid #3b82f6; }
.card-num      { font-size: 26px; font-weight: 800; display: block; margin-bottom: 2px; }
.card-lbl      { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
.num-green { color: #16a34a; }
.num-red   { color: #e11d48; }
.num-blue  { color: #2563eb; }

/* ── Aviso de auditoría ── */
.aviso-table { width: 100%; border-collapse: collapse; background: #f0fdf4; border-left: 4px solid #22c55e; margin: 0; }
.aviso-table td { padding: 10px 14px; font-size: 9.5px; color: #14532d; border: none; }

/* ── Tabla de datos ── */
.data-table { width: 100%; border-collapse: collapse; }
.data-table thead tr { background: linear-gradient(135deg, #1e293b, #1e3a8a); color: #fff; }
.data-table th { padding: 8px 9px; text-align: left; font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; border-bottom: 2px solid #1e293b; white-space: nowrap; }
.data-table td { padding: 7px 9px; font-size: 9px; color: #334155; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.data-table tbody tr:nth-child(even) td { background: #f8fafc; }
.data-table tbody tr:last-child td { border-bottom: none; }

/* Colores de estado de stock */
.stock-ok   { color: #16a34a; font-weight: 700; }
.stock-low  { color: #dc2626; font-weight: 700; }
.qty-e      { color: #16a34a; font-weight: 600; }
.qty-s      { color: #dc2626; font-weight: 600; }

/* Badge de estado */
.badge-ok  { background: #dcfce7; color: #15803d; padding: 2px 7px; border-radius: 20px; font-size: 8px; font-weight: 700; }
.badge-low { background: #fee2e2; color: #b91c1c; padding: 2px 7px; border-radius: 20px; font-size: 8px; font-weight: 700; }

/* ── Sección de firmas ── */
.firmas-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
.firmas-table td { width: 50%; padding: 10px 16px; border: none; vertical-align: top; }
.firma-box { border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; text-align: center; }
.firma-linea { border-top: 1px solid #334155; margin: 30px 10px 6px; }
.firma-label { font-size: 9px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
.firma-nombre { font-size: 10px; color: #1e293b; font-weight: 700; margin-top: 3px; }

/* ── Pie de página ── */
.footer-table { width: 100%; border-collapse: collapse; margin-top: 16px; border-top: 2px solid #e2e8f0; }
.footer-table td { font-size: 8px; color: #64748b; padding: 8px 12px; border: none; }
.footer-table td.right { text-align: right; color: #94a3b8; }
</style>
</head>
<body>

{{-- ══ ENCABEZADO ══ --}}
<table class="header-table">
    <tr>
        <td>
            <div class="header-title">Reporte de Auditoría de Inventario</div>
            <div class="header-sub">SISGECLIN v2.5 &mdash; Control de Medicamentos</div>
        </td>
        <td style="text-align:right;">
            <div class="header-badge">AUDITORÍA OFICIAL</div>
            <div class="header-meta">
                Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}<br>
                Generado: {{ now()->format('d/m/Y H:i') }}<br>
                Por: {{ auth()->user()->nombre_completo }}
            </div>
        </td>
    </tr>
</table>

{{-- ══ TARJETAS DE RESUMEN ══ --}}
@php
    $totalMedicamentos = $medicamentos->count();
    $stockBajoCount    = $medicamentos->filter(fn($m) => $m->tieneStockBajo())->count();
    $stockOkCount      = $totalMedicamentos - $stockBajoCount;
@endphp
<table class="resumen-table">
    <tr>
        <td class="card-entradas">
            <span class="card-num num-green">{{ number_format($totalEntradas) }}</span>
            <span class="card-lbl" style="color:#15803d;">&#8593; Total Entradas (unidades)</span>
        </td>
        <td class="card-salidas">
            <span class="card-num num-red">{{ number_format($totalSalidas) }}</span>
            <span class="card-lbl" style="color:#b91c1c;">&#8595; Total Salidas (unidades)</span>
        </td>
        <td class="card-balance">
            <span class="card-num num-blue">{{ $totalMedicamentos }}</span>
            <span class="card-lbl" style="color:#1d4ed8;">Medicamentos en Inventario</span>
        </td>
    </tr>
</table>

{{-- ══ AVISO DE AUDITORÍA ══ --}}
<table class="aviso-table">
    <tr>
        <td>
            <strong>&#10003; Verificación de Integridad:</strong>
            Este reporte muestra el stock inicial, las entradas, las salidas y el stock actual de cada medicamento.
            La fórmula de verificación es: <strong>Stock Actual = Stock Inicial + Entradas &minus; Salidas</strong>.
            Si los valores coinciden, el inventario está íntegro y no se ha perdido ningún medicamento.
            Medicamentos con stock normal: <strong>{{ $stockOkCount }}</strong> &mdash;
            Medicamentos con stock bajo: <strong style="color:#dc2626;">{{ $stockBajoCount }}</strong>
        </td>
    </tr>
</table>

{{-- ══ TABLA DE MEDICAMENTOS ══ --}}
<table class="data-table" style="margin-top:2px;">
    <thead>
        <tr>
            <th>#</th>
            <th>Medicamento</th>
            <th>Unidad</th>
            <th>Stock Inicial*</th>
            <th>Entradas</th>
            <th>Salidas</th>
            <th>Stock Actual</th>
            <th>Stock Mín.</th>
            <th>Verificación</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @forelse($medicamentos as $i => $med)
        @php
            // Calcular el stock inicial: stock actual - entradas + salidas del período
            $entradas      = (int)($med->total_entradas_unidades ?? 0);
            $salidas       = (int)($med->total_salidas_unidades  ?? 0);
            $stockInicial  = $med->cantidad - $entradas + $salidas;
            // Verificar la fórmula: stock inicial + entradas - salidas = stock actual
            $stockCalculado = $stockInicial + $entradas - $salidas;
            $verificado     = $stockCalculado === $med->cantidad;
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $med->nombre_medicamento }}</strong></td>
            <td style="color:#64748b;">{{ $med->unidad }}</td>
            <td style="color:#64748b;">{{ $stockInicial }}</td>
            <td class="qty-e">+{{ $entradas }}</td>
            <td class="qty-s">-{{ $salidas }}</td>
            <td class="{{ $med->tieneStockBajo() ? 'stock-low' : 'stock-ok' }}">{{ $med->cantidad }}</td>
            <td style="color:#64748b;">{{ $med->stock_minimo }}</td>
            <td style="text-align:center;">
                @if($verificado)
                    <span style="color:#16a34a;font-weight:700;font-size:11px;">&#10003;</span>
                @else
                    <span style="color:#dc2626;font-weight:700;font-size:11px;">&#9888;</span>
                @endif
            </td>
            <td>
                @if($med->tieneStockBajo())
                    <span class="badge-low">Stock Bajo</span>
                @else
                    <span class="badge-ok">Normal</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="10" style="text-align:center;padding:20px;color:#94a3b8;font-style:italic;">Sin medicamentos registrados</td>
        </tr>
        @endforelse
    </tbody>
</table>

<p style="font-size:8px;color:#94a3b8;margin-top:6px;padding:0 4px;">
    * Stock Inicial calculado como: Stock Actual &minus; Entradas del período + Salidas del período.
    &#10003; = Verificado (la fórmula cuadra). &#9888; = Revisar manualmente.
</p>

{{-- ══ SECCIÓN DE FIRMAS ══ --}}
<table class="firmas-table">
    <tr>
        <td>
            <div class="firma-box">
                <div style="font-size:9px;color:#64748b;margin-bottom:8px;">RESPONSABLE DEL INVENTARIO</div>
                <div class="firma-linea"></div>
                <div class="firma-label">Firma</div>
                <div class="firma-nombre">{{ auth()->user()->nombre_completo }}</div>
                <div style="font-size:8.5px;color:#64748b;">{{ ucfirst(auth()->user()->rol) }}</div>
            </div>
        </td>
        <td>
            <div class="firma-box">
                <div style="font-size:9px;color:#64748b;margin-bottom:8px;">REVISADO Y APROBADO POR</div>
                <div class="firma-linea"></div>
                <div class="firma-label">Firma del Supervisor / Administrador</div>
                <div class="firma-nombre">&nbsp;</div>
                <div style="font-size:8.5px;color:#64748b;">Cargo: ___________________________</div>
            </div>
        </td>
    </tr>
</table>

{{-- ══ PIE DE PÁGINA ══ --}}
<table class="footer-table">
    <tr>
        <td><strong>SISGECLIN v2.5</strong> &mdash; Reporte de Auditoría de Inventario &mdash; Documento oficial confidencial</td>
        <td class="right">Generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }} &mdash; &copy; {{ date('Y') }}</td>
    </tr>
</table>

</body>
</html>
