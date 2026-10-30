<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:Arial,sans-serif; font-size:10pt; color:#1a1a1a; }
table { border-collapse:collapse; }
.bar { height:2pt; background:#003399; margin:3mm 0; }
table.pac { width:100%; border:1pt solid #003399; margin-bottom:4mm; }
table.pac td { padding:2.5mm 3mm; font-size:9.5pt; border-bottom:1pt solid #d0d8f0; }
table.pac tr:last-child td { border-bottom:none; }
table.rec-box { width:100%; border:1.5pt solid #003399; margin-bottom:4mm; }
table.meds { width:100%; }
table.meds td { padding:2mm 2mm; font-size:9pt; border-bottom:1pt dashed #d0d8f0; vertical-align:top; }
table.meds tr:last-child td { border-bottom:none; }
.blank-line { border-bottom:1pt solid #aaa; height:7mm; margin-bottom:2mm; }
table.firma { width:100%; margin-top:8mm; }
table.firma td { text-align:center; padding:0; width:50%; }
.fline { border-top:1.5pt solid #1a1a1a; width:40mm; margin:0 auto 2mm; }
.flbl { font-size:9pt; font-weight:bold; }
.fsub { font-size:7.5pt; color:#555; margin-top:1mm; }
.pie { margin-top:4mm; text-align:center; font-size:7pt; color:#888; border-top:1pt solid #d0d8f0; padding-top:2mm; }
</style>
</head>
<body>

{{-- ENCABEZADO --}}
<table style="width:100%;border-collapse:collapse;border:1.5pt solid #003399;">
<tr>
  <td style="width:26mm;text-align:center;padding:3mm 2mm;border-right:1.5pt solid #003399;vertical-align:middle;background:#ffffff;">
    @if(!empty($logoSrc))
      <img src="{{ $logoSrc }}" width="20mm" alt="Logo">
    @else
      <span style="font-size:8pt;font-weight:bold;color:#003399;border:1.5pt solid #003399;padding:1mm 2mm;display:block;">CLINICA<br>POPULAR</span>
    @endif
  </td>
  <td style="text-align:center;padding:3mm 5mm;vertical-align:middle;background:#ffffff;">
    <div style="font-size:10pt;font-weight:bold;text-transform:uppercase;color:#003399;letter-spacing:.5pt;line-height:1.2;">Clinica Popular Tipo I<br>"Dr. Gaetano Matarozzo"</div>
    <div style="font-size:8pt;color:#555;margin-top:1.5mm;">Municipio Pena, Estado Yaracuy</div>
    <div style="font-size:8.5pt;font-weight:bold;color:#003399;letter-spacing:.5pt;margin-top:2mm;">RECETA MEDICA</div>
  </td>
  <td style="width:14mm;text-align:center;padding:3mm 2mm;border-left:1.5pt solid #003399;vertical-align:middle;font-size:22pt;color:#003399;background:#ffffff;">
    &#9877;
  </td>
</tr>
</table>

<div class="bar"></div>

{{-- NÚMERO Y TÍTULO --}}
<table style="width:100%;border-collapse:collapse;margin-bottom:4mm;">
<tr>
  <td style="vertical-align:middle;font-size:12pt;font-weight:bold;color:#1a1a1a;">
    Informe Medico
  </td>
  <td style="text-align:right;vertical-align:middle;">
    <span style="font-size:8.5pt;font-weight:bold;color:#003399;background:#eef2ff;border:1pt solid #003399;padding:1mm 3mm;">{{ $receta->numero_receta }}</span>
  </td>
</tr>
</table>

{{-- DATOS DEL PACIENTE --}}
<table class="pac">
  <tr>
    <td style="font-weight:bold;color:#003399;background:#f5f7ff;width:40%;">Nombre del Trabajador (a)</td>
    <td>{{ $receta->paciente->nombre_completo }}</td>
  </tr>
  <tr>
    <td style="font-weight:bold;color:#003399;background:#f5f7ff;">Cedula de Identidad</td>
    <td>{{ $receta->paciente->cedula }}</td>
  </tr>
</table>

{{-- CUERPO DE LA RECETA --}}
<table class="rec-box">
  <tr>
    <td style="background:#003399;color:#ffffff;font-size:8pt;font-weight:bold;text-transform:uppercase;padding:2mm 3mm;letter-spacing:.5pt;">
      Tratamiento Medico Prescrito
    </td>
  </tr>
  <tr>
    <td style="padding:3mm 4mm;background:#ffffff;">
      <p style="font-size:9.5pt;line-height:1.7;margin-bottom:3mm;">
        Trabajador acudio a la consulta el dia de hoy y se
        Diagnostica <span style="display:inline-block;border-bottom:1pt solid #003399;min-width:50mm;padding:0 2mm;">{{ $receta->diagnostico ?? '' }}</span>
        por lo cual amerita el siguiente tratamiento medico:
      </p>

      @if($receta->tipo_receta === 'digital' && $receta->items->isNotEmpty())
        <table class="meds">
          @foreach($receta->items as $i => $item)
          <tr>
            <td style="width:6mm;font-weight:bold;color:#003399;vertical-align:top;">{{ $i + 1 }}.</td>
            <td>
              <div style="font-weight:bold;font-size:9.5pt;">{{ $item->nombre_medicamento }}
                @if($item->presentacion)<span style="font-weight:400;font-size:8.5pt;"> ({{ $item->presentacion }})</span>@endif
              </div>
              <div style="font-size:8.5pt;color:#555;margin-top:.5mm;">
                Dosis: <strong>{{ $item->dosis }}</strong> &nbsp;&middot;&nbsp;
                Frecuencia: <strong>{{ $item->frecuencia }}</strong> &nbsp;&middot;&nbsp;
                Duracion: <strong>{{ $item->duracion }}</strong> &nbsp;&middot;&nbsp;
                Cantidad: <strong>{{ $item->cantidad }}</strong>
                @if($item->indicaciones)<br>{{ $item->indicaciones }}@endif
              </div>
            </td>
          </tr>
          @endforeach
        </table>
      @else
        <div class="blank-line"></div>
        <div class="blank-line"></div>
        <div class="blank-line"></div>
        <div class="blank-line"></div>
      @endif

      @if($receta->indicaciones_generales)
      <div style="font-size:8.5pt;color:#555;border-top:1pt dashed #d0d8f0;padding-top:2mm;margin-top:2mm;">
        <strong>Indicaciones generales:</strong> {{ $receta->indicaciones_generales }}
      </div>
      @endif
    </td>
  </tr>
</table>

{{-- FECHA --}}
<table style="width:100%;border-collapse:collapse;margin-top:4mm;">
<tr>
  <td style="font-size:10pt;font-weight:bold;vertical-align:bottom;">
    Fecha <span style="display:inline-block;border-bottom:1.5pt solid #1a1a1a;min-width:35mm;padding:0 3mm;">{{ $receta->fecha_emision->format('d/m/Y') }}</span>
  </td>
</tr>
</table>

{{-- FIRMA --}}
<table class="firma"><tr>
  <td>
    <div class="fline"></div>
    <div class="flbl">Firma del Trabajador</div>
    <div class="fsub">C.I.: {{ $receta->paciente->cedula }}</div>
  </td>
  <td>
    <div class="fline"></div>
    <div class="flbl">{{ $receta->medico_nombre }}</div>
    <div class="fsub">Medico Ocupacional - Clinica Popular</div>
  </td>
</tr></table>

<div class="pie">{{ $receta->numero_receta }} | Generado: {{ now()->format('d/m/Y H:i') }} | SISGECLIN</div>

</body>
</html>
