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
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('medicamento', function ($m) use ($search) {
                        $m->where('nombre_medicamento', 'like', "%{$search}%");
                    })->orWhere('motivo', 'like', "%{$search}%")
                      ->orWhere('tipo_movimiento', 'like', "%{$search}%")
                      ->orWhere('recibido_por_nombre', 'like', "%{$search}%")
                      ->orWhereHas('paciente', function ($p) use ($search) {
                          $p->where('nombre', 'like', "%{$search}%")
                            ->orWhere('apellido', 'like', "%{$search}%")
                            ->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$search}%"])
                            ->orWhere('cedula', 'like', "%{$search}%");
                      })
                      ->orWhereHas('usuario', function ($u) use ($search) {
                          $u->where('nombre_completo', 'like', "%{$search}%");
                      });
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
        // Soportar tanto múltiples medicamentos (items[]) como un único medicamento (medicamento_id, cantidad)
        $hasItems = $request->has('items') && is_array($request->items);

        $rules = [
            'motivo'                => 'nullable|string|max:255',
            'consulta_id'           => 'nullable|exists:consultas,id',
            'paciente_id'           => 'nullable|exists:pacientes,id',
            'entregado_por_nombre'  => 'nullable|string|max:150',
            'recibido_por_nombre'   => 'nullable|string|max:150',
            'recibido_por_cedula'   => 'nullable|string|max:20',
            'observaciones_entrega' => 'nullable|string',
        ];

        if ($hasItems) {
            $rules['items']                  = 'required|array|min:1';
            $rules['items.*.medicamento_id'] = 'required|exists:inventario,id';
            $rules['items.*.cantidad']       = 'required|integer|min:1';
        } else {
            $rules['medicamento_id'] = 'required|exists:inventario,id';
            $rules['cantidad']       = 'required|integer|min:1';
        }

        $request->validate($rules, [
            'items.required'                  => 'Agregue al menos un medicamento.',
            'items.*.medicamento_id.required' => 'Seleccione el medicamento en todas las filas.',
            'items.*.cantidad.required'       => 'Indique la cantidad en todas las filas.',
            'items.*.cantidad.min'            => 'La cantidad mínima es 1.',
        ]);

        // Normalizar lista de ítems a procesar y agrupar cantidades si se repite el mismo medicamento
        $rawItems = $hasItems
            ? $request->items
            : [['medicamento_id' => $request->medicamento_id, 'cantidad' => $request->cantidad]];

        $grouped = [];
        foreach ($rawItems as $it) {
            $medId = (int) $it['medicamento_id'];
            $cant  = (int) $it['cantidad'];
            if (!isset($grouped[$medId])) {
                $grouped[$medId] = 0;
            }
            $grouped[$medId] += $cant;
        }

        try {
            // 1. Verificar stock disponible de todos los medicamentos antes de iniciar transacción
            $medicamentosMap = [];
            foreach ($grouped as $medId => $totalCant) {
                $med = Medicamento::findOrFail($medId);
                if ($med->cantidad < $totalCant) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuficiente de {$med->nombre_medicamento}. Solicitado: {$totalCant}, Disponible: {$med->cantidad}",
                    ], 422);
                }
                $medicamentosMap[$medId] = $med;
            }

            $totalRegistrados = 0;

            // 2. Ejecutar descuento de stock y registro de movimientos en transacción
            DB::transaction(function () use ($request, $rawItems, $medicamentosMap, &$totalRegistrados) {
                foreach ($rawItems as $it) {
                    $medId       = (int) $it['medicamento_id'];
                    $cantSalida  = (int) $it['cantidad'];
                    $medicamento = $medicamentosMap[$medId]->fresh();

                    $cantidadAnterior = $medicamento->cantidad;
                    $cantidadNueva    = $cantidadAnterior - $cantSalida;

                    $medicamento->update(['cantidad' => $cantidadNueva]);

                    $movimiento = InventarioMovimiento::create([
                        'medicamento_id'        => $medicamento->id,
                        'tipo_movimiento'       => 'salida',
                        'cantidad'              => $cantSalida,
                        'cantidad_anterior'     => $cantidadAnterior,
                        'cantidad_nueva'        => $cantidadNueva,
                        'motivo'                => $request->motivo ?? 'Salida de inventario',
                        'consulta_id'           => $request->consulta_id,
                        'paciente_id'           => $request->paciente_id,
                        'usuario_id'            => Auth::id(),
                        'entregado_por_nombre'  => $request->entregado_por_nombre ?? Auth::user()->nombre_completo,
                        'recibido_por_nombre'   => $request->recibido_por_nombre,
                        'recibido_por_cedula'   => $request->recibido_por_cedula,
                        'observaciones_entrega' => $request->observaciones_entrega,
                    ]);

                    $totalRegistrados++;

                    // Registrar en bitácora (no crítico)
                    try {
                        Bitacora::registrar(
                            Auth::id(),
                            'SALIDA INVENTARIO',
                            'inventario_movimientos',
                            $movimiento->id,
                            "Salida de {$cantSalida} unidades de {$medicamento->nombre_medicamento}" .
                            ($request->recibido_por_nombre ? " — Recibido por: {$request->recibido_por_nombre}" : '')
                        );
                    } catch (\Throwable $e) { /* bitácora no crítica */ }

                    // Notificar sobre la salida (no crítico)
                    try {
                        Notificacion::notificarTodos(
                            tipo:      'salida_inventario',
                            titulo:    'Salida de Inventario',
                            mensaje:   "-{$cantSalida} unidades de {$medicamento->nombre_medicamento} retiradas del stock",
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
                }
            });

            $msg = $totalRegistrados > 1
                ? "Se registraron {$totalRegistrados} salidas de medicamentos correctamente."
                : 'Salida registrada correctamente.';

            return response()->json([
                'success' => true,
                'message' => $msg,
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

    public function comprobanteEntrega(InventarioMovimiento $movimiento)
    {
        $movimiento->load(['medicamento', 'usuario', 'paciente', 'consulta']);

        // Agrupar movimientos hermanos si fueron registrados en la misma operación (mismo segundo/motivo/paciente)
        $itemsEntrega = InventarioMovimiento::with('medicamento')
            ->where('tipo_movimiento', $movimiento->tipo_movimiento)
            ->where('usuario_id', $movimiento->usuario_id)
            ->where('paciente_id', $movimiento->paciente_id)
            ->where('motivo', $movimiento->motivo)
            ->whereBetween('created_at', [
                $movimiento->created_at->copy()->subSeconds(5),
                $movimiento->created_at->copy()->addSeconds(5),
            ])
            ->orderBy('id')
            ->get();

        if ($itemsEntrega->isEmpty()) {
            $itemsEntrega = collect([$movimiento]);
        }

        try {
            $mpdf = new \Mpdf\Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A5-L',
                'margin_left'   => 12,
                'margin_right'  => 12,
                'margin_top'    => 12,
                'margin_bottom' => 12,
                'tempDir'       => storage_path('app/mpdf'),
            ]);

            $html = view('exports.comprobante-entrega-pdf', compact('movimiento', 'itemsEntrega'))->render();
            $mpdf->WriteHTML($html);

            $filename = 'Comprobante_Movimiento_' . $movimiento->id . '.pdf';
            return response($mpdf->Output('', 'S'), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "inline; filename=\"{$filename}\"",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar comprobante: ' . $e->getMessage()], 500);
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
