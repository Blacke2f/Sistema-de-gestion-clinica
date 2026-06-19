<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Consulta;
use App\Models\Empleado;
use App\Models\ExamenLaboratorio;
use App\Models\GastoMedico;
use App\Models\InventarioMovimiento;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\Receta;
use App\Models\RecetaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;

/**
 * Controlador de Reportes Estadísticos — SISGECLIN v3.0
 * Incluye exportación profesional a PDF (mPDF) y Excel (CSV enriquecido).
 */
class ReportesController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════════
    // VISTA PRINCIPAL (dashboard de reportes)
    // ══════════════════════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        $año = $request->get('año', now()->year);
        $mes = $request->get('mes', now()->month);

        // Consultas por mes (últimos 12 meses)
        $consultasPorMes = Consulta::select(
                DB::raw('YEAR(fecha_hora) as año'),
                DB::raw('MONTH(fecha_hora) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('fecha_hora', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('año', 'mes')
            ->orderBy('año')->orderBy('mes')
            ->get()
            ->map(fn ($r) => [
                'label' => $this->nombreMes($r->mes) . ' ' . $r->año,
                'total' => $r->total,
            ]);

        // Pacientes registrados por mes
        $pacientesPorMes = Paciente::select(
                DB::raw('YEAR(created_at) as año'),
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('año', 'mes')
            ->orderBy('año')->orderBy('mes')
            ->get()
            ->map(fn ($r) => [
                'label' => $this->nombreMes($r->mes),
                'total' => $r->total,
            ]);

        // Distribución de estados de consultas
        $estadosConsultas = Consulta::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->get()
            ->map(fn ($r) => [
                'estado' => ucfirst(str_replace('_', ' ', $r->estado)),
                'total'  => $r->total,
            ]);

        // Top 5 medicamentos más recetados
        $topMedicamentos = RecetaItem::select(
                'nombre_medicamento',
                DB::raw('SUM(cantidad) as total_cantidad'),
                DB::raw('COUNT(*) as veces_recetado')
            )
            ->groupBy('nombre_medicamento')
            ->orderByDesc('veces_recetado')
            ->limit(5)
            ->get();

        // Movimientos de inventario por mes
        $movimientosPorMes = InventarioMovimiento::select(
                DB::raw('YEAR(created_at) as año'),
                DB::raw('MONTH(created_at) as mes'),
                'tipo_movimiento',
                DB::raw('SUM(cantidad) as total')
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('año', 'mes', 'tipo_movimiento')
            ->orderBy('año')->orderBy('mes')
            ->get();

        // Separar entradas y salidas por mes
        $mesesLabels = []; $entradasData = []; $salidasData = [];
        for ($i = 11; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $label = $this->nombreMes($fecha->month) . ' ' . $fecha->year;
            $mesesLabels[] = $label;
            $entradas = $movimientosPorMes->where('año', $fecha->year)->where('mes', $fecha->month)->where('tipo_movimiento', 'entrada')->first();
            $salidas  = $movimientosPorMes->where('año', $fecha->year)->where('mes', $fecha->month)->where('tipo_movimiento', 'salida')->first();
            $entradasData[] = $entradas ? (int)$entradas->total : 0;
            $salidasData[]  = $salidas  ? (int)$salidas->total  : 0;
        }

        $movimientosGrafica = [
            'labels'   => $mesesLabels,
            'entradas' => $entradasData,
            'salidas'  => $salidasData,
        ];

        // Citas por estado (para gráfica)
        $citasPorEstado = Cita::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->get()
            ->map(fn ($r) => [
                'estado' => ucfirst($r->estado),
                'total'  => $r->total,
            ]);

        // Resumen general del mes seleccionado
        $resumen = [
            'total_pacientes' => Paciente::count(),
            'pacientes_mes'   => Paciente::whereMonth('created_at', $mes)->whereYear('created_at', $año)->count(),
            'consultas_mes'   => Consulta::whereMonth('fecha_hora', $mes)->whereYear('fecha_hora', $año)->count(),
            'consultas_hoy'   => Consulta::whereDate('fecha_hora', today())->count(),
            'stock_bajo'      => Medicamento::stockBajo()->count(),
            'recetas_mes'     => Receta::whereMonth('created_at', $mes)->whereYear('created_at', $año)->count(),
            'total_entradas'  => InventarioMovimiento::whereMonth('created_at', $mes)->whereYear('created_at', $año)->where('tipo_movimiento','entrada')->sum('cantidad'),
            'total_salidas'   => InventarioMovimiento::whereMonth('created_at', $mes)->whereYear('created_at', $año)->where('tipo_movimiento','salida')->sum('cantidad'),
        ];

        // Géneros de pacientes
        $generosPacientes = Paciente::select('genero', DB::raw('COUNT(*) as total'))
            ->groupBy('genero')
            ->get()
            ->map(fn ($r) => [
                'genero' => match($r->genero) { 'M' => 'Masculino', 'F' => 'Femenino', default => 'Otro' },
                'total'  => $r->total,
            ]);

        return view('reportes.index', compact(
            'consultasPorMes', 'pacientesPorMes', 'estadosConsultas',
            'topMedicamentos', 'movimientosGrafica', 'citasPorEstado',
            'resumen', 'generosPacientes', 'año', 'mes'
        ));
    }

    // ══════════════════════════════════════════════════════════════════════════
    // EXPORTAR PDF PROFESIONAL
    // ══════════════════════════════════════════════════════════════════════════
    public function exportarPDF(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin    = $request->get('fecha_fin',    now()->toDateString());

        // Gráficas recibidas como base64 desde el frontend
        $graficaConsultas = $request->get('grafica_consultas', '');
        $graficaCitas     = $request->get('grafica_citas', '');
        $graficaGastos    = $request->get('grafica_gastos', '');

        // ── Consultar todos los módulos por rango de fechas ──────────────────
        $pacientes   = Paciente::whereBetween('created_at', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])->orderBy('created_at')->get();
        $consultas   = Consulta::with('paciente')->whereBetween('fecha_hora', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])->orderBy('fecha_hora')->get();
        $citas       = Cita::with('paciente')->whereBetween(\DB::raw('DATE(fecha_hora)'), [$fechaInicio, $fechaFin])->orderBy('fecha_hora')->get();
        $recetas     = Receta::with('paciente')->whereBetween('created_at', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])->orderBy('created_at')->get();
        $examenes    = ExamenLaboratorio::with('paciente')->whereBetween('created_at', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])->orderBy('created_at')->get();
        $gastos      = GastoMedico::with('paciente')->whereBetween('created_at', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])->orderBy('created_at')->get();
        $movimientos = InventarioMovimiento::with('medicamento')->whereBetween('created_at', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])->orderBy('created_at')->get();
        $nomina      = Empleado::activos()->orderBy('apellido')->get();

        // ── KPIs ─────────────────────────────────────────────────────────────
        $kpis = [
            'total_pacientes' => Paciente::count(),
            'pacientes_nuevos'=> $pacientes->count(),
            'consultas'       => $consultas->count(),
            'citas_total'     => $citas->count(),
            'recetas'         => $recetas->count(),
            'examenes'        => $examenes->count(),
            'gastos_total'    => $gastos->count(),
        ];

        // ── Renderizar la vista Blade como HTML ───────────────────────────────
        $html = view('reportes.pdf', compact(
            'fechaInicio', 'fechaFin', 'kpis',
            'pacientes', 'consultas', 'citas', 'recetas',
            'examenes', 'gastos', 'movimientos', 'nomina',
            'graficaConsultas', 'graficaCitas', 'graficaGastos'
        ))->render();

        // ── Generar PDF con mPDF ──────────────────────────────────────────────
        $storageDir = storage_path('app/mpdf');
        if (!is_dir($storageDir)) mkdir($storageDir, 0755, true);

        $mpdf = new Mpdf([
            'mode'           => 'utf-8',
            'format'         => 'Letter',
            'margin_top'     => 5,
            'margin_bottom'  => 12,
            'margin_left'    => 10,
            'margin_right'   => 10,
            'tempDir'        => $storageDir,
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
        ]);

        $mpdf->SetTitle('Reporte SISGECLIN ' . $fechaInicio . ' al ' . $fechaFin);
        $mpdf->SetAuthor('SISGECLIN — Maxical C.A.');
        $mpdf->SetCreator('SISGECLIN v3.0');
        $mpdf->WriteHTML($html);

        $filename = 'Reporte_SISGECLIN_' . str_replace('-', '', $fechaInicio) . '_' . str_replace('-', '', $fechaFin) . '.pdf';

        return response($mpdf->Output($filename, 'S'), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // EXPORTAR EXCEL (CSV estructurado con múltiples secciones)
    // ══════════════════════════════════════════════════════════════════════════
    public function exportarExcel(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin    = $request->get('fecha_fin',    now()->toDateString());

        $pacientes   = Paciente::whereBetween('created_at', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])->orderBy('created_at')->get();
        $consultas   = Consulta::with('paciente')->whereBetween('fecha_hora', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])->orderBy('fecha_hora')->get();
        $citas       = Cita::with('paciente')->whereBetween(\DB::raw('DATE(fecha_hora)'), [$fechaInicio, $fechaFin])->orderBy('fecha_hora')->get();
        $recetas     = Receta::with('paciente')->whereBetween('created_at', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])->orderBy('created_at')->get();
        $examenes    = ExamenLaboratorio::with('paciente')->whereBetween('created_at', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])->orderBy('created_at')->get();
        $gastos      = GastoMedico::with('paciente')->whereBetween('created_at', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])->orderBy('created_at')->get();
        $movimientos = InventarioMovimiento::with('medicamento')->whereBetween('created_at', [$fechaInicio.' 00:00:00', $fechaFin.' 23:59:59'])->orderBy('created_at')->get();
        $nomina      = Empleado::activos()->orderBy('apellido')->get();

        $rows = [];

        // Encabezado
        $rows[] = ['SISGECLIN v3.0 — Maxical C.A.'];
        $rows[] = ['Reporte General de Actividades'];
        $rows[] = ['Período:', $fechaInicio, 'al', $fechaFin];
        $rows[] = ['Generado:', now()->format('d/m/Y H:i')];
        $rows[] = [];

        // ── PACIENTES
        $rows[] = ['=== PACIENTES ==='];
        $rows[] = ['#','Nombre','Apellido','Cédula','Género','Fecha Nac.','Teléfono','Empresa','Registro'];
        foreach ($pacientes as $i => $p) {
            $rows[] = [$i+1, $p->nombre, $p->apellido, $p->cedula,
                $p->genero === 'M' ? 'Masculino' : 'Femenino',
                $p->fecha_nacimiento ?? '—', $p->telefono ?? '—', $p->empresa ?? '—',
                \Carbon\Carbon::parse($p->created_at)->format('d/m/Y')];
        }
        $rows[] = [];

        // ── CONSULTAS
        $rows[] = ['=== CONSULTAS ==='];
        $rows[] = ['#','Paciente','Fecha','Tipo','Motivo','Estado','Médico'];
        foreach ($consultas as $i => $c) {
            $rows[] = [$i+1,
                (optional($c->paciente)->nombre.' '.optional($c->paciente)->apellido),
                \Carbon\Carbon::parse($c->fecha_hora)->format('d/m/Y'),
                $c->tipo_consulta ?? '—', $c->motivo_consulta ?? '—',
                ucfirst($c->estado ?? '—'), $c->medico ?? '—'];
        }
        $rows[] = [];

        // ── CITAS
        $rows[] = ['=== CITAS ==='];
        $rows[] = ['#','Paciente','Fecha','Hora','Motivo','Estado'];
        foreach ($citas as $i => $c) {
            $rows[] = [$i+1,
                (optional($c->paciente)->nombre.' '.optional($c->paciente)->apellido),
                \Carbon\Carbon::parse($c->fecha_cita)->format('d/m/Y'),
                $c->hora_cita ?? '—', $c->motivo ?? '—', ucfirst($c->estado ?? '—')];
        }
        $rows[] = [];

        // ── RECETAS
        $rows[] = ['=== RECETAS ==='];
        $rows[] = ['#','Paciente','Fecha','Tipo','Diagnóstico','Médico'];
        foreach ($recetas as $i => $r) {
            $rows[] = [$i+1,
                (optional($r->paciente)->nombre.' '.optional($r->paciente)->apellido),
                \Carbon\Carbon::parse($r->created_at)->format('d/m/Y'),
                $r->tipo ?? 'Digital', $r->diagnostico ?? '—', $r->medico ?? '—'];
        }
        $rows[] = [];

        // ── EXÁMENES
        $rows[] = ['=== EXÁMENES DE LABORATORIO ==='];
        $rows[] = ['#','Paciente','Fecha','Tipo Examen','Laboratorio','Estado'];
        foreach ($examenes as $i => $e) {
            $rows[] = [$i+1,
                (optional($e->paciente)->nombre.' '.optional($e->paciente)->apellido),
                \Carbon\Carbon::parse($e->created_at)->format('d/m/Y'),
                $e->tipo_examen ?? '—', $e->laboratorio ?? '—', ucfirst($e->estado ?? 'Pendiente')];
        }
        $rows[] = [];

        // ── GASTOS
        $rows[] = ['=== GASTOS MÉDICOS ==='];
        $rows[] = ['#','Paciente','Fecha','Descripción','Categoría','Monto (Bs.)'];
        $totalGastos = 0;
        foreach ($gastos as $i => $g) {
            $totalGastos += $g->monto_total ?? 0;
            $rows[] = [$i+1,
                (optional($g->paciente)->nombre.' '.optional($g->paciente)->apellido),
                \Carbon\Carbon::parse($g->created_at)->format('d/m/Y'),
                $g->descripcion ?? '—', $g->categoria ?? '—',
                number_format($g->monto_total ?? 0, 2)];
        }
        $rows[] = ['','','','','TOTAL:', number_format($totalGastos, 2)];
        $rows[] = [];

        // ── INVENTARIO
        $rows[] = ['=== MOVIMIENTOS DE INVENTARIO ==='];
        $rows[] = ['#','Medicamento','Tipo','Cantidad','Motivo','Fecha'];
        foreach ($movimientos as $i => $m) {
            $rows[] = [$i+1,
                optional($m->medicamento)->nombre ?? '—',
                ucfirst($m->tipo_movimiento), $m->cantidad,
                $m->motivo ?? '—',
                \Carbon\Carbon::parse($m->created_at)->format('d/m/Y')];
        }
        $rows[] = [];

        // ── NÓMINA
        $rows[] = ['=== NÓMINA (PERSONAL ACTIVO) ==='];
        $rows[] = ['#','Nombre','Apellido','Cédula','Cargo','Especialidad','Teléfono'];
        foreach ($nomina as $i => $e) {
            $rows[] = [$i+1, $e->nombre, $e->apellido, $e->cedula ?? '—',
                $e->cargo ?? '—', $e->especialidad ?? '—', $e->telefono ?? '—'];
        }

        // ── Generar CSV
        $filename = 'Reporte_SISGECLIN_' . str_replace('-', '', $fechaInicio) . '_' . str_replace('-', '', $fechaFin) . '.csv';

        $output = fopen('php://temp', 'r+');
        // BOM para UTF-8 en Excel
        fwrite($output, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($output, $row, ';');
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════════════
    // DATOS AJAX
    // ══════════════════════════════════════════════════════════════════════════
    public function datos(Request $request)
    {
        $tipo = $request->get('tipo', 'consultas_mes');
        $data = match ($tipo) {
            'consultas_semana' => $this->consultasPorDia(7),
            'consultas_mes'    => $this->consultasPorDia(30),
            default            => [],
        };
        return response()->json(['success' => true, 'data' => $data]);
    }

    private function consultasPorDia(int $dias): array
    {
        return Consulta::select(DB::raw('DATE(fecha_hora) as fecha'), DB::raw('COUNT(*) as total'))
            ->where('fecha_hora', '>=', now()->subDays($dias))
            ->groupBy('fecha')->orderBy('fecha')
            ->get()
            ->map(fn ($r) => ['fecha' => $r->fecha, 'total' => $r->total])
            ->toArray();
    }

    private function nombreMes(int $mes): string
    {
        return ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
                'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'][$mes] ?? '';
    }
}
