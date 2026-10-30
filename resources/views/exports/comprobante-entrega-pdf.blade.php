<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Movimiento #{{ $movimiento->id }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #0d6efd;
            margin: 0;
        }
        .subtitle {
            font-size: 10px;
            color: #64748b;
            margin: 2px 0 0;
        }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-salida { background: #fee2e2; color: #b91c1c; }
        .badge-entrada { background: #dcfce7; color: #15803d; }
        .info-box {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 10px;
            margin-bottom: 12px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 6px;
            font-size: 10.5px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .items-table th {
            background: #0f172a;
            color: #ffffff;
            text-align: left;
            padding: 6px 8px;
            font-size: 10px;
            text-transform: uppercase;
        }
        .items-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 6px 8px;
            font-size: 11px;
        }
        .sig-table {
            width: 100%;
            margin-top: 26px;
            text-align: center;
        }
        .sig-line {
            border-top: 1px solid #334155;
            width: 75%;
            margin: 0 auto 4px;
            padding-top: 4px;
            font-weight: bold;
            font-size: 10.5px;
        }
        .footer {
            margin-top: 10px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px dashed #cbd5e1;
            padding-top: 6px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="title">SISGECLIN — Acta de {{ $movimiento->tipo_movimiento === 'salida' ? 'Entrega y Salida' : 'Recepción y Entrada' }} de Medicamentos</div>
                <div class="subtitle">Control de Farmacia e Inventario Clínico · Comprobante #{{ str_pad($movimiento->id, 5, '0', STR_PAD_LEFT) }}</div>
            </td>
            <td style="text-align:right;">
                <span class="badge {{ $movimiento->tipo_movimiento === 'salida' ? 'badge-salida' : 'badge-entrada' }}">
                    {{ strtoupper($movimiento->tipo_movimiento) }} DE INVENTARIO
                </span>
                <div style="font-size:10px;color:#475569;margin-top:4px;">
                    Fecha: <strong>{{ $movimiento->created_at->format('d/m/Y H:i') }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <div class="info-box">
        <table class="info-table">
            <tr>
                <td width="50%">
                    <strong>Paciente / Beneficiario:</strong>
                    {{ $movimiento->paciente?->nombre_completo ?? ($movimiento->recibido_por_nombre ?: 'Uso interno / General') }}
                </td>
                <td width="50%">
                    <strong>Cédula / Documento:</strong>
                    {{ $movimiento->paciente?->cedula ?? ($movimiento->recibido_por_cedula ?: 'N/A') }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Motivo / Concepto:</strong>
                    {{ $movimiento->motivo ?: 'Movimiento de inventario' }}
                </td>
                <td>
                    <strong>Observaciones / Ref.:</strong>
                    {{ $movimiento->observaciones_entrega ?: ($movimiento->consulta_id ? 'Consulta #' . $movimiento->consulta_id : '—') }}
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="8%">#</th>
                <th width="52%">Medicamento / Insumo</th>
                <th width="15%">Unidad</th>
                <th width="15%" style="text-align:center;">Cantidad</th>
                <th width="10%" style="text-align:center;">Stock Post.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($itemsEntrega as $idx => $it)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td><strong>{{ $it->medicamento?->nombre_medicamento ?? 'Medicamento' }}</strong></td>
                <td>{{ ucfirst($it->medicamento?->unidad ?? 'Unidades') }}</td>
                <td style="text-align:center;font-weight:bold;">{{ $it->cantidad }}</td>
                <td style="text-align:center;color:#64748b;">{{ $it->cantidad_nueva }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="sig-table">
        <tr>
            <td width="50%">
                <div class="sig-line">{{ $movimiento->entregado_por_nombre ?: ($movimiento->usuario?->nombre_completo ?? 'Responsable de Farmacia') }}</div>
                <div style="font-size:9.5px;color:#64748b;">Entregado / Registrado por</div>
            </td>
            <td width="50%">
                <div class="sig-line">{{ $movimiento->recibido_por_nombre ?: ($movimiento->paciente?->nombre_completo ?? 'Firma del Receptor') }}</div>
                <div style="font-size:9.5px;color:#64748b;">
                    Recibido conforme {{ $movimiento->recibido_por_cedula ? '(Cédula: ' . $movimiento->recibido_por_cedula . ')' : '' }}
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Documento generado por SISGECLIN el {{ now()->format('d/m/Y H:i') }} — Válido para control interno y auditoría de inventario.
    </div>
</body>
</html>
