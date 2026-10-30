<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use App\Models\Consulta;
use App\Models\InventarioMovimiento;
use App\Models\Medicamento;
use App\Models\Notificacion;
use App\Models\Receta;
use App\Models\RecetaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Recetas Médicas
 * Genera, imprime y gestiona recetas por consulta.
 * @version 1.0
 */
class RecetasController extends Controller
{
    /**
     * Lista todas las recetas con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $perPage = in_array($request->get('per_page', 15), [10, 15, 20, 50])
            ? (int) $request->get('per_page', 15) : 15;

        $recetas = Receta::with(['paciente', 'consulta', 'empleado', 'medico', 'items'])
            ->when($search, function ($q) use ($search) {
                $q->where('numero_receta', 'like', "%{$search}%")
                  ->orWhereHas('paciente', fn ($p) =>
                      $p->where('nombre', 'like', "%{$search}%")
                        ->orWhere('apellido', 'like', "%{$search}%")
                        ->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$search}%"])
                        ->orWhere('cedula', 'like', "%{$search}%")
                  );
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $medicamentos = Medicamento::orderBy('nombre_medicamento')->get();
        $pacientes    = \App\Models\Paciente::orderBy('nombre')->get();

        return view('recetas.index', compact('recetas', 'search', 'perPage', 'medicamentos', 'pacientes'));
    }

    /**
     * Crea una receta para una consulta.
     */
    public function store(Request $request)
    {
        $request->validate([
            'consulta_id'                => 'nullable|exists:consultas,id',
            'paciente_id'                => 'required|exists:pacientes,id',
            'tipo_receta'                => 'required|in:digital,manuscrita',
            'diagnostico'                => 'nullable|string',
            'indicaciones_generales'     => 'nullable|string',
            'fecha_vencimiento'          => 'nullable|date|after:today',
            // Archivo para receta manuscrita (imagen o PDF)
            'imagen_receta'              => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            // Items para receta digital
            'items'                      => 'nullable|array',
            'items.*.nombre_medicamento' => 'required_if:tipo_receta,digital|string|max:150',
            'items.*.dosis'              => 'required_if:tipo_receta,digital|string|max:100',
            'items.*.frecuencia'         => 'required_if:tipo_receta,digital|string|max:100',
            'items.*.duracion'           => 'required_if:tipo_receta,digital|string|max:100',
            'items.*.cantidad'           => 'required_if:tipo_receta,digital|integer|min:1',
            'items.*.indicaciones'       => 'nullable|string',
            'items.*.medicamento_id'     => 'nullable|exists:inventario,id',
            'items.*.presentacion'       => 'nullable|string|max:100',
        ], [
            'paciente_id.required' => 'Seleccione un paciente.',
            'imagen_receta.mimes'  => 'El archivo debe ser una imagen o PDF (JPG, PNG, WEBP, PDF).',
            'imagen_receta.max'    => 'El archivo no puede superar 5MB.',
        ]);

        try {
            // Determinar médico desde la consulta si existe
            $medicoId   = null;
            $empleadoId = null;
            $receta     = null;
            if ($request->consulta_id) {
                $consulta   = \App\Models\Consulta::find($request->consulta_id);
                $medicoId   = $consulta?->medico_id;
                $empleadoId = $consulta?->empleado_id;
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $medicoId, $empleadoId, &$receta) {
                // Manejar archivo de receta manuscrita
                $imagenPath = null;
                if ($request->hasFile('imagen_receta')) {
                    $imagenPath = $request->file('imagen_receta')
                        ->store('recetas', 'public'); // storage/app/public/recetas/
                }

                $receta = \App\Models\Receta::create([
                    'numero_receta'          => \App\Models\Receta::generarNumero(),
                    'consulta_id'            => $request->consulta_id,
                    'paciente_id'            => $request->paciente_id,
                    'medico_id'              => $medicoId,
                    'empleado_id'            => $empleadoId,
                    'diagnostico'            => $request->diagnostico,
                    'indicaciones_generales' => $request->indicaciones_generales,
                    'imagen_receta'          => $imagenPath,
                    'tipo_receta'            => $request->tipo_receta ?? 'digital',
                    'fecha_emision'          => today(),
                    'fecha_vencimiento'      => $request->fecha_vencimiento,
                    'estado'                 => 'activa',
                ]);

                // Solo crear items si es receta digital
                if ($request->tipo_receta === 'digital' && $request->items) {
                    foreach ($request->items as $item) {
                        \App\Models\RecetaItem::create([
                            'receta_id'          => $receta->id,
                            'medicamento_id'     => $item['medicamento_id'] ?? null,
                            'nombre_medicamento' => $item['nombre_medicamento'],
                            'presentacion'       => $item['presentacion'] ?? null,
                            'dosis'              => $item['dosis'],
                            'frecuencia'         => $item['frecuencia'],
                            'duracion'           => $item['duracion'],
                            'cantidad'           => $item['cantidad'],
                            'indicaciones'       => $item['indicaciones'] ?? null,
                        ]);
                    }
                }
                // Nota: la dispensación (salida de inventario) se confirma por separado
                // a través de POST /recetas/{receta}/dispensar para que el usuario
                // pueda indicar qué medicamentos entregó del stock y cuáles el paciente
                // debe adquirir por cuenta propia.
            });

            // Preparar datos de dispensación para el modal de confirmación
            $receta->load('items.medicamento');
            $itemsConStock = $receta->items
                ->where('medicamento_id', '!=', null)
                ->map(function ($it) use ($receta) {
                    $med = $it->medicamento;
                    return [
                        'item_id'          => $it->id,
                        'medicamento_id'   => $it->medicamento_id,
                        'nombre'           => $it->nombre_medicamento,
                        'cantidad'         => $it->cantidad,
                        'stock'            => $med ? $med->cantidad : 0,
                        'unidad'           => $med ? ($med->unidad ?? 'unidades') : 'unidades',
                        'stock_suficiente' => $med ? $med->cantidad >= $it->cantidad : false,
                        'sin_stock'        => $med ? $med->cantidad <= 0 : true,
                    ];
                })
                ->values();

            return response()->json([
                'success'         => true,
                'message'         => 'Receta guardada correctamente.',
                'receta_id'       => $receta->id,
                'numero_receta'   => $receta->numero_receta,
                'tipo_receta'     => $receta->tipo_receta,
                'items_con_stock' => $itemsConStock,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna las consultas de un paciente para el selector del modal de recetas.
     */
    public function consultasPorPaciente(\App\Models\Paciente $paciente)
    {
        $consultas = Consulta::where('paciente_id', $paciente->id)
            ->whereIn('estado', ['completada', 'en_proceso'])
            ->with(['empleado', 'medico'])
            ->latest('fecha_hora')
            ->limit(20)
            ->get()
            ->map(fn ($c) => [
                'id'     => $c->id,
                'label'  => $c->fecha_hora->format('d/m/Y H:i') . ' — ' . \Illuminate\Support\Str::limit($c->motivo, 40),
                'medico' => $c->medico_nombre,
            ]);

        return response()->json(['success' => true, 'data' => $consultas]);
    }

    /**
     * Retorna los datos de una receta en JSON.
     */
    public function show(Receta $receta)
    {
        $receta->load(['paciente', 'consulta', 'empleado', 'medico', 'items.medicamento']);
        return response()->json(['success' => true, 'data' => $receta]);
    }

    /**
     * Genera el PDF imprimible de la receta.
     */
    public function imprimir(Receta $receta)
    {
        $receta->load(['paciente', 'consulta', 'empleado', 'medico', 'items.medicamento']);

        if ($receta->tipo_receta === 'manuscrita' && $receta->imagen_receta) {
            $path = storage_path('app/public/' . $receta->imagen_receta);
            if (file_exists($path)) {
                $mime     = mime_content_type($path);
                $filename = "Receta_{$receta->numero_receta}_escaneo." . pathinfo($path, PATHINFO_EXTENSION);
                return response()->file($path, [
                    'Content-Type'        => $mime,
                    'Content-Disposition' => "inline; filename=\"{$filename}\"",
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Archivo escaneado no encontrado.'], 404);
        }

        try {
            $logoPath = public_path('img/Logo_Borde_Blanco.png');
            $logoSrc  = file_exists($logoPath) ? str_replace('\\', '/', $logoPath) : null;

            $html = view('recetas.pdf', compact('receta', 'logoSrc'))->render();

            $mpdf = new \Mpdf\Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A4',
                'margin_left'   => 15,
                'margin_right'  => 15,
                'margin_top'    => 20,
                'margin_bottom' => 20,
                'tempDir'       => storage_path('app/mpdf'),
            ]);

            $mpdf->WriteHTML($html);

            $filename = "Receta_{$receta->numero_receta}.pdf";

            return response($mpdf->Output('', 'S'), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "inline; filename=\"{$filename}\"",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Genera el PDF de la receta en formato compacto A5.
     */
    public function imprimirMaxical(Receta $receta)
    {
        $receta->load(['paciente', 'consulta', 'empleado', 'medico', 'items.medicamento']);

        try {
            $logoPath = public_path('img/Logo_Borde_Blanco.png');
            $logoSrc  = file_exists($logoPath) ? str_replace('\\', '/', $logoPath) : null;

            $mpdf = new \Mpdf\Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A5',
                'margin_left'   => 14,
                'margin_right'  => 14,
                'margin_top'    => 14,
                'margin_bottom' => 14,
                'tempDir'       => storage_path('app/mpdf'),
            ]);

            $html = view('recetas.pdf_maxical', compact('receta', 'logoSrc'))->render();
            $mpdf->WriteHTML($html);

            $filename = "Receta_{$receta->numero_receta}.pdf";
            return response($mpdf->Output('', 'S'), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "inline; filename=\"{$filename}\"",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Anula una receta.
     */
    public function anular(Receta $receta)
    {
        try {
            $receta->update(['estado' => 'anulada']);
            return response()->json(['success' => true, 'message' => 'Receta anulada.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Confirma la dispensación de medicamentos de una receta al inventario.
     *
     * Recibe un array de items confirmados por el usuario:
     *   dispensar[item_id] = 'si'   → descuenta stock e imprime movimiento
     *   dispensar[item_id] = 'no'   → no afecta inventario (paciente compra externo)
     *
     * Esto reemplaza la salida automática — el usuario decide explícitamente.
     */
    public function dispensar(Receta $receta, Request $request)
    {
        $request->validate([
            'dispensar'   => 'required|array',
            'dispensar.*' => 'in:si,no',
        ]);

        if ($receta->tipo_receta !== 'digital') {
            return response()->json(['success' => false, 'message' => 'Solo se dispensan recetas digitales.'], 422);
        }

        $receta->load('items.medicamento');
        $dispensados = 0;
        $mensajes    = [];

        try {
            DB::transaction(function () use ($receta, $request, &$dispensados, &$mensajes) {
                foreach ($receta->items as $item) {
                    $decision = $request->input("dispensar.{$item->id}", 'no');

                    if ($decision !== 'si' || !$item->medicamento_id) continue;

                    $medicamento = Medicamento::find($item->medicamento_id);
                    if (!$medicamento || $medicamento->cantidad <= 0) {
                        $mensajes[] = "{$item->nombre_medicamento}: sin stock disponible.";
                        continue;
                    }

                    $cantidadSalida   = min((int) $item->cantidad, $medicamento->cantidad);
                    $cantidadAnterior = $medicamento->cantidad;
                    $cantidadNueva    = $cantidadAnterior - $cantidadSalida;

                    $medicamento->update(['cantidad' => $cantidadNueva]);

                    $movimiento = InventarioMovimiento::create([
                        'medicamento_id'       => $medicamento->id,
                        'tipo_movimiento'      => 'salida',
                        'cantidad'             => $cantidadSalida,
                        'cantidad_anterior'    => $cantidadAnterior,
                        'cantidad_nueva'       => $cantidadNueva,
                        'motivo'               => "Receta #{$receta->numero_receta} — {$item->nombre_medicamento}",
                        'consulta_id'          => $receta->consulta_id,
                        'paciente_id'          => $receta->paciente_id,
                        'usuario_id'           => Auth::id(),
                        'entregado_por_nombre' => Auth::user()->nombre_completo,
                    ]);

                    $dispensados++;

                    // Bitácora (no crítica)
                    try {
                        Bitacora::registrar(
                            Auth::id(),
                            'DISPENSACIÓN (RECETA)',
                            'inventario_movimientos',
                            $movimiento->id,
                            "Dispensación de {$cantidadSalida} u. de {$medicamento->nombre_medicamento} — Receta #{$receta->numero_receta}"
                        );
                    } catch (\Throwable $e) {}

                    // Alerta stock bajo (no crítica)
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
                    } catch (\Throwable $e) {}
                }
            });

            $msg = $dispensados > 0
                ? "{$dispensados} medicamento(s) dispensados del inventario correctamente."
                : "Receta registrada. Ningún medicamento fue dispensado del inventario.";

            if (!empty($mensajes)) {
                $msg .= ' Avisos: ' . implode(' | ', $mensajes);
            }

            return response()->json(['success' => true, 'message' => $msg, 'dispensados' => $dispensados]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al dispensar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna las recetas activas de un paciente para el selector de movimientos.
     * Incluye los ítems con stock del inventario para sincronización.
     */
    public function recetasActivasPaciente(\App\Models\Paciente $paciente)
    {
        $recetas = Receta::with(['items.medicamento'])
            ->where('paciente_id', $paciente->id)
            ->where('estado', 'activa')
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn($r) => [
                'id'            => $r->id,
                'numero_receta' => $r->numero_receta,
                'consulta_id'   => $r->consulta_id,
                'fecha'         => $r->fecha_emision->format('d/m/Y'),
                'items'         => $r->items->map(fn($it) => [
                    'med_id'   => $it->medicamento_id,
                    'nombre'   => $it->nombre_medicamento,
                    'cantidad' => $it->cantidad,
                    'stock'    => $it->medicamento?->cantidad ?? 0,
                    'bajo'     => $it->medicamento?->tieneStockBajo() ?? false,
                ]),
            ]);

        return response()->json(['success' => true, 'data' => $recetas]);
    }

    /**
     * Retorna las recetas de una consulta específica.
     */
    public function porConsulta(Consulta $consulta)
    {
        $recetas = Receta::with(['items.medicamento'])
            ->where('consulta_id', $consulta->id)
            ->get();
        return response()->json(['success' => true, 'data' => $recetas]);
    }
}
