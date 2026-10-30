<?php

namespace App\Http\Controllers;

/**
 * Controlador de Pacientes
 *
 * Gestiona el CRUD completo de pacientes de la clínica.
 * Incluye búsqueda en tiempo real, paginación configurable
 * y exportación en múltiples formatos (CSV, PDF, Excel).
 * Al registrar un nuevo paciente, notifica automáticamente a todos los usuarios.
 *
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Paciente;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PacientesController extends Controller
{
    /**
     * Muestra la lista paginada de pacientes con búsqueda opcional.
     *
     * Soporta búsqueda por nombre, apellido o cédula.
     * El número de registros por página es configurable (10, 15, 20, 50).
     * withQueryString() preserva los parámetros de búsqueda en los links de paginación.
     *
     * @param Request $request Puede contener: search (texto), per_page (número)
     */
    public function index(Request $request)
    {
        // Obtener el término de búsqueda (vacío si no se envió)
        $search  = $request->get('search', '');

        // Validar que per_page sea uno de los valores permitidos, si no usar 15
        $perPage = in_array($request->get('per_page', 15), [10, 15, 20, 50])
            ? (int) $request->get('per_page', 15)
            : 15;

        // Construir la consulta con búsqueda condicional
        // El scope 'search' del modelo busca en nombre, apellido y cédula
        $pacientes = Paciente::when($search, fn ($q) => $q->search($search))
            ->latest()                  // ordenar por created_at DESC
            ->paginate($perPage)
            ->withQueryString();        // mantener ?search=... en los links de paginación

        return view('pacientes.index', compact('pacientes', 'search', 'perPage'));
    }

    /**
     * Registra un nuevo paciente en el sistema.
     *
     * Valida todos los campos, crea el registro y envía una notificación
     * a todos los usuarios activos (excepto quien lo registró).
     * La notificación está en try/catch para que si falla no afecte el registro.
     *
     * @param Request $request Datos del formulario de nuevo paciente
     * @return JsonResponse { success, message, id }
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario con mensajes en español
        $data = $request->validate([
            'nombre'           => 'required|string|max:80',
            'apellido'         => 'required|string|max:80',
            'cedula'           => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('pacientes', 'cedula')->whereNull('deleted_at')], // cédula única activa
            'telefono'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'direccion'        => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date',
            'genero'           => 'required|in:M,F,Otro',
            'tipo_sangre'      => 'nullable|string|max:5',
            'alergias'         => 'nullable|string',
        ], [
            'nombre.required'   => 'El nombre es requerido.',
            'apellido.required' => 'El apellido es requerido.',
            'cedula.required'   => 'La cédula es requerida.',
            'cedula.unique'     => 'La cédula ya está registrada.',
        ]);

        try {
            // Crear el paciente en la base de datos
            $trashed = Paciente::onlyTrashed()->where('cedula', $data['cedula'])->first();
            if ($trashed) { $trashed->restore(); $trashed->update($data); $paciente = $trashed; } else { $paciente = Paciente::create($data); }

            // Notificar a todos los usuarios sobre el nuevo paciente
            // Si la notificación falla, el registro ya fue guardado — no es crítico
            try {
                Notificacion::notificarTodos(
                    tipo:       'paciente_nuevo',
                    titulo:     'Nuevo Paciente Registrado',
                    mensaje:    "Se registró el paciente {$paciente->nombre} {$paciente->apellido} (Cédula: {$paciente->cedula})",
                    icono:      'bxs-user-plus',
                    color:      'success',
                    url:        '/pacientes',
                    excluirId:  \Illuminate\Support\Facades\Auth::id() // no notificar a quien registró
                );
            } catch (\Throwable $e) { /* notificación no crítica — ignorar si falla */ }

            // Retornar respuesta exitosa con el ID del nuevo paciente
            return response()->json([
                'success' => true,
                'message' => 'Paciente registrado correctamente.',
                'id'      => $paciente->id,
            ]);
        } catch (\Throwable $e) {
            // Error inesperado — retornar mensaje de error sin exponer detalles internos
            return response()->json(['success' => false, 'message' => 'Error al registrar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna los datos de un paciente específico en formato JSON.
     * Se usa para pre-llenar el modal de edición via AJAX.
     *
     * Laravel resuelve automáticamente el modelo por su ID (Route Model Binding).
     *
     * @param Paciente $paciente Inyectado automáticamente por Laravel
     * @return JsonResponse { success, data }
     */
    public function show(Paciente $paciente)
    {
        return response()->json(['success' => true, 'data' => $paciente]);
    }

    /**
     * Actualiza los datos de un paciente existente.
     *
     * La validación de cédula excluye al paciente actual para permitir
     * guardar sin cambiar la cédula (unique con excepción por ID).
     *
     * @param Request  $request  Datos actualizados del formulario
     * @param Paciente $paciente Paciente a actualizar (Route Model Binding)
     * @return JsonResponse { success, message }
     */
    public function update(Request $request, Paciente $paciente)
    {
        $data = $request->validate([
            'nombre'           => 'required|string|max:80',
            'apellido'         => 'required|string|max:80',
            // unique con excepción: permite la misma cédula del paciente actual
            'cedula'           => ['required', 'string', 'max:20', \Illuminate\Validation\Rule::unique('pacientes', 'cedula')->ignore($paciente->id)->whereNull('deleted_at')],
            'telefono'         => 'nullable|string|max:20',
            'email'            => 'nullable|email|max:100',
            'direccion'        => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date',
            'genero'           => 'required|in:M,F,Otro',
            'tipo_sangre'      => 'nullable|string|max:5',
            'alergias'         => 'nullable|string',
        ]);

        try {
            $paciente->update($data);
            return response()->json(['success' => true, 'message' => 'Paciente actualizado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina un paciente y todas sus consultas asociadas.
     *
     * IMPORTANTE: Primero elimina las consultas del paciente para respetar
     * la integridad referencial, luego elimina el paciente.
     * Esta acción es irreversible.
     *
     * @param Paciente $paciente Paciente a eliminar (Route Model Binding)
     * @return JsonResponse { success, message }
     */
    public function destroy(Paciente $paciente)
    {
        try {
            // Eliminar primero las consultas asociadas (integridad referencial)
            $paciente->consultas()->delete();
            // Luego eliminar el paciente
            $paciente->delete();
            return response()->json(['success' => true, 'message' => 'Paciente eliminado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Exporta todos los pacientes en formato CSV.
     *
     * Usa Response::stream() para enviar el archivo directamente al navegador
     * sin guardarlo en disco. Incluye BOM UTF-8 para compatibilidad con Excel.
     *
     * @return StreamedResponse Archivo CSV para descarga
     */
    public function exportar(Request $request)
    {
        // Obtener todos los pacientes para exportar
        $pacientes = Paciente::all();
        $filename  = 'pacientes_' . now()->format('Y-m-d') . '.csv';

        // Headers HTTP para forzar la descarga del archivo
        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        // Callback que escribe el CSV directamente al output stream
        $callback = function () use ($pacientes) {
            $output = fopen('php://output', 'w');

            // BOM UTF-8: necesario para que Excel abra el CSV con tildes correctamente
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Escribir la fila de encabezados
            fputcsv($output, ['ID', 'Nombre', 'Apellido', 'Cédula', 'Teléfono', 'Email', 'Fecha Nacimiento', 'Género', 'Tipo Sangre', 'Dirección', 'Alergias', 'Fecha Registro']);

            // Escribir una fila por cada paciente
            foreach ($pacientes as $p) {
                fputcsv($output, [
                    $p->id, $p->nombre, $p->apellido, $p->cedula,
                    $p->telefono ?? '',
                    $p->email ?? '',
                    $p->fecha_nacimiento?->format('d/m/Y') ?? '',
                    $p->genero_texto,       // accessor del modelo: M→Masculino, F→Femenino
                    $p->tipo_sangre ?? '',
                    $p->direccion ?? '',
                    $p->alergias ?? '',
                    $p->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($output);
        };

        return Response::stream($callback, 200, $headers);
    }
}
