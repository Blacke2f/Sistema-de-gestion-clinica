<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo de Signos Vitales
 * Registra los signos vitales por consulta.
 * @version 1.0
 */
class SignoVital extends Model
{
    protected $table = 'signos_vitales';

    protected $fillable = [
        'consulta_id',
        'paciente_id',
        'registrado_por',
        'presion_sistolica',
        'presion_diastolica',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'temperatura',
        'saturacion_oxigeno',
        'peso',
        'talla',
        'imc',
        'glucosa',
        'observaciones',
    ];

    protected $casts = [
        'presion_sistolica'       => 'decimal:1',
        'presion_diastolica'      => 'decimal:1',
        'frecuencia_cardiaca'     => 'decimal:1',
        'frecuencia_respiratoria' => 'decimal:1',
        'temperatura'             => 'decimal:1',
        'saturacion_oxigeno'      => 'decimal:1',
        'peso'                    => 'decimal:2',
        'talla'                   => 'decimal:2',
        'imc'                     => 'decimal:2',
        'glucosa'                 => 'decimal:1',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(Usuario::class, 'registrado_por');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    /**
     * Presión arterial formateada: "120/80 mmHg"
     */
    public function getPresionArterialAttribute(): string
    {
        if ($this->presion_sistolica && $this->presion_diastolica) {
            return "{$this->presion_sistolica}/{$this->presion_diastolica} mmHg";
        }
        return '-';
    }

    /**
     * Calcula el IMC si hay peso y talla.
     */
    public static function calcularImc(float $peso, float $talla): float
    {
        if ($talla <= 0) return 0;
        $tallaM = $talla / 100; // cm a metros
        return round($peso / ($tallaM * $tallaM), 2);
    }

    /**
     * Clasificación del IMC según OMS.
     */
    public function getClasificacionImcAttribute(): string
    {
        if (!$this->imc) return '-';
        return match (true) {
            $this->imc < 18.5 => 'Bajo peso',
            $this->imc < 25.0 => 'Normal',
            $this->imc < 30.0 => 'Sobrepeso',
            $this->imc < 35.0 => 'Obesidad I',
            $this->imc < 40.0 => 'Obesidad II',
            default           => 'Obesidad III',
        };
    }
}
