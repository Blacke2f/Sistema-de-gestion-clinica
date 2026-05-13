<?php

namespace App\Models;

/**
 * Modelo de Medicamento (Inventario)
 *
 * Representa un medicamento almacenado en el inventario de la clínica.
 * Controla el stock actual y el stock mínimo para generar alertas.
 * Cada movimiento de entrada o salida actualiza la cantidad de este modelo.
 *
 * Tabla en BD: inventario (nombre diferente al modelo por convención del negocio)
 * @version 2.5
 * @package App\Models
 */

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    // La tabla se llama 'inventario', no 'medicamentos'
    protected $table = 'inventario';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'nombre_medicamento', // Nombre comercial o genérico del medicamento
        'descripcion',        // Descripción adicional (opcional)
        'cantidad',           // Stock actual disponible en la clínica
        'unidad',             // Unidad de medida: tabletas, cápsulas, frascos, etc.
        'precio',             // Precio unitario en RD$
        'fecha_vencimiento',  // Fecha de vencimiento del lote actual (opcional)
        'proveedor',          // Nombre del proveedor habitual (opcional)
        'telefono_proveedor', // Teléfono del proveedor para contacto (opcional)
        'stock_minimo',       // Cantidad mínima antes de generar alerta de stock bajo
    ];

    // Conversión automática de tipos al leer de la BD
    protected $casts = [
        'precio'            => 'decimal:2', // Siempre 2 decimales
        'fecha_vencimiento' => 'date',      // Solo fecha, sin hora
    ];

    // ══════════════════════════════════════════════════════════════════════════
    // RELACIONES CON OTROS MODELOS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Historial de movimientos (entradas y salidas) de este medicamento.
     * Cada vez que entra o sale stock, se crea un registro aquí.
     */
    public function movimientos()
    {
        return $this->hasMany(InventarioMovimiento::class, 'medicamento_id');
    }

    /**
     * Consultas en las que se recetó este medicamento.
     * Relación muchos a muchos a través de la tabla consultas_medicamentos.
     */
    public function consultas()
    {
        return $this->belongsToMany(
            Consulta::class,
            'consultas_medicamentos', // tabla pivote
            'medicamento_id',         // clave de este modelo en la pivote
            'consulta_id'             // clave del otro modelo en la pivote
        )
        ->withPivot('cantidad', 'indicaciones'); // datos extra de la pivote
    }

    // ══════════════════════════════════════════════════════════════════════════
    // SCOPES (filtros reutilizables)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Filtra medicamentos cuyo stock actual es menor o igual al stock mínimo.
     * Se usa para mostrar alertas en el dashboard y en la vista de inventario.
     * Uso: Medicamento::stockBajo()->get()
     */
    public function scopeStockBajo($query)
    {
        // whereColumn compara dos columnas de la misma tabla
        return $query->whereColumn('cantidad', '<=', 'stock_minimo');
    }

    /**
     * Busca medicamentos por nombre o proveedor.
     * Usa LIKE para búsqueda parcial (no exacta).
     *
     * @param string $term Término de búsqueda
     * Uso: Medicamento::search('paracetamol')->get()
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nombre_medicamento', 'like', "%{$term}%") // busca en nombre
              ->orWhere('proveedor',         'like', "%{$term}%"); // o en proveedor
        });
    }

    /**
     * Filtra medicamentos que vencen en los próximos N días.
     * Por defecto busca los que vencen en los próximos 30 días.
     *
     * @param int $dias Número de días hacia adelante
     * Uso: Medicamento::vencimientoProximo(15)->get()
     */
    public function scopeVencimientoProximo($query, int $dias = 30)
    {
        return $query->whereBetween('fecha_vencimiento', [
            today(),              // desde hoy
            today()->addDays($dias) // hasta N días en el futuro
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // HELPERS (métodos de utilidad)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Verifica si el medicamento tiene stock bajo.
     * Retorna true si la cantidad actual es menor o igual al stock mínimo.
     * Se usa en las vistas para mostrar badges de alerta roja.
     *
     * Uso: if ($medicamento->tieneStockBajo()) { ... }
     */
    public function tieneStockBajo(): bool
    {
        return $this->cantidad <= $this->stock_minimo;
    }
}
