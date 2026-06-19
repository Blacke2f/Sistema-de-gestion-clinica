<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\ExamenLaboratorio;
use App\Models\Paciente;
use Illuminate\Http\Request;

/**
 * Controlador de Solicitudes de Exámenes de Laboratorio
 * CRUD + generación de PDF en formato Maxical.
 * @version 1.0
 */
class ExamenLaboratorioController extends Controller
{
    /**
     * Lista todas las solicitudes con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $perPage = in_array($request->get('per_page', 15), [10, 15, 20, 50])
            ? (int) $request->get('per_page', 15) : 15;

        $examenes = ExamenLaboratorio::with(['paciente', 'empleado', 'medico'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('paciente', fn ($p) =>
                    $p->where('nombre',   'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%")
                      ->orWhere('cedula',   'like', "%{$search}%")
                )->orWhere('numero_solicitud', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $pacientes = Paciente::orderBy('nombre')->get();
        $medicos   = Empleado::medicos()->orderBy('nombre')->get();

        return view('examenes-laboratorio.index', compact('examenes', 'search', 'perPage', 'pacientes', 'medicos'));
    }

    /**
     * Crea una nueva solicitud de exámenes.
     */
    public function store(Request $request)
    {
        $tipoDoc = $request->input('tipo_documento', 'digital');

        $rules = [
            'paciente_id'    => 'required|exists:pacientes,id',
            'consulta_id'    => 'nullable|exists:consultas,id',
            'empleado_id'    => 'nullable|exists:nomina,id',
            'fecha'          => 'required|date',
            'tipo_documento' => 'required|in:digital,manuscrita',
        ];

        if ($tipoDoc === 'digital') {
            $rules += [
                'hematologia_completa' => 'boolean',
                'glicemia_ayunas'      => 'boolean',
                'urea'                 => 'boolean',
                'creatinina'           => 'boolean',
                'colesterol_total'     => 'boolean',
                'trigliceridos'        => 'boolean',
                'examen_orina'         => 'boolean',
                'examen_heces'         => 'boolean',
                'otros'                => 'nullable|string',
            ];
        } else {
            $rules['imagen_escaneo'] = 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120';
        }

        $data = $request->validate($rules);

        try {
            $medicoId   = null;
            $empleadoId = $data['empleado_id'] ?? null;

            if (!empty($data['consulta_id']) && !$empleadoId) {
                $consulta   = \App\Models\Consulta::find($data['consulta_id']);
                $empleadoId = $consulta?->empleado_id;
                $medicoId   = $consulta?->medico_id;
            }

            $imagenPath = null;
            if ($tipoDoc === 'manuscrita' && $request->hasFile('imagen_escaneo')) {
                $imagenPath = $request->file('imagen_escaneo')
                    ->store('examenes-escaneos', 'public');
            }

            $examen = ExamenLaboratorio::create(array_merge($data, [
                'numero_solicitud' => ExamenLaboratorio::generarNumero(),
                'medico_id'        => $medicoId,
                'empleado_id'      => $empleadoId,
                'tipo_documento'   => $tipoDoc,
                'imagen_escaneo'   => $imagenPath,
                'hematologia_completa' => $request->boolean('hematologia_completa'),
                'glicemia_ayunas'      => $request->boolean('glicemia_ayunas'),
                'urea'                 => $request->boolean('urea'),
                'creatinina'           => $request->boolean('creatinina'),
                'colesterol_total'     => $request->boolean('colesterol_total'),
                'trigliceridos'        => $request->boolean('trigliceridos'),
                'examen_orina'         => $request->boolean('examen_orina'),
                'examen_heces'         => $request->boolean('examen_heces'),
            ]));

            return response()->json([
                'success'          => true,
                'message'          => 'Solicitud de exámenes registrada correctamente.',
                'id'               => $examen->id,
                'numero_solicitud' => $examen->numero_solicitud,
                'tipo_documento'   => $examen->tipo_documento,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna los datos de una solicitud en JSON.
     */
    public function show(ExamenLaboratorio $examenLaboratorio)
    {
        $examenLaboratorio->load(['paciente', 'consulta', 'empleado', 'medico']);
        return response()->json(['success' => true, 'data' => $examenLaboratorio]);
    }

    /**
     * Actualiza una solicitud de exámenes.
     */
    public function update(Request $request, ExamenLaboratorio $examenLaboratorio)
    {
        $request->validate([
            'paciente_id'          => 'required|exists:pacientes,id',
            'consulta_id'          => 'nullable|exists:consultas,id',
            'empleado_id'          => 'nullable|exists:nomina,id',
            'fecha'                => 'required|date',
            'otros'                => 'nullable|string',
        ]);

        try {
            $examenLaboratorio->update([
                'paciente_id'          => $request->paciente_id,
                'consulta_id'          => $request->consulta_id,
                'empleado_id'          => $request->empleado_id,
                'fecha'                => $request->fecha,
                'hematologia_completa' => $request->boolean('hematologia_completa'),
                'glicemia_ayunas'      => $request->boolean('glicemia_ayunas'),
                'urea'                 => $request->boolean('urea'),
                'creatinina'           => $request->boolean('creatinina'),
                'colesterol_total'     => $request->boolean('colesterol_total'),
                'trigliceridos'        => $request->boolean('trigliceridos'),
                'examen_orina'         => $request->boolean('examen_orina'),
                'examen_heces'         => $request->boolean('examen_heces'),
                'otros'                => $request->otros,
            ]);

            return response()->json(['success' => true, 'message' => 'Solicitud actualizada correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina una solicitud de exámenes.
     */
    public function destroy(ExamenLaboratorio $examenLaboratorio)
    {
        try {
            $numero = $examenLaboratorio->numero_solicitud;
            $examenLaboratorio->delete();
            return response()->json([
                'success' => true,
                'message' => "Solicitud {$numero} eliminada correctamente.",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Genera el PDF de la solicitud en formato Maxical A5.
     * Si es manuscrita, sirve la imagen/PDF escaneada directamente.
     */
    public function imprimir(ExamenLaboratorio $examenLaboratorio)
    {
        $examenLaboratorio->load(['paciente', 'consulta', 'empleado', 'medico']);

        // ── Manuscrita: devolver el archivo escaneado directamente ────────────
        if ($examenLaboratorio->tipo_documento === 'manuscrita' && $examenLaboratorio->imagen_escaneo) {
            $path = storage_path('app/public/' . $examenLaboratorio->imagen_escaneo);
            if (file_exists($path)) {
                $mime     = mime_content_type($path);
                $filename = "Examen_{$examenLaboratorio->numero_solicitud}_escaneo." . pathinfo($path, PATHINFO_EXTENSION);
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

            $html = view('examenes-laboratorio.pdf', [
                'examen'  => $examenLaboratorio,
                'logoSrc' => $logoSrc,
            ])->render();
            $mpdf->WriteHTML($html);

            $filename = "Examenes_{$examenLaboratorio->numero_solicitud}.pdf";
            return response($mpdf->Output('', 'S'), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "inline; filename=\"{$filename}\"",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna las solicitudes de un paciente para el expediente.
     */
    public function porPaciente(Paciente $paciente)
    {
        $examenes = ExamenLaboratorio::with(['empleado', 'medico'])
            ->where('paciente_id', $paciente->id)
            ->latest('fecha')
            ->get()
            ->map(fn ($e) => [
                'id'               => $e->id,
                'numero_solicitud' => $e->numero_solicitud,
                'fecha'            => $e->fecha->format('d/m/Y'),
                'examenes'         => implode(', ', $e->examenes_solicitados),
                'medico'           => $e->medico_nombre,
            ]);

        return response()->json(['success' => true, 'data' => $examenes]);
    }
}
