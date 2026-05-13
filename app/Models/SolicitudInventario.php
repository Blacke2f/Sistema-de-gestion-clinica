<?php

namespace App\Models;

/**
 * Modelo de Solicitud de Inventario
 * Representa una solicitud de reposición de medicamentos.
 * @version 2.5
 */

use Illuminate\Database\Eloquent\Model;

class SolicitudInventario extends Model
{
    protected $table = 'solicitudes_inventario';

    protected $fillable = [
        'numero_orden',
        'medicamento_id',
        'solicitado_por',
        'cantidad_solicitada',
        'proveedor_sugerido',
        'telefono_proveedor',
        'numero_factura',
        'fecha_entrega_esperada',
        'notas',
        'estado',
        'atendida_por',
        'atendida_at',
    ];

    /**
     * Genera un número de orden único: SOL-YYYYMM-XXXX
     */
    public static function generarNumeroOrden(): string
    {
        $prefix = 'SOL-' . now()->format('Ym') . '-';
        $ultimo = static::where('numero_orden', 'like', $prefix . '%')
            ->orderByDesc('numero_orden')
            ->value('numero_orden');

        $siguiente = $ultimo ? (int)substr($ultimo, -4) + 1 : 1;
        return $prefix . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }

    protected $casts = [
        'atendida_at' => 'datetime',
    ];

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'medicamento_id');
    }

    public function solicitadoPor()
    {
        return $this->belongsTo(Usuario::class, 'solicitado_por');
    }

    public function atendidaPor()
    {
        return $this->belongsTo(Usuario::class, 'atendida_por');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }
}
