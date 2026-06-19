<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\InformeMedico;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Informes Médicos Ocupacionales
 * CRUD + generación de PDF en formato Maxical.
 * @version 1.0
 */
class InformeMedicoController extends Controller
{
    /**
     * Lista todos los informes con búsqueda y paginación.
     */
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $perPage = in_array($request->get('per_page', 15), [10, 15, 20, 50])
            ? (int) $request->get('per_page', 15) : 15;

        $informes = InformeMedico::with(['paciente', 'empleado', 'medico'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('paciente', fn ($p) =>
                    $p->where('nombre',   'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%")
                      ->orWhere('cedula',   'like', "%{$search}%")
                )->orWhere('numero_informe', 'like', "%{$search}%")
                 ->orWhere('tipo_consulta',  'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $pacientes = Paciente::orderBy('nombre')->get();
        $medicos   = Empleado::medicos()->orderBy('nombre')->get();

        return view('informes-medicos.index', compact('informes', 'search', 'perPage', 'pacientes', 'medicos'));
    }

    /**
     * Crea un nuevo informe médico.
     */
    public function store(Request $request)
    {
        $tipoDoc = $request->input('tipo_documento', 'digital');

        $rules = [
            'paciente_id'         => 'required|exists:pacientes,id',
            'consulta_id'         => 'nullable|exists:consultas,id',
            'empleado_id'         => 'nullable|exists:nomina,id',
            'fecha'               => 'required|date',
            'tipo_documento'      => 'required|in:digital,manuscrita',
        ];

        // Reglas adicionales solo para digital
        if ($tipoDoc === 'digital') {
            $rules += [
                'nombre_empresa'        => 'nullable|string|max:150',
                'edad'                  => 'nullable|integer|min:1|max:120',
                'cargo'                 => 'nullable|string|max:120',
                'antiguedad_cargo'      => 'nullable|string|max:80',
                'tipo_consulta'         => 'nullable|in:preempleo,prevacacional,postvacacional,egreso,reubicacion,otros',
                'tipo_consulta_otros'   => 'nullable|string|max:120',
                'aptitud'               => 'nullable|in:apto,no_apto,apto_condicionado',
                'diagnostico'           => 'nullable|string',
                'estudios_paraclinicos' => 'nullable|string',
                'referencia'            => 'nullable|string',
                'recomendaciones'       => 'nullable|string',
            ];
        } else {
            $rules['imagen_escaneo'] = 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:5120';
        }

        $data = $request->validate($rules);

        try {
            $data['numero_informe'] = InformeMedico::generarNumero();
            $data['medico_id']      = null;
            $data['tipo_documento'] = $tipoDoc;

            // Guardar imagen si es manuscrita
            if ($tipoDoc === 'manuscrita' && $request->hasFile('imagen_escaneo')) {
                $data['imagen_escaneo'] = $request->file('imagen_escaneo')
                    ->store('informes-escaneos', 'public');
            }

            // Determinar médico desde la consulta si aplica
            if (!empty($data['consulta_id']) && empty($data['empleado_id'])) {
                $consulta = \App\Models\Consulta::find($data['consulta_id']);
                $data['empleado_id'] = $consulta?->empleado_id;
                $data['medico_id']   = $consulta?->medico_id;
            }

            $informe = InformeMedico::create($data);

            return response()->json([
                'success'        => true,
                'message'        => 'Informe médico registrado correctamente.',
                'id'             => $informe->id,
                'numero_informe' => $informe->numero_informe,
                'tipo_documento' => $informe->tipo_documento,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna los datos de un informe en JSON.
     */
    public function show(InformeMedico $informeMedico)
    {
        $informeMedico->load(['paciente', 'consulta', 'empleado', 'medico']);
        return response()->json(['success' => true, 'data' => $informeMedico]);
    }

    /**
     * Actualiza un informe existente.
     */
    public function update(Request $request, InformeMedico $informeMedico)
    {
        $data = $request->validate([
            'paciente_id'         => 'required|exists:pacientes,id',
            'consulta_id'         => 'nullable|exists:consultas,id',
            'empleado_id'         => 'nullable|exists:nomina,id',
            'nombre_empresa'      => 'nullable|string|max:150',
            'edad'                => 'nullable|integer|min:1|max:120',
            'cargo'               => 'nullable|string|max:120',
            'antiguedad_cargo'    => 'nullable|string|max:80',
            'tipo_consulta'       => 'nullable|in:preempleo,prevacacional,postvacacional,egreso,reubicacion,otros',
            'tipo_consulta_otros' => 'nullable|string|max:120',
            'aptitud'             => 'nullable|in:apto,no_apto,apto_condicionado',
            'diagnostico'         => 'nullable|string',
            'estudios_paraclinicos' => 'nullable|string',
            'referencia'          => 'nullable|string',
            'recomendaciones'     => 'nullable|string',
            'fecha'               => 'required|date',
        ]);

        try {
            $informeMedico->update($data);
            return response()->json(['success' => true, 'message' => 'Informe actualizado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina un informe.
     */
    public function destroy(InformeMedico $informeMedico)
    {
        try {
            $numero = $informeMedico->numero_informe;
            $informeMedico->delete();
            return response()->json([
                'success' => true,
                'message' => "Informe {$numero} eliminado correctamente.",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Genera el PDF del informe médico en formato Maxical A5.
     * Si es manuscrita, sirve la imagen/PDF escaneada directamente.
     */
    public function imprimir(InformeMedico $informeMedico)
    {
        $informeMedico->load(['paciente', 'consulta', 'empleado', 'medico']);

        // ── Manuscrita: devolver el archivo escaneado directamente ────────────
        if ($informeMedico->tipo_documento === 'manuscrita' && $informeMedico->imagen_escaneo) {
            $path = storage_path('app/public/' . $informeMedico->imagen_escaneo);
            if (file_exists($path)) {
                $mime     = mime_content_type($path);
                $filename = "Informe_{$informeMedico->numero_informe}_escaneo." . pathinfo($path, PATHINFO_EXTENSION);
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

            $html = view('informes-medicos.pdf', [
                'informe' => $informeMedico,
                'logoSrc' => $logoSrc,
            ])->render();
            $mpdf->WriteHTML($html);

            $filename = "Informe_{$informeMedico->numero_informe}.pdf";
            return response($mpdf->Output('', 'S'), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "inline; filename=\"{$filename}\"",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna los informes de un paciente para el expediente.
     */
    public function porPaciente(Paciente $paciente)
    {
        $informes = InformeMedico::with(['empleado', 'medico'])
            ->where('paciente_id', $paciente->id)
            ->latest('fecha')
            ->get()
            ->map(fn ($i) => [
                'id'             => $i->id,
                'numero_informe' => $i->numero_informe,
                'fecha'          => $i->fecha->format('d/m/Y'),
                'tipo_consulta'  => $i->tipo_consulta_texto,
                'aptitud'        => $i->aptitud_texto,
                'medico'         => $i->medico_nombre,
            ]);

        return response()->json(['success' => true, 'data' => $informes]);
    }
}
