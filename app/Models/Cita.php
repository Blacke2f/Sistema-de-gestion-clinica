<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo de Cita Médica
 * Representa una cita programada antes de convertirse en consulta.
 * @version 1.0
 */
class Cita extends Model
{
    use SoftDeletes;

    protected $table = 'citas';

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'empleado_id',
        'consulta_id',
        'creado_por',
        'fecha_hora',
        'duracion_minutos',
        'motivo',
        'notas',
        'estado',
        'color',
        'recordatorio_enviado',
    ];

    protected $casts = [
        'fecha_hora'            => 'datetime',
        'recordatorio_enviado'  => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────────

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function medico()
    {
        return $this->belongsTo(Usuario::class, 'medico_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getMedicoNombreAttribute(): string
    {
        if ($this->empleado) return $this->empleado->nombre_completo;
        if ($this->medico)   return $this->medico->nombre_completo;
        return 'Sin asignar';
    }

    public function getEstadoTextoAttribute(): string
    {
        return match ($this->estado) {
            'programada'  => 'Programada',
            'confirmada'  => 'Confirmada',
            'en_curso'    => 'En Curso',
            'completada'  => 'Completada',
            'cancelada'   => 'Cancelada',
            'no_asistio'  => 'No Asistió',
            default       => ucfirst($this->estado),
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            'programada'  => 'primary',
            'confirmada'  => 'info',
            'en_curso'    => 'warning',
            'completada'  => 'success',
            'cancelada'   => 'danger',
            'no_asistio'  => 'secondary',
            default       => 'secondary',
        };
    }

    public function getFechaHoraFinAttribute(): \Carbon\Carbon
    {
        return $this->fecha_hora->addMinutes($this->duracion_minutos);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_hora', today());
    }

    public function scopeProximas($query, int $dias = 7)
    {
        return $query->whereBetween('fecha_hora', [now(), now()->addDays($dias)])
                     ->whereIn('estado', ['programada', 'confirmada']);
    }

    public function scopeByDateRange($query, string $inicio, string $fin)
    {
        return $query->whereBetween(\DB::raw('DATE(fecha_hora)'), [$inicio, $fin]);
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['programada', 'confirmada']);
    }
}
