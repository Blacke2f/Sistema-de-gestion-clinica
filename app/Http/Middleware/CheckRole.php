<?php

namespace App\Http\Middleware;

/**
 * Middleware de Control de Acceso
 *
 * Verifica que el usuario autenticado tenga el rol requerido para acceder
 * a una ruta. Los administradores siempre tienen acceso total.
 *
 * Uso en rutas: ->middleware('role:admin')
 *
 * @version 2.5
 */

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Verifica que el usuario tenga uno de los roles requeridos.
     *
     * @param Request  $request  Petición HTTP actual
     * @param Closure  $next     Siguiente middleware en la cadena
     * @param string[] $roles    Roles permitidos (ej: 'admin', 'medico')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Verificar que hay sesión activa y que el rol del usuario está en la lista permitida
        if (! Auth::check() || ! in_array(Auth::user()->rol, $roles)) {
            // Si es una petición AJAX/JSON, retornar error 403 en JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sin permiso para esta acción.'
                ], 403);
            }
            // Si es una petición normal, redirigir al dashboard con mensaje de error
            return redirect()->route('dashboard')
                ->with('error', 'Sin permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
