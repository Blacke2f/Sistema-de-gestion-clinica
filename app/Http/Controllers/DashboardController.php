<?php

namespace App\Http\Controllers;

/**
 * Controlador del Panel de Control (Dashboard)
 *
 * Muestra la página principal del sistema con estadísticas en tiempo real:
 * - Total de pacientes registrados
 * - Consultas programadas para hoy
 * - Medicamentos con stock bajo (alertas)
 * - Mensajes no leídos del usuario actual
 *
 * También muestra las últimas 4 consultas y, para administradores,
 * las últimas 15 acciones de la bitácora del sistema.
 *
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Bitacora;
use App\Models\Consulta;
use App\Models\Medicamento;
use App\Models\Paciente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el panel de control principal.
     * Recopila estadísticas de múltiples módulos para mostrar en tarjetas.
     */
    public function index()
    {
        // Obtener el usuario actualmente autenticado
        $usuario = Auth::user();

        // ── Estadísticas para las tarjetas del dashboard ──────────────────────
        $stats = [
            // Total de pacientes registrados en el sistema
            'pacientes'  => Paciente::count(),

            // Consultas programadas para el día de hoy (usa el scope 'hoy' del modelo)
            'consultas'  => Consulta::hoy()->count(),

            // Medicamentos cuyo stock actual es menor o igual al stock mínimo
            // Se usa el scope 'stockBajo' del modelo Medicamento
            'inventario' => Medicamento::stockBajo()->count(),

            // Mensajes no leídos del usuario actual (método del modelo Usuario)
            'mensajes'   => $usuario->mensajesNoLeidos(),
        ];

        // ── Consultas recientes para la tabla del dashboard ───────────────────
        // Carga las relaciones para evitar N+1 queries (eager loading)
        $consultasRecientes = Consulta::with(['paciente', 'empleado', 'medico'])
            ->latest()   // ordenar por created_at DESC (más recientes primero)
            ->limit(4)   // solo las últimas 4 consultas
            ->get();

        // ── Bitácora del sistema (solo para administradores) ──────────────────
        $bitacora = null; // por defecto null para no-admins

        if ($usuario->isAdmin()) {
            // Los admins ven las últimas 15 acciones del sistema
            $bitacora = Bitacora::with('usuario') // cargar el usuario de cada registro
                ->latest()
                ->limit(15)
                ->get();
        }

        // Pasar todas las variables a la vista del dashboard
        return view('dashboard.index', compact('stats', 'consultasRecientes', 'bitacora'));
    }
}
