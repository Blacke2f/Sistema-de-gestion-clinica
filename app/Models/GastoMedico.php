<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo de Gasto Médico
 * Registra los gastos de atención médica de un paciente.
 * @version 1.0
 */
class GastoMedico extends Model
{
    use SoftDeletes;

    protected $table = 'gastos_medicos';

    protected $fillable = [
        'numero_gasto',
        'paciente_id',
        'consulta_id',
        'medico_id',
        'empleado_id',
        'nombre_empresa',
        'fecha',
        'descripcion',
        'tipo_documento',
        'imagen_escaneo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    // ── Relaciones ─────────────────────────────────────────────────────────────

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id')->withTrashed();
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id')->withTrashed();
    }

    public function medico()
    {
        return $this->belongsTo(Usuario::class, 'medico_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id')->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(GastoMedicoItem::class, 'gasto_medico_id');
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getMedicoNombreAttribute(): string
    {
        if ($this->empleado) return $this->empleado->nombre_completo;
        if ($this->medico)   return $this->medico->nombre_completo;
        return 'Sin asignar';
    }

    public function getTotalAttribute(): float
    {
        return $this->items->sum('monto');
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public static function generarNumero(): string
    {
        $year = now()->year;
        $last = static::withTrashed()->whereYear('created_at', $year)->max('numero_gasto');
        $seq  = $last ? ((int) substr($last, -4)) + 1 : 1;
        return 'GAS-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
