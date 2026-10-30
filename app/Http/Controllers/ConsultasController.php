<?php

namespace App\Http\Controllers;

/**
 * Controlador de Consultas Médicas
 *
 * Gestiona el CRUD completo de consultas médicas de la clínica.
 * Cada consulta pertenece a un paciente y puede ser atendida por:
 *   - Un empleado de nómina con cargo médico (empleado_id)
 *   - Un usuario del sistema con rol médico (medico_id)
 * Al menos uno de los dos debe estar asignado.
 *
 * Al crear una consulta, notifica automáticamente a todos los usuarios activos.
 *
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Consulta;
use App\Models\Empleado;
use App\Models\Notificacion;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ConsultasController extends Controller
{
    /**
     * Muestra la lista paginada de consultas con búsqueda y filtros.
     *
     * La búsqueda funciona en: nombre/apellido/cédula del paciente,
     * motivo de la consulta, estado y diagnóstico.
     * Carga las relaciones paciente, empleado y medico para evitar N+1 queries.
     *
     * @param Request $request Puede contener: search, per_page
     */
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $perPage = in_array($request->get('per_page', 15), [10, 15, 20, 50])
            ? (int) $request->get('per_page', 15)
            : 15;

        // Cargar relaciones con eager loading para evitar N+1 queries
        $consultas = Consulta::with(['paciente', 'empleado', 'medico'])
            ->when($search, function ($q) use ($search) {
                // Buscar en datos del paciente (relación) o en campos de la consulta
                $q->whereHas('paciente', function ($p) use ($search) {
                    $p->where('nombre',   'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%")
                      ->orWhere('cedula',   'like', "%{$search}%");
                })->orWhere('motivo',      'like', "%{$search}%")
                  ->orWhere('estado',      'like', "%{$search}%")
                  ->orWhere('diagnostico', 'like', "%{$search}%");
            })
            ->latest('fecha_hora')   // ordenar por fecha de consulta, más recientes primero
            ->paginate($perPage)
            ->withQueryString();

        // Cargar listas para los selectores del formulario de nueva consulta
        $pacientes = Paciente::orderBy('nombre')->get();
        $medicos   = Empleado::medicos()->orderBy('nombre')->get(); // solo empleados con cargo médico

        return view('consultas.index', compact('consultas', 'pacientes', 'medicos', 'perPage', 'search'));
    }

    /**
     * Registra una nueva consulta médica.
     *
     * Valida que al menos un médico esté asignado (empleado_id o medico_id).
     * Crea la consulta y envía notificación a todos los usuarios activos.
     *
     * @param Request $request Datos del formulario de nueva consulta
     * @return JsonResponse { success, message, id }
     */
    public function store(Request $request)
    {
        // Validar los campos del formulario
        $data = $request->validate([
            'paciente_id'  => 'required|exists:pacientes,id',  // debe existir en BD
            'empleado_id'  => 'nullable|exists:nomina,id',     // médico de nómina (opcional)
            'medico_id'    => 'nullable|exists:usuarios,id',   // usuario médico (opcional)
            'fecha_hora'   => 'required|date',
            'motivo'       => 'required|string',
            'estado'       => 'required|in:pendiente,en_proceso,completada,cancelada',
        ], [
            'paciente_id.required' => 'Seleccione un paciente.',
            'fecha_hora.required'  => 'La fecha y hora son requeridas.',
            'motivo.required'      => 'El motivo es requerido.',
        ]);

        // Verificar que al menos un médico esté asignado
        // Una consulta sin médico no tiene sentido clínicamente
        if (empty($data['empleado_id']) && empty($data['medico_id'])) {
            return response()->json(['success' => false, 'message' => 'Seleccione un médico.'], 422);
        }

        try {
            // Crear la consulta en la base de datos
            $consulta = Consulta::create($data);

            // Notificar a todos los usuarios sobre la nueva consulta
            // Cargar la relación paciente para incluir su nombre en el mensaje
            try {
                $consulta->load('paciente');
                Notificacion::notificarTodos(
                    tipo:      'consulta_nueva',
                    titulo:    'Nueva Consulta Registrada',
                    mensaje:   "Consulta para {$consulta->paciente->nombre_completo} — {$consulta->fecha_hora->format('d/m/Y H:i')}",
                    icono:     'bx-clipboard',
                    color:     'primary',
                    url:       '/consultas',
                    excluirId: \Illuminate\Support\Facades\Auth::id() // no notificar a quien registró
                );
            } catch (\Throwable $e) { /* notificación no crítica */ }

            return response()->json([
                'success' => true,
                'message' => 'Consulta registrada correctamente.',
                'id'      => $consulta->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna los datos de una consulta con sus relaciones en formato JSON.
     * Se usa para mostrar el modal de detalles y pre-llenar el de edición.
     *
     * @param Consulta $consulta Consulta a mostrar (Route Model Binding)
     * @return JsonResponse { success, data }
     */
    public function show(Consulta $consulta)
    {
        // Cargar relaciones para incluir datos del paciente y médico en la respuesta
        $consulta->load(['paciente', 'empleado', 'medico']);
        return response()->json(['success' => true, 'data' => $consulta]);
    }

    /**
     * Actualiza los datos de una consulta existente.
     *
     * Permite actualizar todos los campos incluyendo diagnóstico y tratamiento
     * que se completan después de la consulta.
     *
     * @param Request  $request  Datos actualizados
     * @param Consulta $consulta Consulta a actualizar (Route Model Binding)
     * @return JsonResponse { success, message }
     */
    public function update(Request $request, Consulta $consulta)
    {
        $data = $request->validate([
            'paciente_id'  => 'required|exists:pacientes,id',
            'empleado_id'  => 'nullable|exists:nomina,id',
            'medico_id'    => 'nullable|exists:usuarios,id',
            'fecha_hora'   => 'required|date',
            'motivo'       => 'required|string',
            'diagnostico'  => 'nullable|string',  // se llena después de la consulta
            'tratamiento'  => 'nullable|string',  // se llena después de la consulta
            'estado'       => 'required|in:pendiente,en_proceso,completada,cancelada',
        ]);

        try {
            $consulta->update($data);
            return response()->json(['success' => true, 'message' => 'Consulta actualizada correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina una consulta del sistema.
     *
     * @param Consulta $consulta Consulta a eliminar (Route Model Binding)
     * @return JsonResponse { success, message }
     */
    public function destroy(Consulta $consulta)
    {
        try {
            $consulta->delete();
            return response()->json(['success' => true, 'message' => 'Consulta eliminada correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Exporta consultas en formato CSV según el período seleccionado.
     *
     * Períodos disponibles: semanal (7 días), mensual (30 días),
     * rango (fechas personalizadas), todos (sin filtro de fecha).
     *
     * @param Request $request Puede contener: periodo, fecha_inicio, fecha_fin
     * @return StreamedResponse Archivo CSV para descarga
     */
    public function exportar(Request $request)
    {
        // Obtener parámetros de período
        $periodo     = $request->get('periodo', 'todos');
        $fechaInicio = $request->get('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin    = $request->get('fecha_fin', now()->format('Y-m-d'));

        // Construir la consulta base con relaciones
        $query = Consulta::with(['paciente', 'empleado', 'medico']);

        // Aplicar filtro de período y definir nombre del archivo
        if ($periodo === 'semanal') {
            $query->byDateRange(now()->subDays(7)->format('Y-m-d'), now()->format('Y-m-d'));
            $filename = 'consultas_semanal_' . now()->format('Y-m-d') . '.csv';
        } elseif ($periodo === 'mensual') {
            $query->byDateRange(now()->subDays(30)->format('Y-m-d'), now()->format('Y-m-d'));
            $filename = 'consultas_mensual_' . now()->format('Y-m-d') . '.csv';
        } elseif ($periodo === 'rango') {
            // Rango personalizado de fechas
            $query->byDateRange($fechaInicio, $fechaFin);
            $filename = "consultas_{$fechaInicio}_a_{$fechaFin}.csv";
        } else {
            // Sin filtro de fecha — exportar todas
            $filename = 'consultas_todas_' . now()->format('Y-m-d') . '.csv';
        }

        // Obtener los resultados ordenados por fecha
        $consultas = $query->latest('fecha_hora')->get();

        // Headers HTTP para forzar la descarga
        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // Callback que escribe el CSV al stream de salida
        $callback = function () use ($consultas) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8 para Excel
            fputcsv($output, ['ID', 'Paciente', 'Cédula', 'Médico', 'Fecha/Hora', 'Motivo', 'Diagnóstico', 'Tratamiento', 'Estado', 'Fecha Registro']);
            foreach ($consultas as $c) {
                fputcsv($output, [
                    $c->id,
                    $c->paciente->nombre_completo,
                    $c->paciente->cedula,
                    $c->medico_nombre,                    // accessor del modelo
                    $c->fecha_hora->format('d/m/Y H:i'),
                    $c->motivo,
                    $c->diagnostico ?? '',
                    $c->tratamiento ?? '',
                    $c->estado_texto,                     // accessor: 'en_proceso' → 'En proceso'
                    $c->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($output);
        };

        return Response::stream($callback, 200, $headers);
    }
}
