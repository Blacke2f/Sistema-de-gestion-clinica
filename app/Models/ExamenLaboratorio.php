<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo de Solicitud de Exámenes de Laboratorio
 * Formato Maxical con checkboxes estándar + campo otros.
 * @version 1.0
 */
class ExamenLaboratorio extends Model
{
    use SoftDeletes;

    protected $table = 'examenes_laboratorio';

    protected $fillable = [
        'numero_solicitud',
        'paciente_id',
        'consulta_id',
        'medico_id',
        'empleado_id',
        'hematologia_completa',
        'glicemia_ayunas',
        'urea',
        'creatinina',
        'colesterol_total',
        'trigliceridos',
        'examen_orina',
        'examen_heces',
        'otros',
        'fecha',
        'tipo_documento',
        'imagen_escaneo',
    ];

    protected $casts = [
        'fecha'               => 'date',
        'hematologia_completa'=> 'boolean',
        'glicemia_ayunas'     => 'boolean',
        'urea'                => 'boolean',
        'creatinina'          => 'boolean',
        'colesterol_total'    => 'boolean',
        'trigliceridos'       => 'boolean',
        'examen_orina'        => 'boolean',
        'examen_heces'        => 'boolean',
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

    /**
     * Devuelve array con los nombres de los exámenes solicitados.
     */
    public function getExamenesSolicitadosAttribute(): array
    {
        $map = [
            'hematologia_completa' => 'Hematología Completa',
            'glicemia_ayunas'      => 'Glicemia en Ayunas',
            'urea'                 => 'Urea',
            'creatinina'           => 'Creatinina',
            'colesterol_total'     => 'Colesterol Total',
            'trigliceridos'        => 'Triglicéridos',
            'examen_orina'         => 'Examen de Orina',
            'examen_heces'         => 'Examen de Heces',
        ];

        $seleccionados = [];
        foreach ($map as $campo => $etiqueta) {
            if ($this->$campo) {
                $seleccionados[] = $etiqueta;
            }
        }
        return $seleccionados;
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public static function generarNumero(): string
    {
        $year = now()->year;
        $last = static::whereYear('created_at', $year)->max('numero_solicitud');
        $seq  = $last ? ((int) substr($last, -4)) + 1 : 1;
        return 'EXA-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
