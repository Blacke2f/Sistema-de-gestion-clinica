<?php

namespace App\Models;

/**
 * Modelo de Notificación en Tiempo Real
 *
 * Gestiona las notificaciones que aparecen en el dropdown de la campana.
 * Las notificaciones se crean automáticamente cuando ocurren eventos importantes:
 * nuevo paciente, nueva consulta, stock bajo, mensaje recibido, etc.
 * El frontend hace polling cada 30 segundos para actualizar los badges.
 *
 * Tabla en BD: notificaciones
 * @version 2.5
 * @package App\Models
 */

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'notificaciones';

    // No usa updated_at (las notificaciones no se modifican, solo se marcan como leídas)
    public $timestamps = false;

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'usuario_id', // ID del usuario que recibe la notificación
        'tipo',       // Tipo de evento: 'paciente_nuevo', 'stock_bajo', 'mensaje_nuevo', etc.
        'titulo',     // Título corto de la notificación (se muestra en negrita)
        'mensaje',    // Descripción detallada del evento
        'icono',      // Clase de Boxicons sin el prefijo 'bx-': ej. 'bxs-user-plus'
        'color',      // Color del ícono: primary, success, danger, warning, info
        'url',        // URL a la que navega al hacer clic (relativa, ej. '/pacientes')
        'leida',      // false = no leída (aparece resaltada), true = ya vista
    ];

    // Conversión automática de tipos
    protected $casts = [
        'leida'      => 'boolean',  // 0/1 en BD → true/false en PHP
        'created_at' => 'datetime', // Fecha y hora de creación
    ];

    // ══════════════════════════════════════════════════════════════════════════
    // RELACIONES CON OTROS MODELOS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Usuario que recibe esta notificación.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // MÉTODOS ESTÁTICOS DE CREACIÓN
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Envía una notificación a TODOS los usuarios activos del sistema.
     * Opcionalmente puede filtrar por rol o excluir a un usuario específico.
     *
     * Usa INSERT masivo (un solo query) para mejor rendimiento.
     * Se llama desde los controladores cuando ocurre un evento global.
     *
     * Ejemplo: al registrar un nuevo paciente, notificar a todos excepto quien lo registró.
     *   Notificacion::notificarTodos(
     *       tipo: 'paciente_nuevo',
     *       titulo: 'Nuevo Paciente',
     *       mensaje: 'Se registró Juan Pérez',
     *       icono: 'bxs-user-plus',
     *       color: 'success',
     *       url: '/pacientes',
     *       excluirId: Auth::id()
     *   );
     *
     * @param string      $tipo       Identificador del tipo de evento
     * @param string      $titulo     Título de la notificación
     * @param string      $mensaje    Descripción del evento
     * @param string      $icono      Clase Boxicons (sin 'bx-')
     * @param string      $color      Color: primary, success, danger, warning, info
     * @param string|null $url        URL de destino al hacer clic
     * @param string|null $rol        Si se especifica, solo notifica a ese rol
     * @param int|null    $excluirId  ID del usuario a excluir (quien realizó la acción)
     */
    public static function notificarTodos(
        string $tipo,
        string $titulo,
        string $mensaje,
        string $icono   = 'bx-bell',
        string $color   = 'primary',
        ?string $url    = null,
        ?string $rol    = null,
        ?int $excluirId = null
    ): void {
        // Construir la consulta base: solo usuarios activos
        $query = Usuario::activos();

        // Si se especifica un rol, filtrar solo ese rol
        if ($rol) {
            $query->where('rol', $rol);
        }

        // Excluir al usuario que realizó la acción (no se notifica a sí mismo)
        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }

        // Obtener solo los IDs para el INSERT masivo
        $usuarios = $query->pluck('id');

        // Construir el array de filas para insertar de una sola vez
        $rows = $usuarios->map(fn ($uid) => [
            'usuario_id' => $uid,
            'tipo'       => $tipo,
            'titulo'     => $titulo,
            'mensaje'    => $mensaje,
            'icono'      => $icono,
            'color'      => $color,
            'url'        => $url,
            'leida'      => false,       // todas empiezan como no leídas
            'created_at' => now(),       // timestamp actual
        ])->toArray();

        // Solo insertar si hay destinatarios
        if (!empty($rows)) {
            static::insert($rows); // INSERT masivo: un solo query para todos
        }
    }

    /**
     * Envía una notificación a UN usuario específico.
     * Se usa para eventos personales: mensaje recibido, solicitud atendida, etc.
     *
     * Ejemplo: notificar al solicitante cuando su solicitud fue aprobada.
     *   Notificacion::notificarUsuario(
     *       usuarioId: $solicitud->solicitado_por,
     *       tipo: 'solicitud_atendida',
     *       titulo: 'Solicitud Aprobada',
     *       mensaje: 'Tu solicitud fue aprobada',
     *       icono: 'bx-check-circle',
     *       color: 'success',
     *       url: '/solicitudes'
     *   );
     *
     * @param int         $usuarioId  ID del usuario destinatario
     * @param string      $tipo       Identificador del tipo de evento
     * @param string      $titulo     Título de la notificación
     * @param string      $mensaje    Descripción del evento
     * @param string      $icono      Clase Boxicons (sin 'bx-')
     * @param string      $color      Color del ícono
     * @param string|null $url        URL de destino al hacer clic
     */
    public static function notificarUsuario(
        int $usuarioId,
        string $tipo,
        string $titulo,
        string $mensaje,
        string $icono = 'bx-bell',
        string $color = 'primary',
        ?string $url  = null
    ): void {
        static::create([
            'usuario_id' => $usuarioId,
            'tipo'       => $tipo,
            'titulo'     => $titulo,
            'mensaje'    => $mensaje,
            'icono'      => $icono,
            'color'      => $color,
            'url'        => $url,
            'leida'      => false, // empieza como no leída
        ]);
    }
}
