<?php

namespace App\Http\Controllers;

/**
 * Controlador de Nómina del Personal
 *
 * Gestiona el registro del personal médico y administrativo de la clínica.
 * Solo accesible para administradores (middleware role:admin en rutas).
 *
 * Los empleados con cargo médico (Médico General, Pediatra, etc.) aparecen
 * automáticamente en el selector de médicos del módulo de Consultas.
 * Esto se controla con el scope 'medicos' del modelo Empleado.
 *
 * Todas las acciones de creación, edición y eliminación se registran
 * en la bitácora del sistema para auditoría.
 *
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Bitacora;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class NominaController extends Controller
{
    /**
     * Muestra la lista paginada del personal con búsqueda opcional.
     *
     * El scope 'search' del modelo Empleado busca en nombre, apellido y cédula.
     *
     * @param Request $request Puede contener: search, per_page
     */
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $perPage = in_array($request->get('per_page', 15), [10, 15, 20, 50])
            ? (int) $request->get('per_page', 15)
            : 15;

        // Construir consulta con búsqueda condicional
        $empleados = Empleado::when($search, fn ($q) => $q->search($search))
            ->latest()
            ->paginate($perPage)
            ->withQueryString(); // preservar ?search=... en los links de paginación

        return view('nomina.index', compact('empleados', 'search', 'perPage'));
    }

    /**
     * Registra un nuevo empleado en la nómina.
     *
     * Si el cargo contiene palabras como "Médico", "Pediatra", etc.,
     * el empleado aparecerá automáticamente en el selector de médicos
     * del módulo de Consultas (controlado por el scope 'medicos' del modelo).
     *
     * @param Request $request Datos del formulario de nuevo empleado
     * @return JsonResponse { success, message, id }
     */
    public function store(Request $request)
    {
        // Validar todos los campos del formulario
        $data = $request->validate([
            'nombre'        => 'required|string|max:80',
            'apellido'      => 'required|string|max:80',
            'cedula'        => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('nomina', 'cedula')->whereNull('deleted_at')], // cédula única en nómina
            'cargo'         => 'required|string|max:100',   // ej: Médico General, Enfermera
            'departamento'  => 'required|string|max:100',   // ej: Consultas, Administración
            'salario'       => 'required|numeric|min:0',    // salario mensual en RD$
            'fecha_ingreso' => 'required|date',
            'telefono'      => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'estado'        => 'required|in:activo,inactivo',
        ], [
            'nombre.required'        => 'El nombre es requerido.',
            'apellido.required'      => 'El apellido es requerido.',
            'cedula.required'        => 'La cédula es requerida.',
            'cedula.unique'          => 'La cédula ya está registrada.',
            'cargo.required'         => 'El cargo es requerido.',
            'departamento.required'  => 'El departamento es requerido.',
            'fecha_ingreso.required' => 'La fecha de ingreso es requerida.',
        ]);

        try {
            // Crear el empleado en la base de datos
            $trashed = Empleado::onlyTrashed()->where('cedula', $data['cedula'])->first();
            if ($trashed) { $trashed->restore(); $trashed->update($data); $empleado = $trashed; } else { $empleado = Empleado::create($data); }

            // Registrar la acción en la bitácora (no crítico — si falla no afecta el registro)
            try {
                Bitacora::registrar(
                    Auth::id(),
                    'CREAR EMPLEADO',
                    'nomina',
                    $empleado->id,
                    "Creó el empleado '{$empleado->nombre_completo}'"
                );
            } catch (\Throwable $e) { /* bitácora no crítica */ }

            return response()->json([
                'success' => true,
                'message' => 'Empleado registrado correctamente.',
                'id'      => $empleado->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna los datos de un empleado en formato JSON.
     * Se usa para pre-llenar el modal de edición y el modal de detalles via AJAX.
     *
     * Nota: El parámetro se llama $nomina porque la ruta usa el prefijo 'nomina'
     * y Laravel resuelve el Route Model Binding por ese nombre.
     *
     * @param Empleado $nomina Empleado a mostrar (Route Model Binding)
     * @return JsonResponse { success, data }
     */
    public function show(Empleado $nomina)
    {
        return response()->json(['success' => true, 'data' => $nomina]);
    }

    /**
     * Actualiza los datos de un empleado existente.
     *
     * La validación de cédula excluye al empleado actual para permitir
     * guardar sin cambiar la cédula (unique con excepción por ID).
     *
     * @param Request  $request Datos actualizados del formulario
     * @param Empleado $nomina  Empleado a actualizar (Route Model Binding)
     * @return JsonResponse { success, message }
     */
    public function update(Request $request, Empleado $nomina)
    {
        $data = $request->validate([
            'nombre'        => 'required|string|max:80',
            'apellido'      => 'required|string|max:80',
            // unique con excepción: permite la misma cédula del empleado actual
            'cedula'        => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('nomina', 'cedula')->ignore($nomina->id)->whereNull('deleted_at')],
            'cargo'         => 'required|string|max:100',
            'departamento'  => 'required|string|max:100',
            'salario'       => 'required|numeric|min:0',
            'fecha_ingreso' => 'required|date',
            'telefono'      => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:100',
            'estado'        => 'required|in:activo,inactivo',
        ]);

        try {
            $nomina->update($data);

            // Registrar la edición en la bitácora
            try {
                Bitacora::registrar(
                    Auth::id(),
                    'EDITAR EMPLEADO',
                    'nomina',
                    $nomina->id,
                    "Editó el empleado '{$nomina->nombre_completo}'"
                );
            } catch (\Throwable $e) { /* bitácora no crítica */ }

            return response()->json(['success' => true, 'message' => 'Empleado actualizado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina un empleado de la nómina.
     *
     * Registra la eliminación en la bitácora ANTES de eliminar el registro,
     * para que el nombre del empleado quede guardado en el log.
     *
     * @param Empleado $nomina Empleado a eliminar (Route Model Binding)
     * @return JsonResponse { success, message }
     */
    public function destroy(Empleado $nomina)
    {
        try {
            // Registrar ANTES de eliminar para conservar el nombre en la bitácora
            try {
                Bitacora::registrar(
                    Auth::id(),
                    'ELIMINAR EMPLEADO',
                    'nomina',
                    $nomina->id,
                    "Eliminó el empleado '{$nomina->nombre_completo}'"
                );
            } catch (\Throwable $e) { /* bitácora no crítica */ }

            $nomina->delete();
            return response()->json(['success' => true, 'message' => 'Empleado eliminado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Exporta toda la nómina en formato CSV.
     *
     * Exporta todos los empleados sin filtro de período.
     * Incluye BOM UTF-8 para compatibilidad con Excel en Windows.
     *
     * @return StreamedResponse Archivo CSV para descarga
     */
    public function exportar()
    {
        // Obtener todos los empleados para exportar
        $empleados = Empleado::all();
        $filename  = 'nomina_' . now()->format('Y-m-d') . '.csv';

        // Headers HTTP para forzar la descarga del archivo
        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // Callback que escribe el CSV directamente al stream de salida
        $callback = function () use ($empleados) {
            $output = fopen('php://output', 'w');

            // BOM UTF-8: necesario para que Excel abra el CSV con tildes correctamente
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Escribir la fila de encabezados
            fputcsv($output, [
                'ID', 'Nombre', 'Apellido', 'Cédula', 'Cargo', 'Departamento',
                'Salario', 'Fecha Ingreso', 'Teléfono', 'Email', 'Estado', 'Fecha Registro'
            ]);

            // Escribir una fila por cada empleado
            foreach ($empleados as $e) {
                fputcsv($output, [
                    $e->id,
                    $e->nombre,
                    $e->apellido,
                    $e->cedula,
                    $e->cargo,
                    $e->departamento,
                    number_format($e->salario, 2, '.', ''), // formato numérico sin separador de miles
                    $e->fecha_ingreso?->format('d/m/Y') ?? '',
                    $e->telefono ?? '',
                    $e->email ?? '',
                    $e->estado === 'activo' ? 'Activo' : 'Inactivo',
                    $e->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($output);
        };

        return Response::stream($callback, 200, $headers);
    }
}
