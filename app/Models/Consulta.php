<?php

namespace App\Models;

/**
 * Modelo de Consulta Médica
 *
 * Representa una consulta médica registrada en el sistema.
 * Cada consulta pertenece a un paciente y puede ser atendida por un médico
 * de la nómina (empleado) o por un usuario con rol médico.
 *
 * Tabla en BD: consultas
 * @version 2.5
 * @package App\Models
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consulta extends Model
{
    use SoftDeletes;
    // Nombre de la tabla en la base de datos
    protected $table = 'consultas';

    // Campos que se pueden asignar masivamente (mass assignment)
    protected $fillable = [
        'paciente_id',    // ID del paciente que recibe la consulta
        'medico_id',      // ID del usuario con rol médico (opcional)
        'empleado_id',    // ID del empleado de nómina que atiende (opcional)
        'fecha_hora',     // Fecha y hora programada de la consulta
        'motivo',         // Motivo o razón de la consulta
        'diagnostico',    // Diagnóstico médico (se llena después)
        'tratamiento',    // Tratamiento indicado (se llena después)
        'estado',         // Estado: pendiente, en_proceso, completada, cancelada
        'tipo_consulta',  // Tipo: preempleo, prevacacional, postvacacional, egreso, reubicacion, otros
    ];

    // Conversión automática de tipos al leer de la BD
    protected $casts = [
        'fecha_hora' => 'datetime', // Se convierte a objeto Carbon automáticamente
    ];

    // ══════════════════════════════════════════════════════════════════════════
    // RELACIONES CON OTROS MODELOS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Paciente que recibe la consulta.
     * Una consulta pertenece a un solo paciente.
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    /**
     * Usuario médico que atiende la consulta (si es usuario del sistema).
     * Puede ser null si la consulta la atiende un empleado de nómina.
     */
    public function medico()
    {
        return $this->belongsTo(Usuario::class, 'medico_id');
    }

    /**
     * Empleado de nómina que atiende la consulta.
     * Puede ser null si la atiende un usuario del sistema.
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    /**
     * Medicamentos recetados en esta consulta.
     * Relación muchos a muchos con la tabla pivote consultas_medicamentos.
     * Incluye la cantidad y las indicaciones de cada medicamento.
     */
    public function medicamentos()
    {
        return $this->belongsToMany(
            Medicamento::class,
            'consultas_medicamentos',
            'consulta_id',
            'medicamento_id'
        )
        ->withPivot('cantidad', 'indicaciones');
        // Sin withTimestamps() — la tabla solo tiene created_at, no updated_at
    }

    /**
     * Movimientos de inventario generados por esta consulta.
     * Cuando se dispensa un medicamento en consulta, se registra una salida.
     */
    public function movimientos()
    {
        return $this->hasMany(InventarioMovimiento::class, 'consulta_id');
    }

    /**
     * Signos vitales registrados en esta consulta.
     */
    public function signosVitales()
    {
        return $this->hasMany(SignoVital::class, 'consulta_id');
    }

    /**
     * Recetas emitidas en esta consulta.
     */
    public function recetas()
    {
        return $this->hasMany(Receta::class, 'consulta_id');
    }

    /**
     * Informes médicos de esta consulta.
     */
    public function informesMedicos()
    {
        return $this->hasMany(InformeMedico::class, 'consulta_id');
    }

    /**
     * Gastos médicos de esta consulta.
     */
    public function gastosMedicos()
    {
        return $this->hasMany(GastoMedico::class, 'consulta_id');
    }

    /**
     * Solicitudes de exámenes de laboratorio de esta consulta.
     */
    public function examenesLaboratorio()
    {
        return $this->hasMany(ExamenLaboratorio::class, 'consulta_id');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ACCESSORS (atributos calculados)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Obtiene el nombre del médico que atiende la consulta.
     * Primero busca en empleados de nómina, luego en usuarios del sistema.
     * Si no hay ninguno asignado, retorna 'Sin asignar'.
     *
     * Uso: $consulta->medico_nombre
     */
    public function getMedicoNombreAttribute(): string
    {
        // Prioridad 1: empleado de nómina
        if ($this->empleado) {
            return $this->empleado->nombre_completo;
        }
        // Prioridad 2: usuario del sistema con rol médico
        if ($this->medico) {
            return $this->medico->nombre_completo;
        }
        // Sin médico asignado
        return 'Sin asignar';
    }

    /**
     * Convierte el estado de la consulta a texto legible.
     * Reemplaza guiones bajos por espacios y capitaliza la primera letra.
     * Ejemplo: 'en_proceso' → 'En proceso'
     *
     * Uso: $consulta->estado_texto
     */
    public function getEstadoTextoAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->estado));
    }

    /**
     * Convierte el tipo de consulta a texto legible en español.
     * Uso: $consulta->tipo_consulta_texto
     */
    public function getTipoConsultaTextoAttribute(): string
    {
        return match ($this->tipo_consulta) {
            'preempleo'      => 'Pre-empleo',
            'prevacacional'  => 'Pre-vacacional',
            'postvacacional' => 'Post-vacacional',
            'egreso'         => 'Egreso',
            'reubicacion'    => 'Reubicación',
            'otros'          => 'Otros',
            default          => '—',
        };
    }

    // ══════════════════════════════════════════════════════════════════════════
    // SCOPES (filtros reutilizables para consultas Eloquent)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Filtra consultas del día de hoy.
     * Uso: Consulta::hoy()->get()
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_hora', today());
    }

    /**
     * Filtra consultas dentro de un rango de fechas.
     * Compara solo la parte de fecha (sin hora) usando DATE().
     *
     * @param string $inicio Fecha de inicio en formato Y-m-d
     * @param string $fin    Fecha de fin en formato Y-m-d
     * Uso: Consulta::byDateRange('2026-01-01', '2026-12-31')->get()
     */
    public function scopeByDateRange($query, string $inicio, string $fin)
    {
        return $query->whereBetween(\DB::raw('DATE(fecha_hora)'), [$inicio, $fin]);
    }

    /**
     * Carga las relaciones más comunes de una consulta.
     * Evita el problema N+1 al listar múltiples consultas.
     * Uso: Consulta::withRelations()->get()
     */
    public function scopeWithRelations($query)
    {
        return $query->with(['paciente', 'empleado', 'medico'])
                     ->select('consultas.*');
    }
}
