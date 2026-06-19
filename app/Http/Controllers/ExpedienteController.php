<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\ExamenLaboratorio;
use App\Models\GastoMedico;
use App\Models\InformeMedico;
use App\Models\Paciente;
use App\Models\Receta;
use App\Models\SignoVital;
use Illuminate\Http\Request;

/**
 * Controlador de Expediente Clínico
 * Vista unificada del historial médico completo de un paciente:
 * consultas, signos vitales, recetas y medicamentos frecuentes.
 * @version 1.1
 */
class ExpedienteController extends Controller
{
    /**
     * Muestra el expediente clínico completo de un paciente.
     */
    public function show(Paciente $paciente)
    {
        // Asegurar que el paciente existe aunque tenga soft delete
        // (por si se accede desde un enlace guardado)
        if (!$paciente->exists) {
            abort(404);
        }

        // Historial de consultas con todas sus relaciones
        $consultas = Consulta::with([
                'empleado',
                'medico',
                'medicamentos',
                'signosVitales',
                'recetas.items',
            ])
            ->where('paciente_id', $paciente->id)
            ->latest('fecha_hora')
            ->get();

        // Última consulta para mostrar en el resumen
        $ultimaConsulta = $consultas->first();

        // Últimos signos vitales para tendencias
        $signosVitales = SignoVital::where('paciente_id', $paciente->id)
            ->latest()
            ->limit(10)
            ->get();

        // Todas las recetas del paciente
        $recetas = Receta::with(['items.medicamento', 'empleado', 'medico'])
            ->where('paciente_id', $paciente->id)
            ->latest()
            ->get();

        // Estadísticas del paciente
        $stats = [
            'total_consultas'    => $consultas->count(),
            'consultas_año'      => $consultas->filter(fn($c) => $c->fecha_hora >= now()->startOfYear())->count(),
            'total_recetas'      => $recetas->count(),
            'ultima_visita'      => $ultimaConsulta?->fecha_hora,
            'total_informes'     => InformeMedico::where('paciente_id', $paciente->id)->count(),
            'total_gastos'       => GastoMedico::where('paciente_id', $paciente->id)->count(),
            'total_examenes'     => ExamenLaboratorio::where('paciente_id', $paciente->id)->count(),
        ];

        // Medicamentos más recetados al paciente
        $medicamentosFrec = \App\Models\RecetaItem::whereHas('receta', fn ($q) => $q->where('paciente_id', $paciente->id))
            ->select('nombre_medicamento', \DB::raw('SUM(cantidad) as total_cantidad'), \DB::raw('COUNT(*) as veces'))
            ->groupBy('nombre_medicamento')
            ->orderByDesc('veces')
            ->limit(5)
            ->get();

        // Informes médicos del paciente
        $informesMedicos = InformeMedico::with(['empleado', 'medico'])
            ->where('paciente_id', $paciente->id)
            ->latest('fecha')
            ->get();

        // Gastos médicos del paciente
        $gastosMedicos = GastoMedico::with(['empleado', 'medico', 'items'])
            ->where('paciente_id', $paciente->id)
            ->latest('fecha')
            ->get();

        // Solicitudes de exámenes de laboratorio
        $examenesLaboratorio = ExamenLaboratorio::with(['empleado', 'medico'])
            ->where('paciente_id', $paciente->id)
            ->latest('fecha')
            ->get();

        // Médicos disponibles para los formularios
        $medicos = \App\Models\Empleado::medicos()->orderBy('nombre')->get();

        return view('expediente.show', compact(
            'paciente',
            'consultas',
            'ultimaConsulta',
            'signosVitales',
            'recetas',
            'stats',
            'medicamentosFrec',
            'informesMedicos',
            'gastosMedicos',
            'examenesLaboratorio',
            'medicos'
        ));
    }

    /**
     * Retorna el resumen del expediente en JSON (para modal rápido).
     */
    public function resumen(Paciente $paciente)
    {
        $ultimaConsulta = Consulta::with(['empleado', 'medico'])
            ->where('paciente_id', $paciente->id)
            ->latest('fecha_hora')
            ->first();

        $ultimosSignos = SignoVital::where('paciente_id', $paciente->id)
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'paciente'        => $paciente,
                'ultima_consulta' => $ultimaConsulta,
                'ultimos_signos'  => $ultimosSignos,
                'total_consultas' => Consulta::where('paciente_id', $paciente->id)->count(),
                'total_recetas'   => Receta::where('paciente_id', $paciente->id)->count(),
            ],
        ]);
    }
}
