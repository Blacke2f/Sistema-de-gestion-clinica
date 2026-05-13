<?php

namespace App\Http\Controllers;

/**
 * Controlador de Bitácora del Sistema
 *
 * Muestra el registro completo de auditoría del sistema.
 * Solo accesible para administradores (middleware role:admin en rutas).
 *
 * La bitácora registra automáticamente todas las acciones críticas:
 * inicio/cierre de sesión, creación/edición/eliminación de usuarios,
 * entradas y salidas de inventario, cambios de perfil, etc.
 *
 * Permite filtrar por:
 *   - Acción (INICIO SESIÓN, CREAR USUARIO, ENTRADA INVENTARIO, etc.)
 *   - Usuario específico
 *   - Fecha exacta
 *
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * Muestra la lista paginada de registros de auditoría con filtros.
     *
     * Los filtros son opcionales y se combinan (AND lógico).
     * Por defecto muestra 25 registros por página (más que otros módulos
     * porque la bitácora se consulta para revisar historial).
     *
     * @param Request $request Puede contener: accion, usuario_id, fecha, per_page
     */
    public function index(Request $request)
    {
        // Validar per_page — por defecto 25 para la bitácora
        $perPage = in_array((int)$request->get('per_page', 25), [10, 15, 20, 50])
            ? (int)$request->get('per_page', 25)
            : 25;

        // Construir la consulta con filtros opcionales
        $bitacora = Bitacora::with('usuario') // cargar datos del usuario para mostrar su nombre
            // Filtro por tipo de acción (ej: 'INICIO SESIÓN', 'CREAR USUARIO')
            ->when($request->accion,     fn ($q) => $q->where('accion', $request->accion))
            // Filtro por usuario específico (ID del usuario)
            ->when($request->usuario_id, fn ($q) => $q->where('usuario_id', $request->usuario_id))
            // Filtro por fecha exacta (solo la parte de fecha, sin hora)
            ->when($request->fecha,      fn ($q) => $q->whereDate('created_at', $request->fecha))
            ->latest()           // más recientes primero
            ->paginate($perPage)
            ->withQueryString(); // preservar filtros en los links de paginación

        return view('bitacora.index', compact('bitacora'));
    }
}
