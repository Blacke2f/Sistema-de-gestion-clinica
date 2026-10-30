<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo de Receta Médica
 * @version 1.0
 */
class Receta extends Model
{
    use SoftDeletes;

    protected $table = 'recetas';

    protected $fillable = [
        'numero_receta',
        'consulta_id',
        'paciente_id',
        'medico_id',
        'empleado_id',
        'diagnostico',
        'indicaciones_generales',
        'imagen_receta',      // ruta de imagen escaneada (receta manuscrita)
        'tipo_receta',        // 'digital' | 'manuscrita'
        'fecha_emision',
        'fecha_vencimiento',
        'estado',
    ];

    protected $casts = [
        'fecha_emision'    => 'date',
        'fecha_vencimiento'=> 'date',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id')->withTrashed();
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id')->withTrashed();
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
        return $this->hasMany(RecetaItem::class, 'receta_id');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getMedicoNombreAttribute(): string
    {
        if ($this->empleado) return $this->empleado->nombre_completo;
        if ($this->medico)   return $this->medico->nombre_completo;
        return 'Sin asignar';
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Genera el siguiente número de receta correlativo.
     */
    public static function generarNumero(): string
    {
        $year  = now()->year;
        $last  = static::withTrashed()->whereYear('created_at', $year)->max('numero_receta');
        $seq   = $last ? ((int) substr($last, -4)) + 1 : 1;
        return 'REC-' . $year . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
