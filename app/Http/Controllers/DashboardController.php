<?php

namespace App\Http\Controllers;

/**
 * Controlador del Panel de Control (Dashboard)
 *
 * Muestra la página principal del sistema con estadísticas en tiempo real:
 * - Total de pacientes registrados
 * - Consultas programadas para hoy
 * - Medicamentos con stock bajo (alertas) y próximos a vencer
 * - Mensajes no leídos del usuario actual
 *
 * También muestra las últimas 4 consultas y, para administradores,
 * las últimas 15 acciones de la bitácora del sistema.
 *
 * @version 2.6
 * @package App\Http\Controllers
 */

use App\Models\Bitacora;
use App\Models\Cita;
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
            'pacientes'  => Paciente::count(),
            'consultas'  => Consulta::hoy()->count(),
            'inventario' => Medicamento::stockBajo()->count(),
            'por_vencer' => Medicamento::whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<=', now()->addDays(30)->toDateString())->count(),
            'mensajes'   => $usuario->mensajesNoLeidos(),
            'citas_hoy'  => class_exists(\App\Models\Cita::class) ? \App\Models\Cita::hoy()->whereNotIn('estado', ['cancelada'])->count() : 0,
        ];

        // ── Consultas recientes ───────────────────────────────────────────────
        $consultasRecientes = Consulta::with(['paciente', 'empleado', 'medico'])
            ->latest()
            ->limit(4)
            ->get();

        // ── Citas de hoy ─────────────────────────────────────────────────────
        $citasHoy = \App\Models\Cita::with(['paciente', 'empleado', 'medico'])
            ->hoy()
            ->whereNotIn('estado', ['cancelada'])
            ->orderBy('fecha_hora')
            ->limit(5)
            ->get();

        // ── Alertas de Farmacia (stock bajo o próximos a vencer ≤ 60 días) ───
        $alertasFarmacia = Medicamento::where(function ($q) {
                $q->whereColumn('cantidad', '<=', 'stock_minimo')
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('fecha_vencimiento')
                         ->where('fecha_vencimiento', '<=', now()->addDays(60)->toDateString());
                  });
            })
            ->orderBy('cantidad')
            ->limit(6)
            ->get();

        // ── Gráfica: consultas últimos 7 días ────────────────────────────────
        $consultasSemana = Consulta::select(
                DB::raw('DATE(fecha_hora) as fecha'),
                DB::raw('COUNT(*) as total')
            )
            ->where('fecha_hora', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->keyBy('fecha');

        $labels7dias = [];
        $data7dias   = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha       = now()->subDays($i)->format('Y-m-d');
            $labels7dias[] = now()->subDays($i)->format('d/m');
            $data7dias[]   = $consultasSemana->get($fecha)?->total ?? 0;
        }

        // ── Bitácora (solo admin) ─────────────────────────────────────────────
        $bitacora = null;
        if ($usuario->isAdmin()) {
            $bitacora = Bitacora::with('usuario')->latest()->limit(15)->get();
        }

        // Pasar todas las variables a la vista del dashboard
        return view('dashboard.index', compact(
            'stats', 'consultasRecientes', 'citasHoy', 'alertasFarmacia',
            'bitacora', 'labels7dias', 'data7dias'
        ));
    }
}
