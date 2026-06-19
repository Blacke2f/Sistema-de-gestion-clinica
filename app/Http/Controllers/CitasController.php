<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Empleado;
use App\Models\Notificacion;
use App\Models\Paciente;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de Citas Médicas (Agenda)
 *
 * Gestiona la agenda de citas: programar, confirmar, cancelar y
 * convertir citas en consultas. Incluye vista de calendario y lista.
 *
 * @version 1.0
 */
class CitasController extends Controller
{
    /**
     * Vista principal con lista de citas y calendario.
     */
    public function index(Request $request)
    {
        $search  = $request->get('search', '');
        $estado  = $request->get('estado', '');
        $fecha   = $request->get('fecha', '');
        $perPage = in_array($request->get('per_page', 15), [10, 15, 20, 50])
            ? (int) $request->get('per_page', 15)
            : 15;

        $citas = Cita::with(['paciente', 'empleado', 'medico'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('paciente', function ($p) use ($search) {
                    $p->where('nombre',   'like', "%{$search}%")
                      ->orWhere('apellido', 'like', "%{$search}%")
                      ->orWhere('cedula',   'like', "%{$search}%");
                })->orWhere('motivo', 'like', "%{$search}%");
            })
            ->when($estado, fn ($q) => $q->where('estado', $estado))
            ->when($fecha,  fn ($q) => $q->whereDate('fecha_hora', $fecha))
            ->orderBy('fecha_hora', 'asc')
            ->paginate($perPage)
            ->withQueryString();

        // Datos para el calendario (próximos 30 días)
        $citasCalendario = Cita::with(['paciente', 'empleado', 'medico'])
            ->byDateRange(now()->format('Y-m-d'), now()->addDays(30)->format('Y-m-d'))
            ->whereNotIn('estado', ['cancelada'])
            ->get();

        $pacientes = Paciente::orderBy('nombre')->get();
        $medicos   = Empleado::medicos()->orderBy('nombre')->get();
        $usuarios  = Usuario::where('rol', 'medico')->where('activo', true)->orderBy('nombre_completo')->get();

        // Estadísticas rápidas
        $stats = [
            'hoy'       => Cita::hoy()->whereNotIn('estado', ['cancelada'])->count(),
            'semana'    => Cita::proximas(7)->count(),
            'pendientes'=> Cita::pendientes()->count(),
            'hoy_total' => Cita::hoy()->count(),
        ];

        $servicios = \App\Models\ServicioMedico::where('activo', true)->get();

        return view('citas.index', compact(
            'citas', 'citasCalendario', 'pacientes', 'medicos', 'usuarios', 'servicios',
            'stats', 'search', 'estado', 'fecha', 'perPage'
        ));
    }

