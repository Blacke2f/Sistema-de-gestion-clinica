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

class ExpedienteController extends Controller
{
    public function show(Paciente $paciente)
    {
        if (!$paciente->exists) {
            abort(404);
        }

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

        $ultimaConsulta = $consultas->first();

        $signosVitales = SignoVital::where('paciente_id', $paciente->id)
            ->latest()
            ->limit(10)
            ->get();

        $recetas = Receta::with(['items.medicamento', 'empleado', 'medico'])
            ->where('paciente_id', $paciente->id)
            ->latest()
            ->get();

        $informesMedicos = InformeMedico::with(['empleado', 'medico'])
            ->where('paciente_id', $paciente->id)
            ->latest('fecha')
            ->get();

        $gastosMedicos = GastoMedico::with(['empleado', 'medico', 'items'])
            ->where('paciente_id', $paciente->id)
            ->latest('fecha')
            ->get();

        $examenesLaboratorio = ExamenLaboratorio::with(['empleado', 'medico'])
            ->where('paciente_id', $paciente->id)
            ->latest('fecha')
            ->get();

        $stats = [
            'total_consultas'    => $consultas->count(),
            'consultas_año'      => $consultas->filter(fn($c) => $c->fecha_hora >= now()->startOfYear())->count(),
            'total_recetas'      => $recetas->count(),
            'ultima_visita'      => $ultimaConsulta?->fecha_hora,
            'total_informes'     => $informesMedicos->count(),
            'total_gastos'       => $gastosMedicos->count(),
            'total_examenes'     => $examenesLaboratorio->count(),
        ];

        $medicamentosFrec = \App\Models\RecetaItem::whereHas('receta', fn ($q) => $q->where('paciente_id', $paciente->id))
            ->select('nombre_medicamento', \DB::raw('SUM(cantidad) as total_cantidad'), \DB::raw('COUNT(*) as veces'))
            ->groupBy('nombre_medicamento')
            ->orderByDesc('veces')
            ->limit(5)
            ->get();

        $medicos = \App\Models\Empleado::medicos()->orderBy('nombre')->get();
        
        $archivosNube = [];

        // Construir Línea de Tiempo Unificada
        $historial = collect();

        foreach ($consultas as $c) {
            $historial->push([
                'tipo' => 'consulta',
                'fecha' => \Carbon\Carbon::parse($c->fecha_hora),
                'modelo' => $c
            ]);
        }
        foreach ($informesMedicos as $i) {
            $historial->push([
                'tipo' => 'informe',
                'fecha' => \Carbon\Carbon::parse($i->fecha),
                'modelo' => $i
            ]);
        }
        foreach ($gastosMedicos as $g) {
            $historial->push([
                'tipo' => 'gasto',
                'fecha' => \Carbon\Carbon::parse($g->fecha),
                'modelo' => $g
            ]);
        }
        foreach ($examenesLaboratorio as $e) {
            $historial->push([
                'tipo' => 'examen',
                'fecha' => \Carbon\Carbon::parse($e->fecha),
                'modelo' => $e
            ]);
        }
        foreach ($recetas as $r) {
            $historial->push([
                'tipo' => 'receta',
                'fecha' => \Carbon\Carbon::parse($r->fecha_emision),
                'modelo' => $r
            ]);
        }

        // Ordenar por fecha descendente
        $historial = $historial->sortByDesc('fecha')->values();

        return view('expediente.show', compact('archivosNube', 
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
            'medicos',
            'historial'
        ));
    }

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