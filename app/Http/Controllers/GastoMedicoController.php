<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\GastoMedico;
use App\Models\GastoMedicoItem;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Gastos Médicos
 * CRUD con ítems de gasto y generación de PDF.
 * @version 1.0
 */
class GastoMedicoController extends Controller
{
    /**
     * Lista todos los gastos con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $perPage = in_array($request->get('per_page', 15), [10, 15, 20, 50])
            ? (int) $request->get('per_page', 15) : 15;

        $gastos = GastoMedico::with(['paciente', 'empleado', 'medico', 'items'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('paciente', fn ($p) =>
                    $p->where('nombre',   'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%")
                      ->orWhere('cedula',   'like', "%{$search}%")
                )->orWhere('numero_gasto', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $pacientes = Paciente::orderBy('nombre')->get();
        $medicos   = Empleado::medicos()->orderBy('nombre')->get();

        return view('gastos-medicos.index', compact('gastos', 'search', 'perPage', 'pacientes', 'medicos'));
    }

    /**
     * Crea un nuevo gasto médico con sus ítems.
     */
    public function store(Request $request)
    {
        $tipoDoc = $request->input('tipo_documento', 'digital');

        $rules = [
            'paciente_id'    => 'required|exists:pacientes,id',
            'consulta_id'    => 'nullable|exists:consultas,id',
            'empleado_id'    => 'nullable|exists:nomina,id',
            'nombre_empresa' => 'nullable|string|max:150',
            'fecha'          => 'required|date',
            'tipo_documento' => 'required|in:digital,manuscrita',
        ];

        if ($tipoDoc === 'digital') {
            $rules += [
                'descripcion'      => 'nullable|string',
                'items'            => 'nullable|array',
                'items.*.concepto' => 'required|string|max:200',
                'items.*.monto'    => 'required|numeric|min:0',
            ];
        } else {
            $rules['imagen_escaneo'] = 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120';
        }

        $request->validate($rules);

        try {
            $gasto = null;

            DB::transaction(function () use ($request, $tipoDoc, &$gasto) {
                $medicoId   = null;
                $empleadoId = $request->empleado_id;

                if ($request->consulta_id && !$empleadoId) {
                    $consulta   = \App\Models\Consulta::find($request->consulta_id);
                    $empleadoId = $consulta?->empleado_id;
                    $medicoId   = $consulta?->medico_id;
                }

                $imagenPath = null;
                if ($tipoDoc === 'manuscrita' && $request->hasFile('imagen_escaneo')) {
                    $imagenPath = $request->file('imagen_escaneo')
                        ->store('gastos-escaneos', 'public');
                }

                $gasto = GastoMedico::create([
                    'numero_gasto'   => GastoMedico::generarNumero(),
                    'paciente_id'    => $request->paciente_id,
                    'consulta_id'    => $request->consulta_id,
                    'medico_id'      => $medicoId,
                    'empleado_id'    => $empleadoId,
                    'nombre_empresa' => $request->nombre_empresa,
                    'fecha'          => $request->fecha,
                    'descripcion'    => $request->descripcion,
                    'tipo_documento' => $tipoDoc,
                    'imagen_escaneo' => $imagenPath,
                ]);

                if ($tipoDoc === 'digital' && $request->items) {
                    foreach ($request->items as $item) {
                        GastoMedicoItem::create([
                            'gasto_medico_id' => $gasto->id,
                            'concepto'        => $item['concepto'],
                            'monto'           => $item['monto'],
                        ]);
                    }
                }
            });

            return response()->json([
                'success'        => true,
                'message'        => 'Gasto médico registrado correctamente.',
                'id'             => $gasto->id,
                'numero_gasto'   => $gasto->numero_gasto,
                'tipo_documento' => $gasto->tipo_documento,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna los datos de un gasto en JSON.
     */
    public function show(GastoMedico $gastoMedico)
    {
        $gastoMedico->load(['paciente', 'consulta', 'empleado', 'medico', 'items']);
        return response()->json(['success' => true, 'data' => $gastoMedico]);
    }

    /**
     * Actualiza un gasto médico y sus ítems.
     */
    public function update(Request $request, GastoMedico $gastoMedico)
    {
        $request->validate([
            'paciente_id'    => 'required|exists:pacientes,id',
            'consulta_id'    => 'nullable|exists:consultas,id',
            'empleado_id'    => 'nullable|exists:nomina,id',
            'nombre_empresa' => 'nullable|string|max:150',
            'fecha'          => 'required|date',
            'descripcion'    => 'nullable|string',
            'items'          => 'nullable|array',
            'items.*.concepto' => 'required|string|max:200',
            'items.*.monto'    => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $gastoMedico) {
                $gastoMedico->update([
                    'paciente_id'    => $request->paciente_id,
                    'consulta_id'    => $request->consulta_id,
                    'empleado_id'    => $request->empleado_id,
                    'nombre_empresa' => $request->nombre_empresa,
                    'fecha'          => $request->fecha,
                    'descripcion'    => $request->descripcion,
                ]);

                // Reemplazar ítems
                $gastoMedico->items()->delete();
                if ($request->items) {
                    foreach ($request->items as $item) {
                        GastoMedicoItem::create([
                            'gasto_medico_id' => $gastoMedico->id,
                            'concepto'        => $item['concepto'],
                            'monto'           => $item['monto'],
                        ]);
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'Gasto actualizado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina un gasto médico.
     */
    public function destroy(GastoMedico $gastoMedico)
    {
        try {
            $numero = $gastoMedico->numero_gasto;
            $gastoMedico->items()->delete();
            $gastoMedico->delete();
            return response()->json([
                'success' => true,
                'message' => "Gasto {$numero} eliminado correctamente.",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Genera el PDF del gasto médico.
     * Si es manuscrita, sirve la imagen/PDF escaneada directamente.
     */
    public function imprimir(GastoMedico $gastoMedico)
    {
        $gastoMedico->load(['paciente', 'consulta', 'empleado', 'medico', 'items']);

        // ── Manuscrita: devolver el archivo escaneado directamente ────────────
        if ($gastoMedico->tipo_documento === 'manuscrita' && $gastoMedico->imagen_escaneo) {
            $path = storage_path('app/public/' . $gastoMedico->imagen_escaneo);
            if (file_exists($path)) {
                $mime     = mime_content_type($path);
                $filename = "Gasto_{$gastoMedico->numero_gasto}_escaneo." . pathinfo($path, PATHINFO_EXTENSION);
                return response()->file($path, [
                    'Content-Type'        => $mime,
                    'Content-Disposition' => "inline; filename=\"{$filename}\"",
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Archivo escaneado no encontrado.'], 404);
        }

        // ── Digital: generar PDF con mPDF ─────────────────────────────────────
        try {
            $logoPath = public_path('img/Logo_Borde_Blanco.png');
            $logoSrc  = file_exists($logoPath) ? str_replace('\\', '/', $logoPath) : null;

            $mpdf = new \Mpdf\Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A5',
                'margin_left'   => 15,
                'margin_right'  => 15,
                'margin_top'    => 15,
                'margin_bottom' => 15,
                'tempDir'       => storage_path('app/mpdf'),
            ]);

            $html = view('gastos-medicos.pdf', [
                'gasto'   => $gastoMedico,
                'logoSrc' => $logoSrc,
            ])->render();
            $mpdf->WriteHTML($html);

            $filename = "Gasto_{$gastoMedico->numero_gasto}.pdf";
            return response($mpdf->Output('', 'S'), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "inline; filename=\"{$filename}\"",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna los gastos de un paciente para el expediente.
     */
    public function porPaciente(Paciente $paciente)
    {
        $gastos = GastoMedico::with(['empleado', 'medico', 'items'])
            ->where('paciente_id', $paciente->id)
            ->latest('fecha')
            ->get()
            ->map(fn ($g) => [
                'id'           => $g->id,
                'numero_gasto' => $g->numero_gasto,
                'fecha'        => $g->fecha->format('d/m/Y'),
                'total'        => number_format($g->total, 2),
                'medico'       => $g->medico_nombre,
            ]);

        return response()->json(['success' => true, 'data' => $gastos]);
    }
}
