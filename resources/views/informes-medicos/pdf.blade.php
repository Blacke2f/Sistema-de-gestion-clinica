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

/* BARRA */
.bar { height:2pt; background:#003399; margin:3mm 0; }

/* SECCIÓN TÍTULOS */
.stitle { font-size:7pt; font-weight:bold; text-transform:uppercase; color:#003399; background:#eef2ff; padding:1.5mm 3mm; border:1pt solid #003399; border-bottom:none; letter-spacing:.5pt; }

/* TABLA DATOS */
table.td { width:100%; border:1pt solid #003399; margin-bottom:3.5mm; }
table.td td { padding:2.5mm 3mm; font-size:9.5pt; border-bottom:1pt solid #d0d8f0; vertical-align:middle; }
table.td tr:last-child td { border-bottom:none; }
.lb { font-weight:bold; color:#003399; background:#f5f7ff; }
.vl { color:#1a1a1a; }
.vl-box { color:#1a1a1a; border:1pt solid #003399; padding:2mm 3mm; min-height:14mm; vertical-align:top; }

/* CHECKBOXES — en tabla 2 filas x 3 cols para tipo de consulta */
table.ck-grid { width:100%; }
table.ck-grid td { padding:1.5mm 2mm; font-size:9.5pt; vertical-align:middle; }
table.ck-grid .ck-pair { white-space:nowrap; }
.cbox { width:4mm; height:4mm; border:1.5pt solid #003399; text-align:center; font-size:9pt; font-weight:bold; line-height:4mm; background:#ffffff; display:inline-block; vertical-align:middle; }
.cbox.on { background:#003399; color:#ffffff; }
.clbl { font-size:9.5pt; vertical-align:middle; padding-left:1.5mm; }

/* APTITUD — 3 opciones en una fila */
table.apt { width:100%; }
table.apt td { padding:2mm 3mm; font-size:9.5pt; text-align:center; vertical-align:middle; border-right:1pt solid #d0d8f0; }
table.apt td:last-child { border-right:none; }
.apt-label { font-size:8pt; font-weight:bold; color:#003399; display:block; margin-bottom:1mm; }

/* FIRMA */
table.firma { width:100%; margin-top:10mm; }
table.firma td { text-align:center; padding:0; width:50%; }
.fline { border-top:1.5pt solid #1a1a1a; width:40mm; margin:0 auto 2mm; }
.flbl { font-size:9pt; font-weight:bold; color:#1a1a1a; }
.fsub { font-size:7.5pt; color:#555; margin-top:1mm; }

/* PIE */
.pie { margin-top:5mm; text-align:center; font-size:7pt; color:#888; border-top:1pt solid #d0d8f0; padding-top:2mm; }
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
    <div class="rif">Yaritagua, Estado Yaracuy</div>
  </td>
  <td class="info-c">
    <div class="e1">Clínica Popular Tipo I "Dr. Gaetano Matarozzo"</div>
    <div class="e2">Sistema de Gestión Clínica</div>
    <div class="e3">INFORME MEDICO</div>
  </td>
</tr></table>

<div class="bar"></div>

{{-- DATOS DEL TRABAJADOR --}}
<div class="stitle">Datos del Trabajador</div>
<table class="td">
  <tr>
    <td class="lb" style="width:32%;">Nombre y Apellido:</td>
    <td class="vl" style="width:33%;">{{ $informe->paciente->nombre_completo }}</td>
    <td class="lb" style="width:15%;">Fecha:</td>
    <td class="vl">{{ $informe->fecha->format('d/m/Y') }}</td>
  </tr>
  <tr>
    <td class="lb">Cedula de Identidad:</td>
    <td class="vl">{{ $informe->paciente->cedula }}</td>
    <td class="lb">Edad:</td>
    <td class="vl">{{ $informe->edad ?? $informe->paciente->edad ?? '' }} anos</td>
  </tr>
  <tr>
    <td class="lb">Nombre de la Empresa:</td>
    <td class="vl" colspan="3">{{ $informe->nombre_empresa ?? '' }}</td>
  </tr>
  <tr>
    <td class="lb">Cargo:</td>
    <td class="vl">{{ $informe->cargo ?? '' }}</td>
    <td class="lb">Antiguedad en cargo:</td>
    <td class="vl">{{ $informe->antiguedad_cargo ?? '' }}</td>
  </tr>
</table>

{{-- TIPO DE CONSULTA — 2 filas de 3 para no apretarse --}}
@php $tipo = $informe->tipo_consulta; @endphp
<div class="stitle">Tipo de Consulta / Actitud</div>
<table class="td" style="margin-bottom:3.5mm;">
  <tr>
    <td style="padding:2mm 3mm;">
      <table class="ck-grid">
        <tr>
          <td class="ck-pair">
            <div class="cbox {{ $tipo==='preempleo' ? 'on':'' }}">{!! $tipo==='preempleo' ? '&#10003;':'' !!}</div>
            <span class="clbl">Pre-empleo</span>
          </td>
          <td class="ck-pair">
            <div class="cbox {{ $tipo==='prevacacional' ? 'on':'' }}">{!! $tipo==='prevacacional' ? '&#10003;':'' !!}</div>
            <span class="clbl">Pre-vacacional</span>
          </td>
          <td class="ck-pair">
            <div class="cbox {{ $tipo==='postvacacional' ? 'on':'' }}">{!! $tipo==='postvacacional' ? '&#10003;':'' !!}</div>
            <span class="clbl">Post-vacacional</span>
          </td>
        </tr>
        <tr>
          <td class="ck-pair">
            <div class="cbox {{ $tipo==='egreso' ? 'on':'' }}">{!! $tipo==='egreso' ? '&#10003;':'' !!}</div>
            <span class="clbl">Egreso</span>
          </td>
          <td class="ck-pair">
            <div class="cbox {{ $tipo==='reubicacion' ? 'on':'' }}">{!! $tipo==='reubicacion' ? '&#10003;':'' !!}</div>
            <span class="clbl">Reubicacion</span>
          </td>
          <td class="ck-pair">
            <div class="cbox {{ $tipo==='otros' ? 'on':'' }}">{!! $tipo==='otros' ? '&#10003;':'' !!}</div>
            <span class="clbl">Otros{{ $tipo==='otros' && $informe->tipo_consulta_otros ? ': '.$informe->tipo_consulta_otros : '' }}</span>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

{{-- APTITUD --}}
@php $aptitud = $informe->aptitud; @endphp
<div class="stitle">Resultado / Aptitud</div>
<table class="td" style="margin-bottom:3.5mm;">
  <tr>
    <td style="padding:0;">
      <table class="apt">
        <tr>
          <td>
            <span class="apt-label">Apto</span>
            <div class="cbox {{ $aptitud==='apto' ? 'on':'' }}" style="margin:0 auto;">{!! $aptitud==='apto' ? '&#10003;':'' !!}</div>
          </td>
          <td>
            <span class="apt-label">No Apto</span>
            <div class="cbox {{ $aptitud==='no_apto' ? 'on':'' }}" style="margin:0 auto;">{!! $aptitud==='no_apto' ? '&#10003;':'' !!}</div>
          </td>
          <td>
            <span class="apt-label">Apto Condicionado</span>
            <div class="cbox {{ $aptitud==='apto_condicionado' ? 'on':'' }}" style="margin:0 auto;">{!! $aptitud==='apto_condicionado' ? '&#10003;':'' !!}</div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

{{-- INFORMACIÓN CLÍNICA --}}
<div class="stitle">Informacion Clinica</div>
<table class="td">
  @if($informe->diagnostico)
  <tr>
    <td class="lb" style="width:32%;">Diagnostico:</td>
    <td class="vl">{{ $informe->diagnostico }}</td>
  </tr>
  @endif
  <tr>
    <td class="lb">Estudios Paraclinicos:</td>
    <td class="vl">{{ $informe->estudios_paraclinicos ?? '' }}</td>
  </tr>
  <tr>
    <td class="lb">Referencia:</td>
    <td class="vl">{{ $informe->referencia ?? '' }}</td>
  </tr>
  <tr>
    <td class="lb" style="vertical-align:top;padding-top:2mm;">Recomendaciones:</td>
    <td class="vl-box">{{ $informe->recomendaciones ?? '' }}</td>
  </tr>
</table>

{{-- FIRMA --}}
<table class="firma"><tr>
  <td>
    <div class="fline"></div>
    <div class="flbl">Firma del Trabajador</div>
    <div class="fsub">C.I.: {{ $informe->paciente->cedula }}</div>
  </td>
  <td>
    <div class="fline"></div>
    <div class="flbl">Dr(a). {{ $informe->medico_nombre }}</div>
    <div class="fsub">Medico Ocupacional - Clínica Dr. Gaetano Matarozzo</div>
  </td>
</tr></table>

{{-- PIE --}}
<div class="pie">
  {{ $informe->numero_informe }} &nbsp;|&nbsp; Generado: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; SISGECLIN
</div>

</body>
</html>
