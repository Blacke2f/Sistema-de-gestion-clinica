<?php

namespace App\Models;

/**
 * Permisos por defecto según rol — sin módulo de facturación.
 * Sistema institucional: los medicamentos se reciben por asignación,
 * no se cobran. Se mantiene control de stock y auditoría.
 */
class PermisosPorRol
{
    public static function parRol(string $rol): array
    {
        return match ($rol) {
            'medico'        => static::medico(),
            'recepcionista' => static::recepcionista(),
            'enfermera'     => static::enfermera(),
            'farmaceutico'  => static::farmaceutico(),
            'contador'      => static::contador(),
            'laboratorista' => static::laboratorista(),
            default         => [],
        };
    }

    public static function aplicar(int $usuarioId, string $rol): void
    {
        if ($rol === 'admin') return;
        foreach (static::parRol($rol) as $modulo => $acciones) {
            PermisoUsuario::updateOrCreate(
                ['usuario_id' => $usuarioId, 'modulo' => $modulo],
                [
                    'puede_ver'      => $acciones['ver']      ?? false,
                    'puede_crear'    => $acciones['crear']    ?? false,
                    'puede_editar'   => $acciones['editar']   ?? false,
                    'puede_eliminar' => $acciones['eliminar'] ?? false,
                ]
            );
        }
    }

    // ── Médico: acceso clínico completo ───────────────────────────────────────
    private static function medico(): array
    {
        return [
            'pacientes'   => ['ver'=>true,  'crear'=>true,  'editar'=>true,  'eliminar'=>false],
            'consultas'   => ['ver'=>true,  'crear'=>true,  'editar'=>true,  'eliminar'=>false],
            'citas'       => ['ver'=>true,  'crear'=>true,  'editar'=>true,  'eliminar'=>false],
            'recetas'     => ['ver'=>true,  'crear'=>true,  'editar'=>false, 'eliminar'=>false],
            'informes'    => ['ver'=>true,  'crear'=>true,  'editar'=>true,  'eliminar'=>false],
            'gastos'      => ['ver'=>true,  'crear'=>true,  'editar'=>true,  'eliminar'=>false],
            'examenes'    => ['ver'=>true,  'crear'=>true,  'editar'=>true,  'eliminar'=>false],
            'medicina'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'inventario'  => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'mensajes'    => ['ver'=>true,  'crear'=>true,  'editar'=>false, 'eliminar'=>true],
            'solicitudes' => ['ver'=>true,  'crear'=>true,  'editar'=>false, 'eliminar'=>false],
            'reportes'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
        ];
    }

    // ── Recepcionista: atención al paciente y agenda ──────────────────────────
    private static function recepcionista(): array
    {
        return [
            'pacientes'   => ['ver'=>true,  'crear'=>true,  'editar'=>true,  'eliminar'=>false],
            'consultas'   => ['ver'=>true,  'crear'=>true,  'editar'=>true,  'eliminar'=>false],
            'citas'       => ['ver'=>true,  'crear'=>true,  'editar'=>true,  'eliminar'=>true],
            'recetas'     => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'informes'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'gastos'      => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'examenes'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'medicina'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'inventario'  => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'mensajes'    => ['ver'=>true,  'crear'=>true,  'editar'=>false, 'eliminar'=>true],
            'solicitudes' => ['ver'=>true,  'crear'=>true,  'editar'=>false, 'eliminar'=>false],
            'reportes'    => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
        ];
    }

    // ── Enfermera: signos vitales y apoyo clínico ─────────────────────────────
    private static function enfermera(): array
    {
        return [
            'pacientes'   => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'consultas'   => ['ver'=>true,  'crear'=>false, 'editar'=>true,  'eliminar'=>false],
            'citas'       => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'recetas'     => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'informes'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'gastos'      => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'examenes'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'medicina'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'inventario'  => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'mensajes'    => ['ver'=>true,  'crear'=>true,  'editar'=>false, 'eliminar'=>true],
            'solicitudes' => ['ver'=>true,  'crear'=>true,  'editar'=>false, 'eliminar'=>false],
            'reportes'    => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
        ];
    }

    // ── Farmacéutico: control total de inventario ─────────────────────────────
    private static function farmaceutico(): array
    {
        return [
            'pacientes'   => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'consultas'   => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'citas'       => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'recetas'     => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'informes'    => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'gastos'      => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'examenes'    => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'medicina'    => ['ver'=>true,  'crear'=>true,  'editar'=>true,  'eliminar'=>false],
            'inventario'  => ['ver'=>true,  'crear'=>true,  'editar'=>true,  'eliminar'=>false],
            'mensajes'    => ['ver'=>true,  'crear'=>true,  'editar'=>false, 'eliminar'=>true],
            'solicitudes' => ['ver'=>true,  'crear'=>true,  'editar'=>true,  'eliminar'=>false],
            'reportes'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
        ];
    }

    // ── Contador: reportes y estadísticas (sin cobros) ────────────────────────
    private static function contador(): array
    {
        return [
            'pacientes'   => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'consultas'   => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'citas'       => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'recetas'     => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'informes'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'gastos'      => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'examenes'    => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'medicina'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'inventario'  => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'mensajes'    => ['ver'=>true,  'crear'=>true,  'editar'=>false, 'eliminar'=>true],
            'solicitudes' => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'reportes'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
        ];
    }

    // ── Laboratorista: resultados y consultas ─────────────────────────────────
    private static function laboratorista(): array
    {
        return [
            'pacientes'   => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'consultas'   => ['ver'=>true,  'crear'=>false, 'editar'=>true,  'eliminar'=>false],
            'citas'       => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'recetas'     => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'informes'    => ['ver'=>true,  'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'gastos'      => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'examenes'    => ['ver'=>true,  'crear'=>true,  'editar'=>false, 'eliminar'=>false],
            'medicina'    => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'inventario'  => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'mensajes'    => ['ver'=>true,  'crear'=>true,  'editar'=>false, 'eliminar'=>true],
            'solicitudes' => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
            'reportes'    => ['ver'=>false, 'crear'=>false, 'editar'=>false, 'eliminar'=>false],
        ];
    }
}
