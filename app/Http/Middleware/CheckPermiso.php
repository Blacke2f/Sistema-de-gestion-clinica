<?php

namespace App\Http\Middleware;

/**
 * Middleware de Permisos Granulares (ACL)
 *
 * Verifica que el usuario autenticado tenga el permiso específico
 * para realizar una acción en un módulo determinado.
 *
 * Los administradores siempre tienen acceso total — este middleware
 * los deja pasar sin verificar la tabla permisos_usuario.
 *
 * Para usuarios no-admin, consulta la tabla permisos_usuario y verifica
 * si el campo correspondiente (puede_ver, puede_crear, etc.) es true.
 *
 * Uso en rutas:
 *   ->middleware('permiso:pacientes,ver')      // solo ver la lista
 *   ->middleware('permiso:pacientes,crear')    // crear nuevos registros
 *   ->middleware('permiso:pacientes,editar')   // editar registros
 *   ->middleware('permiso:pacientes,eliminar') // eliminar registros
 *
 * @version 2.5
 */

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermiso
{
    /**
     * Verifica el permiso antes de ejecutar el controlador.
     *
     * @param Request $request  Petición HTTP actual
     * @param Closure $next     Siguiente middleware en la cadena
     * @param string  $modulo   Módulo a verificar: 'pacientes', 'consultas', etc.
     * @param string  $accion   Acción requerida: 'ver', 'crear', 'editar', 'eliminar'
     */
    public function handle(Request $request, Closure $next, string $modulo, string $accion = 'ver'): Response
    {
        // Obtener el usuario autenticado
        $usuario = Auth::user();

        // Si no hay sesión activa, redirigir al login
        if (!$usuario) {
            return redirect()->route('login');
        }

        // ── Administradores: acceso total sin verificación ────────────────────
        // Los admins nunca tienen restricciones de permisos
        if ($usuario->isAdmin()) {
            return $next($request); // dejar pasar directamente
        }

        // ── Usuarios no-admin: verificar permisos en la BD ───────────────────

        // Cargar los permisos del usuario si aún no están en memoria
        // (eager loading para evitar múltiples queries por request)
        if (!$usuario->relationLoaded('permisos')) {
            $usuario->load('permisos');
        }

        // Verificar si el usuario tiene el permiso específico
        // El método puedeEn() busca en la colección ya cargada (sin nueva query)
        if (!$usuario->puedeEn($modulo, $accion)) {
            // ── Respuesta para peticiones AJAX/JSON (fetch, axios) ────────────
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sin permiso para esta acción.'
                ], 403); // 403 Forbidden
            }

            // ── Respuesta para peticiones normales (navegación) ───────────────
            // Redirigir al dashboard con mensaje de error en la sesión flash
            return redirect()->route('dashboard')
                ->with('error', 'Sin permiso para acceder a esta sección.');
        }

        // El usuario tiene el permiso — continuar con el controlador
        return $next($request);
    }
}
