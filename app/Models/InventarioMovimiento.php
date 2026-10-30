<?php

namespace App\Models;

/**
 * Modelo de Movimiento de Inventario
 *
 * Registra cada entrada o salida de medicamentos del inventario.
 * Para las salidas, almacena quién entregó y quién recibió el medicamento,
 * permitiendo generar comprobantes de entrega con firma para auditoría.
 *
 * Tabla en BD: inventario_movimientos
 * @version 2.5
 * @package App\Models
 */

use Illuminate\Database\Eloquent\Model;

class InventarioMovimiento extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'inventario_movimientos';

    // Solo usa created_at — cada movimiento es inmutable una vez registrado (no tiene updated_at)
    public $timestamps = false;

    /**
     * Al crear, rellenar automáticamente created_at.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });
    }

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'medicamento_id',       // ID del medicamento afectado
        'tipo_movimiento',      // 'entrada' o 'salida'
        'cantidad',             // Cantidad de unidades que entran o salen
        'cantidad_anterior',    // Stock antes del movimiento (para auditoría)
        'cantidad_nueva',       // Stock después del movimiento (para auditoría)
        'motivo',               // Razón del movimiento (texto libre)
        'telefono_proveedor',   // Teléfono del proveedor (solo en entradas)
        'consulta_id',          // ID de la consulta que generó la salida (opcional)
        'paciente_id',          // ID del paciente que recibe el medicamento (opcional)
        'usuario_id',           // ID del usuario que registró el movimiento
        // ── Campos de firma para comprobante de entrega ──
        'entregado_por_nombre', // Nombre de quien entrega el medicamento
        'recibido_por_nombre',  // Nombre de quien recibe el medicamento
        'recibido_por_cedula',  // Cédula de quien recibe (identificación)
        'observaciones_entrega',// Observaciones adicionales de la entrega
    ];

    // Conversión automática de tipos al leer de la BD
    protected $casts = [
        'created_at' => 'datetime', // Fecha y hora del movimiento
    ];

    // ══════════════════════════════════════════════════════════════════════════
    // RELACIONES CON OTROS MODELOS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Medicamento afectado por este movimiento.
     */
    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'medicamento_id')->withTrashed();
    }

    /**
     * Usuario del sistema que registró el movimiento.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    /**
     * Consulta médica que generó este movimiento (solo salidas por consulta).
     */
    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id')->withTrashed();
    }

    /**
     * Paciente que recibió el medicamento (solo salidas a pacientes).
     */
    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id')->withTrashed();
    }

    // ══════════════════════════════════════════════════════════════════════════
    // SCOPES (filtros reutilizables)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Filtra movimientos dentro de un rango de fechas.
     * Compara solo la parte de fecha (sin hora).
     *
     * @param string $inicio Fecha inicio en formato Y-m-d
     * @param string $fin    Fecha fin en formato Y-m-d
     */
    public function scopeByDateRange($query, string $inicio, string $fin)
    {
        return $query->whereBetween(\DB::raw('DATE(created_at)'), [$inicio, $fin]);
    }

    /**
     * Filtra solo movimientos de tipo 'entrada' (medicamentos que ingresan).
     * Uso: InventarioMovimiento::entradas()->get()
     */
    public function scopeEntradas($query)
    {
        return $query->where('tipo_movimiento', 'entrada');
    }

    /**
     * Filtra solo movimientos de tipo 'salida' (medicamentos que salen).
     * Uso: InventarioMovimiento::salidas()->get()
     */
    public function scopeSalidas($query)
    {
        return $query->where('tipo_movimiento', 'salida');
    }
}
