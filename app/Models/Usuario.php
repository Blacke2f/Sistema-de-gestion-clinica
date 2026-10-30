<?php

namespace App\Models;

/**
 * Modelo de Usuario del Sistema
 *
 * Representa un usuario que puede iniciar sesión en SISGECLIN.
 * Extiende Authenticatable para integrarse con el sistema de autenticación de Laravel.
 * Los roles disponibles son: admin, medico, recepcionista.
 * Las cuentas nuevas quedan deshabilitadas (activo=false) hasta que el admin las active.
 *
 * Tabla en BD: usuarios
 * @version 2.5
 * @package App\Models
 */

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    // Trait que permite enviar notificaciones de Laravel (email, etc.)
    use Notifiable;

    // Nombre de la tabla (no usa la convención 'users' de Laravel)
    protected $table = 'usuarios';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'username',        // Nombre de usuario único para iniciar sesión
        'email',           // Correo electrónico único
        'password',        // Contraseña hasheada con bcrypt
        'nombre_completo', // Nombre completo para mostrar en la interfaz
        'rol',             // Rol: admin, medico, recepcionista
        'activo',          // true = puede iniciar sesión, false = bloqueado
        'ultimo_acceso',   // Fecha y hora del último inicio de sesión exitoso
    ];

    // Campos que NUNCA se incluyen en respuestas JSON (seguridad)
    protected $hidden = [
        'password',       // La contraseña nunca se expone en JSON
        'remember_token', // Token de "recordarme" tampoco se expone
    ];

    // ── Métodos requeridos por Laravel para autenticación personalizada ──────

    /**
     * Indica a Laravel qué columna contiene la contraseña.
     * Necesario porque usamos 'password' en lugar del campo por defecto.
     */
    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    /**
     * Retorna el valor de la contraseña hasheada.
     * Laravel lo usa internamente para verificar credenciales.
     */
    public function getAuthPassword(): string
    {
        return $this->password;
    }

    // Conversión automática de tipos al leer de la BD
    protected $casts = [
        'activo'        => 'boolean',  // 0/1 en BD → true/false en PHP
        'ultimo_acceso' => 'datetime', // string en BD → objeto Carbon en PHP
    ];

    // ══════════════════════════════════════════════════════════════════════════
    // RELACIONES CON OTROS MODELOS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Consultas que este usuario atiende como médico.
     * Solo aplica a usuarios con rol 'medico'.
     */
    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'medico_id');
    }

    /**
     * Mensajes que este usuario ha recibido de otros usuarios.
     */
    public function mensajesRecibidos()
    {
        return $this->hasMany(Mensaje::class, 'destinatario_id');
    }

    /**
     * Mensajes que este usuario ha enviado a otros usuarios.
     */
    public function mensajesEnviados()
    {
        return $this->hasMany(Mensaje::class, 'remitente_id');
    }

    /**
     * Registros de bitácora generados por las acciones de este usuario.
     */
    public function bitacora()
    {
        return $this->hasMany(Bitacora::class, 'usuario_id');
    }

    /**
     * Movimientos de inventario registrados por este usuario.
     */
    public function movimientos()
    {
        return $this->hasMany(InventarioMovimiento::class, 'usuario_id');
    }

    /**
     * Notificaciones recibidas por este usuario.
     */
    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }

    /**
     * Permisos granulares del usuario por módulo.
     * Solo aplica a no-administradores.
     */
    public function permisos()
    {
        return $this->hasMany(PermisoUsuario::class, 'usuario_id');
    }

    /**
     * Verifica si el usuario tiene un permiso específico en un módulo.
     * Los administradores siempre tienen todos los permisos.
     *
     * @param string $modulo  Nombre del módulo: 'pacientes', 'consultas', etc.
     * @param string $accion  Acción: 'ver', 'crear', 'editar', 'eliminar'
     *
     * Uso: if ($usuario->puedeEn('pacientes', 'crear')) { ... }
     */
    public function puedeEn(string $modulo, string $accion): bool
    {
        // Los administradores tienen acceso total siempre
        if ($this->isAdmin()) return true;

        // Buscar el permiso en la caché de relaciones cargadas
        $permiso = $this->permisos->firstWhere('modulo', $modulo);

        if (!$permiso) return false;

        return match ($accion) {
            'ver'      => (bool) $permiso->puede_ver,
            'crear'    => (bool) $permiso->puede_crear,
            'editar'   => (bool) $permiso->puede_editar,
            'eliminar' => (bool) $permiso->puede_eliminar,
            default    => false,
        };
    }

    // ══════════════════════════════════════════════════════════════════════════
    // MÉTODOS DE CONTEO
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Cuenta las notificaciones no leídas del usuario.
     * Se usa para mostrar el badge en la campana de notificaciones.
     * Retorna 0 si no hay notificaciones pendientes.
     */
    public function notificacionesNoLeidas(): int
    {
        return $this->notificaciones()->where('leida', false)->count();
    }

    /**
     * Cuenta los mensajes no leídos recibidos por el usuario.
     * Se usa para mostrar el badge en el ícono de mensajes.
     */
    public function mensajesNoLeidos(): int
    {
        return $this->mensajesRecibidos()->where('leido', false)->count();
    }

    // ══════════════════════════════════════════════════════════════════════════
    // SCOPES (filtros reutilizables)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Filtra solo usuarios con cuenta activa (activo = true).
     * Uso: Usuario::activos()->get()
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Filtra usuarios con rol médico o administrador.
     * Se usa para el selector de médicos en el módulo de mensajes.
     */
    public function scopeMedicos($query)
    {
        return $query->whereIn('rol', ['medico', 'admin']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // HELPERS (métodos de utilidad)
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Verifica si el usuario es administrador.
     * Uso: if ($usuario->isAdmin()) { ... }
     */
    public function isAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    /**
     * Verifica si el usuario tiene rol de médico.
     * Uso: if ($usuario->isMedico()) { ... }
     */
    public function isMedico(): bool
    {
        return $this->rol === 'medico';
    }
}
