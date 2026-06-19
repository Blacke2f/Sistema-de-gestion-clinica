<?php

namespace App\Models;

/**
 * Modelo de Permisos Granulares por Usuario
 *
 * Permite al administrador definir exactamente qué puede hacer cada usuario
 * en cada módulo del sistema. Cada registro representa los permisos de un
 * usuario en un módulo específico.
 *
 * Ejemplo: usuario_id=5, modulo='pacientes', puede_ver=true, puede_crear=false
 * → El usuario 5 puede ver pacientes pero no crear nuevos.
 *
 * REGLA IMPORTANTE: Los administradores siempre tienen acceso total.
 * Sus permisos no se guardan en esta tabla — el método puedeEn() del modelo
 * Usuario retorna true automáticamente para admins.
 *
 * Tabla en BD: permisos_usuario
 * @version 2.5
 * @package App\Models
 */

use Illuminate\Database\Eloquent\Model;

class PermisoUsuario extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'permisos_usuario';

    // Campos permitidos para asignación masiva
    protected $fillable = [
        'usuario_id',     // ID del usuario al que aplican estos permisos
        'modulo',         // Nombre del módulo: 'pacientes', 'consultas', etc.
        'puede_ver',      // true = puede acceder y ver la lista del módulo
        'puede_crear',    // true = puede crear nuevos registros
        'puede_editar',   // true = puede modificar registros existentes
        'puede_eliminar', // true = puede eliminar registros
    ];

    // Conversión automática: los valores 0/1 de la BD se convierten a true/false
    protected $casts = [
        'puede_ver'      => 'boolean',
        'puede_crear'    => 'boolean',
        'puede_editar'   => 'boolean',
        'puede_eliminar' => 'boolean',
    ];

    // ══════════════════════════════════════════════════════════════════════════
    // RELACIONES
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Usuario propietario de estos permisos.
     * Permite acceder a los datos del usuario desde un permiso.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // CATÁLOGO DE MÓDULOS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Retorna el catálogo completo de módulos del sistema.
     * Cada módulo tiene: etiqueta legible, ícono Boxicons y color de acento.
     *
     * Se usa en:
     * - La tabla de permisos del modal en usuarios/index.blade.php
     * - El método guardarTodos() para iterar todos los módulos
     * - El sidebar del layout para mostrar/ocultar ítems del menú
     *
     * @return array Módulos indexados por su clave interna
     */
    public static function modulos(): array
    {
        return [
            'pacientes'   => ['label' => 'Pacientes',     'icon' => 'bxs-user-detail',   'color' => '#0d6efd'],
            'consultas'   => ['label' => 'Consultas',     'icon' => 'bx-clipboard',      'color' => '#0dcaf0'],
            'citas'       => ['label' => 'Agenda/Citas',  'icon' => 'bx-calendar-event', 'color' => '#0d6efd'],
            'recetas'     => ['label' => 'Recetas',       'icon' => 'bx-file-blank',     'color' => '#198754'],
            'informes'    => ['label' => 'Informes Medicos',  'icon' => 'bx-id-card',   'color' => '#0dcaf0'],
            'gastos'      => ['label' => 'Gastos Medicos',    'icon' => 'bx-receipt',   'color' => '#fd7e14'],
            'examenes'    => ['label' => 'Examenes de Lab.',  'icon' => 'bx-test-tube', 'color' => '#198754'],
            'medicina'    => ['label' => 'Medicamentos',  'icon' => 'bxs-capsule',       'color' => '#fd7e14'],
            'inventario'  => ['label' => 'Movimientos',   'icon' => 'bx-transfer',       'color' => '#198754'],
            'solicitudes' => ['label' => 'Solicitudes',   'icon' => 'bx-send',           'color' => '#856404'],
            'mensajes'    => ['label' => 'Mensajes',      'icon' => 'bxs-envelope',      'color' => '#0dcaf0'],
            'reportes'    => ['label' => 'Reportes',      'icon' => 'bx-bar-chart-alt-2','color' => '#6f42c1'],
            'nomina'      => ['label' => 'Nómina',        'icon' => 'bxs-group',         'color' => '#6f42c1'],
            'usuarios'    => ['label' => 'Usuarios',      'icon' => 'bxs-user-account',  'color' => '#1a2234'],
            'bitacora'    => ['label' => 'Bitácora',      'icon' => 'bx-history',        'color' => '#64748b'],
        ];
    }

    // ══════════════════════════════════════════════════════════════════════════
    // HELPERS ESTÁTICOS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Obtiene los permisos de un usuario para un módulo específico.
     *
     * Si no existe el registro en la BD (nunca se configuraron permisos),
     * retorna un objeto nuevo con todos los permisos en false (sin guardar).
     * Esto garantiza que un usuario sin permisos configurados no tenga acceso.
     *
     * @param int    $usuarioId ID del usuario
     * @param string $modulo    Nombre del módulo (ej: 'pacientes')
     * @return self  Objeto PermisoUsuario (existente o nuevo vacío)
     */
    public static function obtener(int $usuarioId, string $modulo): self
    {
        // firstOrNew: busca el registro, si no existe crea uno en memoria (sin guardar)
        return static::firstOrNew(
            // Condición de búsqueda
            ['usuario_id' => $usuarioId, 'modulo' => $modulo],
            // Valores por defecto si no existe (todos en false = sin acceso)
            ['puede_ver' => false, 'puede_crear' => false, 'puede_editar' => false, 'puede_eliminar' => false]
        );
    }

    /**
     * Guarda o actualiza los permisos de un usuario para TODOS los módulos.
     *
     * Itera sobre todos los módulos del catálogo y hace un upsert (INSERT o UPDATE)
     * para cada uno. Si el módulo no viene en el array $permisos, se guardan
     * todos los permisos en false (sin acceso).
     *
     * Se llama desde UsuariosController::guardarPermisos() al enviar el formulario.
     *
     * @param int   $usuarioId ID del usuario cuyos permisos se actualizan
     * @param array $permisos  Array con estructura:
     *                         ['pacientes' => ['ver'=>'1', 'crear'=>'1', 'editar'=>'0', 'eliminar'=>'0'], ...]
     */
    public static function guardarTodos(int $usuarioId, array $permisos): void
    {
        // Iterar sobre TODOS los módulos del catálogo (no solo los enviados)
        // Esto garantiza que si un módulo no viene en el form, se guarda como false
        foreach (array_keys(static::modulos()) as $modulo) {
            // Obtener los permisos enviados para este módulo (o array vacío si no vino)
            $datos = $permisos[$modulo] ?? [];

            // updateOrCreate: actualiza si existe, inserta si no existe
            static::updateOrCreate(
                // Clave única: usuario + módulo
                ['usuario_id' => $usuarioId, 'modulo' => $modulo],
                // Valores a guardar: !empty convierte '1' en true y '' en false
                [
                    'puede_ver'      => !empty($datos['ver']),
                    'puede_crear'    => !empty($datos['crear']),
                    'puede_editar'   => !empty($datos['editar']),
                    'puede_eliminar' => !empty($datos['eliminar']),
                ]
            );
        }
    }
}
