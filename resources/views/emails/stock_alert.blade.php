<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Alerta de Inventario</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.5; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .header { background: #de350b; color: white; padding: 15px; border-radius: 6px 6px 0 0; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f4f5f7; }
        .critical { color: #de350b; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>⚠️ Alerta de Inventario Crítico</h2>
        </div>
        <p>Hola Administrador,</p>
        <p>El sistema ha detectado que los siguientes medicamentos han alcanzado su nivel de stock mínimo o están agotados:</p>

        <table>
            <thead>
                <tr>
                    <th>Medicamento</th>
                    <th>Stock Actual</th>
                    <th>Mínimo Permitido</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($medicamentosCriticos as $med)
                    <tr>
                        <td>{{ $med->nombre_medicamento }}</td>
                        <td class="critical">{{ $med->cantidad }} {{ $med->unidad }}</td>
                        <td>{{ $med->stock_minimo }} {{ $med->unidad }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style="margin-top: 30px;">
            Por favor, gestione el reabastecimiento lo antes posible.<br>
            <em>Generado automáticamente por SISGECLIN.</em>
        </p>
    </div>
</body>
</html>
