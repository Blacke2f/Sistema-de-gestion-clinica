<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo de Informe Médico Ocupacional
 * Formato Maxical: evaluación por tipo de consulta con resultado de aptitud.
 * @version 1.0
 */
class InformeMedico extends Model
{
    use SoftDeletes;

    protected $table = 'informes_medicos';

    protected $fillable = [
        'numero_informe',
        'paciente_id',
        'consulta_id',
        'medico_id',
        'empleado_id',
        'nombre_empresa',
        'edad',
        'cargo',
        'antiguedad_cargo',
        'tipo_consulta',
        'tipo_consulta_otros',
        'aptitud',
        'diagnostico',
        'estudios_paraclinicos',
        'referencia',
        'recomendaciones',
        'fecha',
        'tipo_documento',
        'imagen_escaneo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    // ── Relaciones ─────────────────────────────────────────────────────────────

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id');
    }

    public function medico()
    {
        return $this->belongsTo(Usuario::class, 'medico_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getMedicoNombreAttribute(): string
    {
        if ($this->empleado) return $this->empleado->nombre_completo;
        if ($this->medico)   return $this->medico->nombre_completo;
        return 'Sin asignar';
    }

    public function getTipoConsultaTextoAttribute(): string
    {
        return match ($this->tipo_consulta) {
            'preempleo'      => 'Pre-empleo',
            'prevacacional'  => 'Pre-vacacional',
            'postvacacional' => 'Post-vacacional',
            'egreso'         => 'Egreso',
            'reubicacion'    => 'Reubicación',
            'otros'          => $this->tipo_consulta_otros ?? 'Otros',
            default          => '—',
        };
    }

    public function getAptitudTextoAttribute(): string
    {
        return match ($this->aptitud) {
            'apto'               => 'Apto',
            'no_apto'            => 'No Apto',
            'apto_condicionado'  => 'Apto Condicionado',
            default              => '—',
        };
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public static function generarNumero(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->max('numero_informe');
        $seq  = $last ? ((int) substr($last, -4)) + 1 : 1;
        return 'INF-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