    /**
     * Crea una nueva cita.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'paciente_id'      => 'required|exists:pacientes,id',
            'empleado_id'      => 'nullable|exists:nomina,id',
            'medico_id'        => 'nullable|exists:usuarios,id',
            'servicio_id'      => 'nullable|exists:servicio_medicos,id',
            'fecha_hora'       => 'required|date|after:now',
            'duracion_minutos' => 'nullable|integer|min:10|max:240',
            'motivo'           => 'nullable|string|max:255',
            'notas'            => 'nullable|string',
            'color'            => 'nullable|string|max:20',
        ], [
            'paciente_id.required' => 'Seleccione un paciente.',
            'fecha_hora.required'  => 'La fecha y hora son requeridas.',
            'fecha_hora.after'     => 'La cita debe ser en el futuro.',
        ]);

        if (empty($data['empleado_id']) && empty($data['medico_id'])) {
            return response()->json(['success' => false, 'message' => 'Seleccione un médico.'], 422);
        }

        // Lógica de Servicio Médico
        if (!empty($data['servicio_id'])) {
            $servicio = \App\Models\ServicioMedico::find($data['servicio_id']);
            if ($servicio) {
                $data['duracion_minutos'] = $data['duracion_minutos'] ?: $servicio->duracion_minutos;
                $data['motivo'] = $data['motivo'] ?: $servicio->nombre;
            }
        }
        
        $data['duracion_minutos'] = $data['duracion_minutos'] ?? 30;
        $data['motivo'] = $data['motivo'] ?? 'Cita médica';

        // Verificación de Conflictos de Horario
        $medicoId = $data['medico_id'] ?? null;
        $empleadoId = $data['empleado_id'] ?? null;

        $inicio = \Carbon\Carbon::parse($data['fecha_hora']);
        $fin = $inicio->copy()->addMinutes((int)$data['duracion_minutos']);

        $conflicto = \App\Models\Cita::where(function ($query) use ($medicoId, $empleadoId) {
                if ($medicoId) {
                    $query->where('medico_id', $medicoId);
                } elseif ($empleadoId) {
                    $query->where('empleado_id', $empleadoId);
                }
            })
            ->whereIn('estado', ['programada', 'confirmada', 'en_curso'])
            ->whereDate('fecha_hora', $inicio->toDateString())
            ->get();

        foreach ($conflicto as $c) {
            $c_inicio = \Carbon\Carbon::parse($c->fecha_hora);
            $c_fin = $c_inicio->copy()->addMinutes((int)$c->duracion_minutos);
            
            // Solapamiento: A.inicio < B.fin AND A.fin > B.inicio
            if ($inicio < $c_fin && $fin > $c_inicio) {
                return response()->json(['success' => false, 'message' => 'Conflicto de horario: El médico ya tiene una cita de ' . $c_inicio->format('H:i') . ' a ' . $c_fin->format('H:i')], 422);
            }
        }

        try {
            $data['creado_por'] = Auth::id();
            $data['estado']     = 'programada';
            $data['color']      = $data['color'] ?? '#0d6efd';

            $cita = Cita::create($data);
            $cita->load('paciente');

            // Notificar al médico asignado
            try {
                if ($medicoId) {
                    Notificacion::notificarUsuario(
                        usuarioId: $medicoId,
                        tipo:      'cita_nueva',
                        titulo:    'Nueva Cita Programada',
                        mensaje:   "Cita con {$cita->paciente->nombre_completo} el {$cita->fecha_hora->format('d/m/Y H:i')}",
                        icono:     'bx-calendar-plus',
                        color:     'primary',
                        url:       '/citas'
                    );
                }
            } catch (\Throwable $e) { /* no crítico */ }

            return response()->json([
                'success' => true,
                'message' => 'Cita programada correctamente.',
                'id'      => $cita->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al programar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna los datos de una cita en JSON.
     */
    public function show(Cita $cita)
    {
        $cita->load(['paciente', 'empleado', 'medico']);
        return response()->json(['success' => true, 'data' => $cita]);
    }

    /**
     * Actualiza una cita existente.
     */
    public function update(Request $request, Cita $cita)
    {
        $data = $request->validate([
            'paciente_id'      => 'required|exists:pacientes,id',
            'empleado_id'      => 'nullable|exists:nomina,id',
            'medico_id'        => 'nullable|exists:usuarios,id',
            'servicio_id'      => 'nullable|exists:servicio_medicos,id',
            'fecha_hora'       => 'required|date',
            'duracion_minutos' => 'nullable|integer|min:10|max:240',
            'motivo'           => 'nullable|string|max:255',
            'notas'            => 'nullable|string',
            'estado'           => 'required|in:programada,confirmada,en_curso,completada,cancelada,no_asistio',
            'color'            => 'nullable|string|max:20',
        ]);

        // Lógica de Servicio Médico
        if (!empty($data['servicio_id'])) {
            $servicio = \App\Models\ServicioMedico::find($data['servicio_id']);
            if ($servicio) {
                $data['duracion_minutos'] = $data['duracion_minutos'] ?: $servicio->duracion_minutos;
                $data['motivo'] = $data['motivo'] ?: $servicio->nombre;
            }
        }
        
        $data['duracion_minutos'] = $data['duracion_minutos'] ?? $cita->duracion_minutos;
        $data['motivo'] = $data['motivo'] ?? $cita->motivo;

        // Verificación de Conflictos de Horario
        if (in_array($data['estado'], ['programada', 'confirmada', 'en_curso'])) {
            $medicoId = $data['medico_id'] ?? null;
            $empleadoId = $data['empleado_id'] ?? null;

            $inicio = \Carbon\Carbon::parse($data['fecha_hora']);
            $fin = $inicio->copy()->addMinutes((int)$data['duracion_minutos']);

            $conflicto = \App\Models\Cita::where('id', '!=', $cita->id)
                ->where(function ($query) use ($medicoId, $empleadoId) {
                    if ($medicoId) {
                        $query->where('medico_id', $medicoId);
                    } elseif ($empleadoId) {
                        $query->where('empleado_id', $empleadoId);
                    }
                })
                ->whereIn('estado', ['programada', 'confirmada', 'en_curso'])
                ->whereDate('fecha_hora', $inicio->toDateString())
                ->get();

            foreach ($conflicto as $c) {
                $c_inicio = \Carbon\Carbon::parse($c->fecha_hora);
                $c_fin = $c_inicio->copy()->addMinutes((int)$c->duracion_minutos);
                
                if ($inicio < $c_fin && $fin > $c_inicio) {
                    return response()->json(['success' => false, 'message' => 'Conflicto de horario: El médico ya tiene una cita de ' . $c_inicio->format('H:i') . ' a ' . $c_fin->format('H:i')], 422);
                }
            }
        }

        try {
            $cita->update($data);
            return response()->json(['success' => true, 'message' => 'Cita actualizada correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cambia el estado de una cita rápidamente.
     */
    public function cambiarEstado(Request $request, Cita $cita)
    {
        $request->validate([
            'estado' => 'required|in:programada,confirmada,en_curso,completada,cancelada,no_asistio',
        ]);

        try {
            $cita->update(['estado' => $request->estado]);
            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado a: ' . $cita->fresh()->estado_texto,
                'estado'  => $cita->fresh()->estado,
                'color'   => $cita->fresh()->estado_color,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Convierte una cita en consulta médica.
     * Crea la consulta y vincula la cita a ella.
     */
    public function convertirAConsulta(Cita $cita)
    {
        if ($cita->consulta_id) {
            return response()->json(['success' => false, 'message' => 'Esta cita ya fue convertida en consulta.'], 422);
        }

        try {
            $consulta = Consulta::create([
                'paciente_id' => $cita->paciente_id,
                'medico_id'   => $cita->medico_id,
                'empleado_id' => $cita->empleado_id,
                'fecha_hora'  => $cita->fecha_hora,
                'motivo'      => $cita->motivo,
                'estado'      => 'en_proceso',
            ]);

            $cita->update([
                'consulta_id' => $consulta->id,
                'estado'      => 'en_curso',
            ]);

            return response()->json([
                'success'     => true,
                'message'     => 'Cita convertida en consulta correctamente.',
                'consulta_id' => $consulta->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Elimina una cita (soft delete).
     */
    public function destroy(Cita $cita)
    {
        try {
            $cita->delete();
            return response()->json(['success' => true, 'message' => 'Cita eliminada correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna citas en formato JSON para el calendario FullCalendar.
     */
    public function calendario(Request $request)
    {
        $inicio = $request->get('start', now()->startOfMonth()->format('Y-m-d'));
        $fin    = $request->get('end',   now()->endOfMonth()->format('Y-m-d'));

        $citas = Cita::with(['paciente', 'empleado', 'medico'])
            ->byDateRange($inicio, $fin)
            ->whereNotIn('estado', ['cancelada'])
            ->get();

        $eventos = $citas->map(fn ($c) => [
            'id'              => $c->id,
            'title'           => $c->paciente->nombre_completo,
            'start'           => $c->fecha_hora->toIso8601String(),
            'end'             => $c->fecha_hora_fin->toIso8601String(),
            'backgroundColor' => $c->color,
            'borderColor'     => $c->color,
            'extendedProps'   => [
                'paciente' => $c->paciente->nombre_completo,
                'paciente_id' => $c->paciente_id,
                'empleado_id' => $c->empleado_id,
                'medico_id'   => $c->medico_id,
                'servicio_id' => $c->servicio_id,
                'medico'   => $c->medico_nombre,
                'motivo'   => $c->motivo,
                'estado'   => $c->estado,
                'estadoTexto' => $c->estado_texto,
            ],
        ]);

        return response()->json($eventos);
    }
}
