<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,sans-serif; font-size:10pt; color:#1a1a1a; }
table { border-collapse:collapse; }

/* ENCABEZADO */
table.hdr { width:100%; border:1.5pt solid #003399; }
table.hdr td { vertical-align:middle; padding:0; }
td.logo-c { width:24mm; text-align:center; padding:2mm; border-right:1.5pt solid #003399; background:#ffffff; }
td.logo-c .rif { font-size:6pt; color:#555; margin-top:1mm; }
td.info-c { text-align:center; padding:3mm 5mm; background:#ffffff; }
td.info-c .e1 { font-size:7pt; font-weight:bold; text-transform:uppercase; color:#003399; letter-spacing:.5pt; }
td.info-c .e2 { font-size:11pt; font-weight:bold; color:#1a1a1a; margin:1mm 0; }
td.info-c .e3 { font-size:9pt; font-weight:bold; color:#003399; letter-spacing:.5pt; margin-top:1mm; }
td.num-c { width:30mm; text-align:center; padding:2mm; border-left:1.5pt solid #003399; background:#f5f7ff; }
td.num-c .nd { font-size:7pt; color:#555; }
td.num-c .nv { font-size:8.5pt; font-weight:bold; color:#003399; margin-top:1mm; }
td.num-c .nf { font-size:8pt; color:#333; margin-top:1mm; }

/* BARRA */
.bar { height:2pt; background:#003399; margin:3mm 0; }

/* SECCIÓN TÍTULOS */
.stitle { font-size:7pt; font-weight:bold; text-transform:uppercase; color:#003399; background:#eef2ff; padding:1.5mm 3mm; border:1pt solid #003399; border-bottom:none; letter-spacing:.5pt; }

/* TABLA DATOS */
table.td { width:100%; border:1pt solid #003399; margin-bottom:4mm; }
table.td td { padding:2.5mm 3mm; font-size:9.5pt; border-bottom:1pt solid #d0d8f0; vertical-align:middle; }
table.td tr:last-child td { border-bottom:none; }
.lb { font-weight:bold; color:#003399; background:#f5f7ff; }

/* TABLA ITEMS */
table.items { width:100%; border:1pt solid #003399; margin-bottom:4mm; }
table.items thead td { background:#003399; color:#ffffff; font-weight:bold; font-size:9pt; padding:2.5mm 3mm; }
table.items tbody td { padding:2.5mm 3mm; font-size:9.5pt; border-bottom:1pt solid #d0d8f0; vertical-align:middle; }
table.items tbody tr:last-child td { border-bottom:none; }
table.items tbody tr:nth-child(even) td { background:#f5f7ff; }
.monto { text-align:right; font-weight:bold; }
table.items tfoot td { background:#003399; color:#ffffff; font-weight:bold; font-size:10pt; padding:2.5mm 3mm; }
.total-lbl { text-align:right; }

/* FIRMA — tabla en lugar de flex */
table.footer-tbl { width:100%; margin-top:10mm; }
table.footer-tbl td { vertical-align:bottom; padding:0; }
td.footer-left { width:55%; font-size:7.5pt; color:#888; }
td.footer-right { width:45%; text-align:center; }
.fline { border-top:1.5pt solid #1a1a1a; width:40mm; margin:0 auto 2mm; }
.flbl { font-size:9pt; font-weight:bold; color:#1a1a1a; }
.fsub { font-size:7.5pt; color:#555; margin-top:1mm; }
</style>
</head>
<body>

{{-- ENCABEZADO --}}
<table class="hdr"><tr>
  <td class="logo-c">
    @if(!empty($logoSrc))
      <img src="{{ $logoSrc }}" width="20mm" alt="SISGECLIN">
    @else
      <span style="font-size:9pt;font-weight:bold;color:#003399;border:1.5pt solid #003399;padding:1mm 2mm;display:block;text-align:center;">SISGECLIN</span>
    @endif
    <div class="rif">Gestión Clínica</div>
  </td>
  <td class="info-c">
    <div class="e1">Sistema de Gestión Clínica</div>
    <div class="e2">Clínica Popular Tipo I "Dr. Gaetano Matarozzo"</div>
    <div class="e3">GASTOS MEDICOS</div>
  </td>
  <td class="num-c">
    <div class="nd">N. Documento</div>
    <div class="nv">{{ $gasto->numero_gasto }}</div>
    <div class="nf">{{ $gasto->fecha->format('d/m/Y') }}</div>
  </td>
</tr></table>

<div class="bar"></div>

{{-- DATOS DEL TRABAJADOR --}}
<div class="stitle">Datos del Trabajador</div>
<table class="td">
  <tr>
    <td class="lb" style="width:32%;">Nombre y Apellido:</td>
    <td colspan="3">{{ $gasto->paciente->nombre_completo }}</td>
  </tr>
  <tr>
    <td class="lb">Cedula de Identidad:</td>
    <td style="width:33%;">{{ $gasto->paciente->cedula }}</td>
    <td class="lb" style="width:15%;">Edad:</td>
    <td>{{ $gasto->paciente->edad ? $gasto->paciente->edad.' anos' : 'N/D' }}</td>
  </tr>
  @if($gasto->nombre_empresa)
  <tr>
    <td class="lb">Empresa:</td>
    <td colspan="3">{{ $gasto->nombre_empresa }}</td>
  </tr>
  @endif
  @if($gasto->descripcion)
  <tr>
    <td class="lb">Descripcion / Motivo:</td>
    <td colspan="3">{{ $gasto->descripcion }}</td>
  </tr>
  @endif
</table>

{{-- ÍTEMS --}}
<div class="stitle">Detalle de Gastos</div>
<table class="items">
  <thead>
    <tr>
      <td style="width:8%;">#</td>
      <td>Concepto</td>
      <td style="width:28%;text-align:right;">Monto (Bs.)</td>
    </tr>
  </thead>
  <tbody>
    @forelse($gasto->items as $i => $item)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $item->concepto }}</td>
      <td class="monto">{{ number_format($item->monto, 2) }}</td>
    </tr>
    @empty
    <tr>
      <td colspan="3" style="text-align:center;color:#888;font-style:italic;padding:4mm;">Sin items registrados</td>
    </tr>
    @endforelse
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2" class="total-lbl">TOTAL:</td>
      <td style="text-align:right;">{{ number_format($gasto->total, 2) }} Bs.</td>
    </tr>
  </tfoot>
</table>

{{-- FIRMA --}}
<table class="footer-tbl"><tr>
  <td class="footer-left">
    Generado: {{ now()->format('d/m/Y H:i') }}<br>
    SISGECLIN - Sistema de Gestion Clinica<br>
    Clínica Popular Tipo I "Dr. Gaetano Matarozzo"
  </td>
  <td class="footer-right">
    <div class="fline"></div>
    <div class="flbl">{{ $gasto->medico_nombre }}</div>
    <div class="fsub">Medico Ocupacional</div>
  </td>
</tr></table>

</body>
</html>
