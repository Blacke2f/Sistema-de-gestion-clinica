<?php

namespace App\Models;

/**
 * Modelo de Bitácora del Sistema
 *
 * Registra automáticamente todas las acciones críticas realizadas en el sistema.
 * Funciona como un log de auditoría: quién hizo qué, cuándo y desde qué IP.
 * Solo los administradores pueden ver la bitácora completa.
 *
 * Tabla en BD: bitacora
 * @version 2.5
 * @package App\Models
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class Bitacora extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'bitacora';

    // No usa updated_at, solo created_at (cada registro es inmutable)
    public $timestamps = false;

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'usuario_id',  // ID del usuario que realizó la acción
        'accion',      // Etiqueta de la acción: 'INICIO SESIÓN', 'CREAR PACIENTE', etc.
        'tabla',       // Tabla de BD afectada: 'pacientes', 'usuarios', etc. (opcional)
        'registro_id', // ID del registro afectado en esa tabla (opcional)
        'detalles',    // Descripción legible de lo que se hizo (opcional)
        'ip',          // Dirección IP del cliente que realizó la acción
    ];

    // Conversión automática de tipos
    protected $casts = [
        'created_at' => 'datetime', // Fecha y hora del registro
    ];

    // ══════════════════════════════════════════════════════════════════════════
    // RELACIONES CON OTROS MODELOS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Usuario que generó este registro de bitácora.
     * Permite mostrar el nombre del usuario en la vista de auditoría.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // MÉTODO ESTÁTICO DE REGISTRO
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Registra una acción en la bitácora del sistema.
     *
     * Este método estático se llama desde los controladores cada vez que
     * se realiza una acción importante. Captura automáticamente la IP del cliente.
     *
     * Ejemplo de uso en un controlador:
     *   Bitacora::registrar(Auth::id(), 'CREAR PACIENTE', 'pacientes', $paciente->id, "Creó el paciente Juan Pérez");
     *
     * @param int         $usuarioId  ID del usuario que realiza la acción
     * @param string      $accion     Etiqueta descriptiva de la acción
     * @param string|null $tabla      Tabla de BD afectada (opcional)
     * @param int|null    $registroId ID del registro afectado (opcional)
     * @param string|null $detalles   Descripción adicional (opcional)
     */
    public static function registrar(
        int $usuarioId,
        string $accion,
        ?string $tabla      = null,
        ?int $registroId    = null,
        ?string $detalles   = null
    ): void {
        static::create([
            'usuario_id'  => $usuarioId,
            'accion'      => $accion,
            'tabla'       => $tabla,
            'registro_id' => $registroId,
            'detalles'    => $detalles,
            'ip'          => Request::ip(), // IP del cliente actual
        ]);
    }
}
