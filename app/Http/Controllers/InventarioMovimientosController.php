<?php

namespace App\Http\Controllers;

/**
 * Controlador de Movimientos de Inventario
 * 
 * Registra entradas y salidas de medicamentos del inventario.
 * Actualiza el stock automáticamente y genera alertas cuando el stock es bajo.
 * Registra cada movimiento en la bitácora del sistema.
 * 
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Bitacora;
use App\Models\InventarioMovimiento;
use App\Models\Medicamento;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class InventarioMovimientosController extends Controller
{
    public function index(Request $request)
    {
        $periodo = $request->get('periodo', 'semanal');
        $tipo    = $request->get('tipo', '');
        $search  = $request->get('search', '');
        $perPage = in_array($request->get('per_page', 15), [10, 15, 20, 50])
            ? (int) $request->get('per_page', 15)
            : 15;

        [$fechaInicio, $fechaFin] = $this->getPeriodoDates($periodo);

        $movimientos = InventarioMovimiento::with([
                'medicamento',
                'usuario',
                'paciente',
                'consulta.recetas',
            ])
            ->byDateRange($fechaInicio, $fechaFin)
            ->when($tipo, fn ($q) => $q->where('tipo_movimiento', $tipo))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('medicamento', function ($m) use ($search) {
                    $m->where('nombre_medicamento', 'like', "%{$search}%");
                })->orWhere('motivo', 'like', "%{$search}%")
                  ->orWhere('tipo_movimiento', 'like', "%{$search}%")
                  ->orWhereHas('usuario', function ($u) use ($search) {
                      $u->where('nombre_completo', 'like', "%{$search}%");
                  });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $estadisticas = InventarioMovimiento::select(
                'tipo_movimiento',
                DB::raw('COUNT(*) as total_movimientos'),
                DB::raw('SUM(cantidad) as total_cantidad'),
                DB::raw('DATE(created_at) as fecha')
            )
            ->byDateRange($fechaInicio, $fechaFin)
            ->groupBy('tipo_movimiento', DB::raw('DATE(created_at)'))
            ->orderByDesc('fecha')
            ->get();

        return view('inventario.index', compact('movimientos', 'estadisticas', 'periodo', 'tipo', 'perPage', 'search'));
    }

    public function registrarEntrada(Request $request)
    {
        $request->validate([
            'medicamento_id' => 'required|exists:inventario,id',
            'cantidad'       => 'required|integer|min:1',
            'motivo'         => 'nullable|string|max:255',
        ]);

        try {
            $medicamento      = Medicamento::findOrFail($request->medicamento_id);
            $cantidadAnterior = $medicamento->cantidad;
            $cantidadNueva    = $cantidadAnterior + $request->cantidad;

            DB::transaction(function () use ($request, $medicamento, $cantidadAnterior, $cantidadNueva) {
                $medicamento->update(['cantidad' => $cantidadNueva]);

                $movimiento = InventarioMovimiento::create([
                    'medicamento_id'   => $medicamento->id,
                    'tipo_movimiento'  => 'entrada',
                    'cantidad'         => $request->cantidad,
                    'cantidad_anterior'=> $cantidadAnterior,
                    'cantidad_nueva'   => $cantidadNueva,
                    'motivo'           => $request->motivo ?? 'Entrada de inventario',
                    'usuario_id'       => Auth::id(),
                ]);

                try {
                    Bitacora::registrar(
                        Auth::id(),
                        'ENTRADA INVENTARIO',
                        'inventario_movimientos',
                        $movimiento->id,
                        "Entrada de {$request->cantidad} unidades de {$medicamento->nombre_medicamento}"
                    );
                } catch (\Throwable $e) { /* bitácora no crítica */ }

                try {
                    Notificacion::notificarTodos(
                        tipo:      'entrada_inventario',
                        titulo:    'Entrada de Inventario',
                        mensaje:   "+{$request->cantidad} unidades de {$medicamento->nombre_medicamento} ingresadas al stock",
                        icono:     'bx-up-arrow-circle',
                        color:     'success',
                        url:       '/inventario',
                        excluirId: Auth::id()
                    );
                } catch (\Throwable $e) { /* notificación no crítica */ }
            });

            return response()->json(['success' => true, 'message' => 'Entrada registrada correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al registrar entrada: ' . $e->getMessage()], 500);
        }
    }

    public function registrarSalida(Request $request)
    {
        // Validar los campos del formulario de salida, incluyendo los de firma
        $request->validate([
            'medicamento_id'        => 'required|exists:inventario,id',
            'cantidad'              => 'required|integer|min:1',
            'motivo'                => 'nullable|string|max:255',
            'consulta_id'           => 'nullable|exists:consultas,id',
            'paciente_id'           => 'nullable|exists:pacientes,id',
            // Campos de firma — opcionales pero recomendados para auditoría
            'entregado_por_nombre'  => 'nullable|string|max:150',
            'recibido_por_nombre'   => 'nullable|string|max:150',
            'recibido_por_cedula'   => 'nullable|string|max:20',
            'observaciones_entrega' => 'nullable|string',
        ]);

        try {
            $medicamento = Medicamento::findOrFail($request->medicamento_id);

            // Verificar que hay suficiente stock antes de proceder
            if ($medicamento->cantidad < $request->cantidad) {
                return response()->json([
                    'success' => false,
                    'message' => "Stock insuficiente de {$medicamento->nombre_medicamento}. Disponible: {$medicamento->cantidad}",
                ], 422);
            }

            $cantidadAnterior = $medicamento->cantidad;
            $cantidadNueva    = $cantidadAnterior - $request->cantidad;

            // Usar transacción para garantizar consistencia:
            // si algo falla, el stock NO se actualiza y el movimiento NO se crea
            DB::transaction(function () use ($request, $medicamento, $cantidadAnterior, $cantidadNueva) {
                // Actualizar el stock del medicamento
                $medicamento->update(['cantidad' => $cantidadNueva]);

                // Crear el registro del movimiento con todos los campos de firma
                $movimiento = InventarioMovimiento::create([
                    'medicamento_id'        => $medicamento->id,
                    'tipo_movimiento'       => 'salida',
                    'cantidad'              => $request->cantidad,
                    'cantidad_anterior'     => $cantidadAnterior,
                    'cantidad_nueva'        => $cantidadNueva,
                    'motivo'                => $request->motivo ?? 'Salida de inventario',
                    'consulta_id'           => $request->consulta_id,
                    'paciente_id'           => $request->paciente_id,
                    'usuario_id'            => Auth::id(),
                    // Datos de firma para el comprobante de entrega
                    'entregado_por_nombre'  => $request->entregado_por_nombre ?? Auth::user()->nombre_completo,
                    'recibido_por_nombre'   => $request->recibido_por_nombre,
                    'recibido_por_cedula'   => $request->recibido_por_cedula,
                    'observaciones_entrega' => $request->observaciones_entrega,
                ]);

                // Registrar en bitácora (no crítico — si falla no afecta el movimiento)
                try {
                    Bitacora::registrar(
                        Auth::id(),
                        'SALIDA INVENTARIO',
                        'inventario_movimientos',
                        $movimiento->id,
                        "Salida de {$request->cantidad} unidades de {$medicamento->nombre_medicamento}" .
                        ($request->recibido_por_nombre ? " — Recibido por: {$request->recibido_por_nombre}" : '')
                    );
                } catch (\Throwable $e) { /* bitácora no crítica */ }

                // Notificar a todos sobre la salida (no crítico)
                try {
                    Notificacion::notificarTodos(
                        tipo:      'salida_inventario',
                        titulo:    'Salida de Inventario',
                        mensaje:   "-{$request->cantidad} unidades de {$medicamento->nombre_medicamento} retiradas del stock",
                        icono:     'bx-down-arrow-circle',
                        color:     'warning',
                        url:       '/inventario',
                        excluirId: Auth::id()
                    );
                } catch (\Throwable $e) { /* notificación no crítica */ }

                // Verificar si el stock quedó bajo y notificar (no crítico)
                try {
                    if ($medicamento->fresh()->tieneStockBajo()) {
                        Notificacion::notificarTodos(
                            tipo:    'stock_bajo',
                            titulo:  'Alerta: Stock Bajo',
                            mensaje: "{$medicamento->nombre_medicamento} tiene stock bajo ({$medicamento->fresh()->cantidad} unidades)",
                            icono:   'bx-error',
                            color:   'danger',
                            url:     '/medicina'
                        );
                    }
                } catch (\Throwable $e) { /* alerta stock no crítica */ }
            });

            return response()->json([
                'success' => true,
                'message' => 'Salida registrada correctamente.',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al registrar salida: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Genera el reporte de auditoría de inventario.
     * Muestra entradas, salidas y stock actual de cada medicamento
     * para demostrar que no se perdió ningún medicamento.
     * Accesible desde la vista de movimientos.
     */
    public function reporteAuditoria(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin    = $request->get('fecha_fin',    now()->format('Y-m-d'));

        // Obtener todos los medicamentos con sus totales de entradas y salidas en el período
        $medicamentos = Medicamento::orderBy('nombre_medicamento')
            ->withCount([
                // Contar movimientos de entrada en el período
                'movimientos as total_entradas_unidades' => function ($q) use ($fechaInicio, $fechaFin) {
                    $q->where('tipo_movimiento', 'entrada')
                      ->whereBetween(\DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
                      ->select(\DB::raw('COALESCE(SUM(cantidad), 0)'));
                },
                // Contar movimientos de salida en el período
                'movimientos as total_salidas_unidades' => function ($q) use ($fechaInicio, $fechaFin) {
                    $q->where('tipo_movimiento', 'salida')
                      ->whereBetween(\DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
                      ->select(\DB::raw('COALESCE(SUM(cantidad), 0)'));
                },
            ])
            ->get();

        // Calcular totales globales para el resumen
        $totalEntradas = InventarioMovimiento::entradas()
            ->byDateRange($fechaInicio, $fechaFin)
            ->sum('cantidad');

        $totalSalidas = InventarioMovimiento::salidas()
            ->byDateRange($fechaInicio, $fechaFin)
            ->sum('cantidad');

        // Generar PDF con mPDF
        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A4',
                'margin_left'   => 12,
                'margin_right'  => 12,
                'margin_top'    => 15,
                'margin_bottom' => 15,
                'tempDir'       => storage_path('app/mpdf'),
            ]);

            $html = view('exports.auditoria-inventario-pdf', compact(
                'medicamentos', 'fechaInicio', 'fechaFin', 'totalEntradas', 'totalSalidas'
            ))->render();

            $mpdf->WriteHTML($html);
            $filename = 'Auditoria_Inventario_' . now()->format('Y-m-d') . '.pdf';
            $content  = $mpdf->Output('', 'S');

            return response($content, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar reporte: ' . $e->getMessage()], 500);
        }
    }

    public function getByMedicamento(Medicamento $medicamento)
    {
        $movimientos = InventarioMovimiento::with('usuario')
            ->where('medicamento_id', $medicamento->id)
            ->latest()
            ->limit(20)
            ->get();

        return response()->json(['success' => true, 'data' => $movimientos]);
    }

    public function exportar(Request $request)
    {
        $periodo = $request->get('periodo', 'todos');
        $tipo    = $request->get('tipo', '');

        [$fechaInicio, $fechaFin] = $this->getPeriodoDates($periodo);

        $movimientos = InventarioMovimiento::with(['medicamento', 'usuario', 'paciente', 'consulta'])
            ->when($periodo !== 'todos', fn ($q) => $q->byDateRange($fechaInicio, $fechaFin))
            ->when($tipo, fn ($q) => $q->where('tipo_movimiento', $tipo))
            ->latest()
            ->get();

        $filename = "movimientos_{$periodo}_" . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($movimientos) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, [
                'ID', 'Fecha/Hora', 'Medicamento', 'Tipo', 'Cantidad',
                'Stock Anterior', 'Stock Nuevo', 'Paciente', 'Consulta ID',
                'Entregado Por', 'Recibido Por', 'Cédula Receptor',
                'Motivo', 'Observaciones', 'Usuario'
            ]);
            foreach ($movimientos as $m) {
                fputcsv($output, [
                    $m->id,
                    $m->created_at->format('d/m/Y H:i'),
                    $m->medicamento->nombre_medicamento ?? '',
                    ucfirst($m->tipo_movimiento),
                    $m->cantidad,
                    $m->cantidad_anterior,
                    $m->cantidad_nueva,
                    $m->paciente?->nombre_completo ?? '',
                    $m->consulta_id ?? '',
                    $m->entregado_por_nombre ?? '',
                    $m->recibido_por_nombre ?? '',
                    $m->recibido_por_cedula ?? '',
                    $m->motivo ?? '',
                    $m->observaciones_entrega ?? '',
                    $m->usuario->nombre_completo ?? '',
                ]);
            }
            fclose($output);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function getPeriodoDates(string $periodo): array
    {
        return match ($periodo) {
            'semanal' => [now()->subDays(7)->format('Y-m-d'), now()->format('Y-m-d')],
            'mensual' => [now()->subDays(30)->format('Y-m-d'), now()->format('Y-m-d')],
            default   => [now()->subYears(10)->format('Y-m-d'), now()->format('Y-m-d')],
        };
    }
}
