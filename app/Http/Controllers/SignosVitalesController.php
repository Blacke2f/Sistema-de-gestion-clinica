<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Paciente;
use App\Models\SignoVital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador de Signos Vitales
 * Registra y consulta signos vitales por consulta/paciente.
 * @version 1.0
 */
class SignosVitalesController extends Controller
{
    /**
     * Registra signos vitales para una consulta.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'consulta_id'             => 'required|exists:consultas,id',
            'paciente_id'             => 'required|exists:pacientes,id',
            'presion_sistolica'       => 'nullable|numeric|min:50|max:300',
            'presion_diastolica'      => 'nullable|numeric|min:30|max:200',
            'frecuencia_cardiaca'     => 'nullable|numeric|min:20|max:300',
            'frecuencia_respiratoria' => 'nullable|numeric|min:5|max:60',
            'temperatura'             => 'nullable|numeric|min:30|max:45',
            'saturacion_oxigeno'      => 'nullable|numeric|min:50|max:100',
            'peso'                    => 'nullable|numeric|min:1|max:500',
            'talla'                   => 'nullable|numeric|min:30|max:250',
            'glucosa'                 => 'nullable|numeric|min:20|max:800',
            'observaciones'           => 'nullable|string',
        ]);

        try {
            // Calcular IMC automáticamente si hay peso y talla
            if (!empty($data['peso']) && !empty($data['talla'])) {
                $data['imc'] = SignoVital::calcularImc($data['peso'], $data['talla']);
            }

            $data['registrado_por'] = Auth::id();

            // Upsert: si ya existe para esta consulta, actualizar
            $signo = SignoVital::updateOrCreate(
                ['consulta_id' => $data['consulta_id']],
                $data
            );

            return response()->json([
                'success' => true,
                'message' => 'Signos vitales registrados correctamente.',
                'data'    => $signo,
                'imc_clasificacion' => $signo->clasificacion_imc,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna los signos vitales de una consulta.
     */
    public function porConsulta(Consulta $consulta)
    {
        $signo = SignoVital::where('consulta_id', $consulta->id)->first();
        return response()->json(['success' => true, 'data' => $signo]);
    }

    /**
     * Retorna el historial de signos vitales de un paciente (últimas 10 consultas).
     * Útil para mostrar tendencias en el expediente clínico.
     */
    public function historialPaciente(Paciente $paciente)
    {
        $historial = SignoVital::with('consulta')
            ->where('paciente_id', $paciente->id)
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn ($s) => [
                'fecha'                  => $s->created_at->format('d/m/Y'),
                'presion'                => $s->presion_arterial,
                'frecuencia_cardiaca'    => $s->frecuencia_cardiaca,
                'temperatura'            => $s->temperatura,
                'saturacion_oxigeno'     => $s->saturacion_oxigeno,
                'peso'                   => $s->peso,
                'imc'                    => $s->imc,
                'clasificacion_imc'      => $s->clasificacion_imc,
                'glucosa'                => $s->glucosa,
            ]);

        return response()->json(['success' => true, 'data' => $historial]);
    }
}
