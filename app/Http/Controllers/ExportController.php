<?php

namespace App\Http\Controllers;

/**
 * Controlador de Exportaciones
 * 
 * Genera reportes en formato PDF (mPDF), Excel (PhpSpreadsheet) y CSV
 * para los módulos de: Pacientes, Consultas, Medicamentos, Movimientos y Nómina.
 * 
 * @version 2.5
 * @package App\Http\Controllers
 */

use App\Models\Consulta;
use App\Models\InventarioMovimiento;
use App\Models\Medicamento;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    // ─── Nómina ───────────────────────────────────────────────────────────────

    public function exportarNomina(Request $request)
    {
        $formato  = $request->get('formato', 'pdf');
        $empleados = \App\Models\Empleado::orderBy('nombre')->get();
        $titulo   = 'Nómina del Personal';

        return match ($formato) {
            'pdf'   => $this->nominaPDF($empleados, $titulo),
            'excel' => $this->nominaExcel($empleados, $titulo),
            'csv'   => $this->nominaCSV($empleados),
            default => response()->json(['success' => false, 'message' => 'Formato no válido.'], 400),
        };
    }

    private function nominaPDF($empleados, string $titulo)
    {
        try {
            $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'margin_left' => 12, 'margin_right' => 12, 'margin_top' => 15, 'margin_bottom' => 15, 'tempDir' => storage_path('app/mpdf')]);
            $html = view('exports.nomina-pdf', compact('empleados', 'titulo'))->render();
            $mpdf->WriteHTML($html);
            $filename = 'Nomina_' . now()->format('Y-m-d') . '.pdf';
            return response($mpdf->Output('', 'S'), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => "attachment; filename=\"{$filename}\""]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error PDF: ' . $e->getMessage()], 500);
        }
    }

    private function nominaExcel($empleados, string $titulo)
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet()->setTitle('Nomina');

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', $titulo);
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF6F42C1');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $headers = ['#', 'Nombre Completo', 'Cédula', 'Cargo', 'Departamento', 'Salario', 'Fecha Ingreso', 'Estado'];
        foreach (range('A', 'H') as $i => $col) {
            $sheet->setCellValue("{$col}3", $headers[$i]);
            $sheet->getStyle("{$col}3")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("{$col}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF6F42C1');
            $sheet->getStyle("{$col}3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $row = 4;
        foreach ($empleados as $i => $e) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $e->nombre_completo);
            $sheet->setCellValue("C{$row}", $e->cedula);
            $sheet->setCellValue("D{$row}", $e->cargo);
            $sheet->setCellValue("E{$row}", $e->departamento);
            $sheet->setCellValue("F{$row}", 'RD$ ' . number_format($e->salario, 2));
            $sheet->setCellValue("G{$row}", $e->fecha_ingreso?->format('d/m/Y') ?? '-');
            $sheet->setCellValue("H{$row}", ucfirst($e->estado));
            if ($e->estado === 'inactivo') {
                $sheet->getStyle("H{$row}")->getFont()->getColor()->setARGB('FF6C757D');
            } else {
                $sheet->getStyle("H{$row}")->getFont()->getColor()->setARGB('FF198754');
            }
            if (($i + 1) % 2 === 0) {
                $sheet->getStyle("A{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8F9FA');
            }
            $row++;
        }

        foreach (['A' => 5, 'B' => 28, 'C' => 16, 'D' => 22, 'E' => 18, 'F' => 14, 'G' => 14, 'H' => 12] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        $sheet->getStyle("A3:H" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $filename = 'Nomina_' . now()->format('Y-m-d') . '.xlsx';
        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    private function nominaCSV($empleados)
    {
        $filename = 'Nomina_' . now()->format('Y-m-d') . '.csv';
        return response()->streamDownload(function () use ($empleados) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['ID', 'Nombre', 'Apellido', 'Cédula', 'Cargo', 'Departamento', 'Salario', 'Fecha Ingreso', 'Teléfono', 'Email', 'Estado']);
            foreach ($empleados as $e) {
                fputcsv($out, [
                    $e->id, $e->nombre, $e->apellido, $e->cedula,
                    $e->cargo, $e->departamento,
                    number_format($e->salario, 2, '.', ''),
                    $e->fecha_ingreso?->format('d/m/Y') ?? '',
                    $e->telefono ?? '', $e->email ?? '',
                    ucfirst($e->estado),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    // ─── Movimientos ──────────────────────────────────────────────────────────

    public function exportarMovimientos(Request $request)
    {
        $formato     = $request->get('formato', 'pdf');
        $periodo     = $request->get('periodo', 'semanal');
        $tipo        = $request->get('tipo') ?? '';
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin    = $request->get('fecha_fin');

        [$movimientos, $periodoLabel] = $this->getMovimientos($periodo, $tipo, $fechaInicio, $fechaFin);

        // Calcular totales para las tarjetas
        $totalEntradas = $movimientos->where('tipo_movimiento', 'entrada')->sum('cantidad');
        $totalSalidas  = $movimientos->where('tipo_movimiento', 'salida')->sum('cantidad');

        return match ($formato) {
            'pdf'   => $this->movimientosPDF($movimientos, $periodoLabel, $totalEntradas, $totalSalidas),
            'excel' => $this->movimientosExcel($movimientos, $periodoLabel),
            'csv'   => $this->movimientosCSV($movimientos),
            default => response()->json(['success' => false, 'message' => 'Formato no valido.'], 400),
        };
    }

    private function getMovimientos(string $periodo, ?string $tipo = null, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $query = InventarioMovimiento::with(['medicamento', 'usuario']);

        switch ($periodo) {
            case 'semanal':
                $query->byDateRange(now()->subDays(7)->format('Y-m-d'), now()->format('Y-m-d'));
                $label = 'Ultima Semana';
                break;
            case 'mensual':
                $query->byDateRange(now()->subDays(30)->format('Y-m-d'), now()->format('Y-m-d'));
                $label = 'Ultimo Mes';
                break;
            case 'rango':
                $fi = $fechaInicio ?? now()->subDays(30)->format('Y-m-d');
                $ff = $fechaFin    ?? now()->format('Y-m-d');
                $query->byDateRange($fi, $ff);
                $label = "Del {$fi} al {$ff}";
                break;
            default:
                $label = 'Todos los registros';
        }

        if (!empty($tipo)) {
            $query->where('tipo_movimiento', $tipo);
        }

        return [$query->latest()->get(), $label];
    }

    private function movimientosPDF($movimientos, string $periodoLabel, $totalEntradas, $totalSalidas)
    {
        try {
            $mpdf = new Mpdf([
                'mode'          => 'utf-8',
                'format'        => 'A4-L',
                'margin_left'   => 12,
                'margin_right'  => 12,
                'margin_top'    => 15,
                'margin_bottom' => 15,
                'tempDir'       => storage_path('app/mpdf'),
            ]);

            // Forzar que los enteros sean int
            $totalEntradas = (int) $totalEntradas;
            $totalSalidas  = (int) $totalSalidas;

            $html = view('exports.movimientos-pdf', compact(
                'movimientos', 'periodoLabel', 'totalEntradas', 'totalSalidas'
            ))->render();

            $mpdf->WriteHTML($html);

            $filename = 'Movimientos_' . now()->format('Y-m-d_H-i-s') . '.pdf';
            $content  = $mpdf->Output('', 'S');

            return response($content, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error PDF: ' . $e->getMessage()], 500);
        }
    }

    // ─── Consultas ────────────────────────────────────────────────────────────

    private function movimientosExcel($movimientos, string $periodoLabel)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Movimientos');

        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', "Movimientos de Inventario - {$periodoLabel}");
        $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF667EEA');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $headers = ['#', 'Fecha/Hora', 'Medicamento', 'Tipo', 'Cantidad', 'Stock Ant.', 'Stock Nuevo', 'Paciente/Motivo', 'Usuario'];
        foreach (range('A', 'I') as $i => $col) {
            $sheet->setCellValue("{$col}3", $headers[$i]);
            $sheet->getStyle("{$col}3")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("{$col}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF667EEA');
        }

        $row = 4;
        foreach ($movimientos as $i => $m) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $m->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue("C{$row}", $m->medicamento->nombre_medicamento);
            $sheet->setCellValue("D{$row}", ucfirst($m->tipo_movimiento));
            $sheet->setCellValue("E{$row}", ($m->tipo_movimiento === 'entrada' ? '+' : '-') . $m->cantidad);
            $sheet->setCellValue("F{$row}", $m->cantidad_anterior);
            $sheet->setCellValue("G{$row}", $m->cantidad_nueva);
            $sheet->setCellValue("H{$row}", $m->paciente ? $m->paciente->nombre_completo : ($m->motivo ?? '-'));
            $sheet->setCellValue("I{$row}", $m->usuario->nombre_completo);
            if ($m->tipo_movimiento === 'entrada') {
                $sheet->getStyle("E{$row}")->getFont()->getColor()->setARGB('FF198754');
            } else {
                $sheet->getStyle("E{$row}")->getFont()->getColor()->setARGB('FFDC3545');
            }
            if (($i + 1) % 2 === 0) {
                $sheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8F9FA');
            }
            $row++;
        }

        foreach (['A'=>5,'B'=>18,'C'=>25,'D'=>10,'E'=>10,'F'=>10,'G'=>10,'H'=>22,'I'=>20] as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }
        $sheet->getStyle("A3:I" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $filename = 'Movimientos_' . now()->format('Y-m-d') . '.xlsx';
        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    private function movimientosCSV($movimientos)
    {
        $filename = 'Movimientos_' . now()->format('Y-m-d') . '.csv';
        return response()->streamDownload(function () use ($movimientos) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['ID', 'Fecha/Hora', 'Medicamento', 'Tipo', 'Cantidad', 'Stock Anterior', 'Stock Nuevo', 'Paciente', 'Motivo', 'Usuario']);
            foreach ($movimientos as $m) {
                fputcsv($out, [
                    $m->id,
                    $m->created_at->format('d/m/Y H:i'),
                    $m->medicamento->nombre_medicamento,
                    ucfirst($m->tipo_movimiento),
                    $m->cantidad,
                    $m->cantidad_anterior,
                    $m->cantidad_nueva,
                    $m->paciente ? $m->paciente->nombre_completo : '',
                    $m->motivo ?? '',
                    $m->usuario->nombre_completo,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    // ─── Consultas ────────────────────────────────────────────────────────────

    public function exportarConsultas(Request $request)
    {
        $formato     = $request->get('formato', 'pdf');
        $periodo     = $request->get('periodo', 'todos');
        $fechaInicio = $request->get('fecha_inicio', now()->subDays(30)->format('Y-m-d'));
        $fechaFin    = $request->get('fecha_fin', now()->format('Y-m-d'));

        [$consultas, $titulo] = $this->getConsultas($periodo, $fechaInicio, $fechaFin);

        return match ($formato) {
            'pdf'   => $this->consultasPDF($consultas, $titulo),
            'excel' => $this->consultasExcel($consultas, $titulo),
            'csv'   => $this->consultasCSV($consultas),
            default => response()->json(['success' => false, 'message' => 'Formato no válido.'], 400),
        };
    }

    public function exportarPacientes(Request $request)
    {
        $formato = $request->get('formato', 'pdf');
        $periodo = $request->get('periodo', 'todos');

        [$pacientes, $titulo] = $this->getPacientes($periodo);

        return match ($formato) {
            'pdf'   => $this->pacientesPDF($pacientes, $titulo),
            'excel' => $this->pacientesExcel($pacientes, $titulo),
            'csv'   => $this->pacientesCSV($pacientes),
            default => response()->json(['success' => false, 'message' => 'Formato no válido.'], 400),
        };
    }

    // ─── Consultas helpers ────────────────────────────────────────────────────

    private function getConsultas(string $periodo, string $fechaInicio, string $fechaFin): array
    {
        $query = Consulta::with(['paciente', 'empleado', 'medico']);

        return match ($periodo) {
            'semanal' => [$query->byDateRange(now()->subDays(7)->format('Y-m-d'), now()->format('Y-m-d'))->latest('fecha_hora')->get(), 'Consultas de la Última Semana'],
            'mensual' => [$query->byDateRange(now()->subDays(30)->format('Y-m-d'), now()->format('Y-m-d'))->latest('fecha_hora')->get(), 'Consultas del Último Mes'],
            'rango'   => [$query->byDateRange($fechaInicio, $fechaFin)->latest('fecha_hora')->get(), "Consultas del {$fechaInicio} al {$fechaFin}"],
            default   => [$query->latest('fecha_hora')->get(), 'Todas las Consultas'],
        };
    }

    private function consultasPDF($consultas, string $titulo)
    {
        try {
            $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'margin_left' => 15, 'margin_right' => 15, 'margin_top' => 20, 'margin_bottom' => 20, 'tempDir' => storage_path('app/mpdf')]);
            $html = view('exports.consultas-pdf', compact('consultas', 'titulo'))->render();
            $mpdf->WriteHTML($html);
            $filename = 'Consultas_' . now()->format('Y-m-d') . '.pdf';
            return response($mpdf->Output('', 'S'), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => "attachment; filename=\"{$filename}\""]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error PDF: ' . $e->getMessage()], 500);
        }
    }

    private function consultasExcel($consultas, string $titulo)
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet()->setTitle('Consultas');

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', $titulo);
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF667EEA');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $headers = ['#', 'Paciente', 'Cédula', 'Médico', 'Fecha/Hora', 'Motivo', 'Estado'];
        foreach (range('A', 'G') as $i => $col) {
            $sheet->setCellValue("{$col}3", $headers[$i]);
            $sheet->getStyle("{$col}3")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("{$col}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF667EEA');
        }

        $row = 4;
        foreach ($consultas as $i => $c) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $c->paciente->nombre_completo);
            $sheet->setCellValue("C{$row}", $c->paciente->cedula);
            $sheet->setCellValue("D{$row}", $c->medico_nombre);
            $sheet->setCellValue("E{$row}", $c->fecha_hora->format('d/m/Y H:i'));
            $sheet->setCellValue("F{$row}", substr($c->motivo, 0, 50));
            $sheet->setCellValue("G{$row}", $c->estado_texto);
            if (($i + 1) % 2 === 0) {
                $sheet->getStyle("A{$row}:G{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8F9FA');
            }
            $row++;
        }

        foreach (['A' => 5, 'B' => 22, 'C' => 15, 'D' => 22, 'E' => 18, 'F' => 30, 'G' => 15] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        $sheet->getStyle("A3:G" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $filename = 'Consultas_' . now()->format('Y-m-d') . '.xlsx';
        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    private function consultasCSV($consultas)
    {
        $filename = 'Consultas_' . now()->format('Y-m-d') . '.csv';
        return response()->streamDownload(function () use ($consultas) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['ID', 'Paciente', 'Cédula', 'Médico', 'Fecha/Hora', 'Motivo', 'Diagnóstico', 'Tratamiento', 'Estado', 'Fecha Registro']);
            foreach ($consultas as $c) {
                fputcsv($out, [$c->id, $c->paciente->nombre_completo, $c->paciente->cedula, $c->medico_nombre, $c->fecha_hora->format('d/m/Y H:i'), $c->motivo, $c->diagnostico ?? '', $c->tratamiento ?? '', $c->estado_texto, $c->created_at->format('d/m/Y H:i')]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    // ─── Pacientes helpers ────────────────────────────────────────────────────

    private function getPacientes(string $periodo): array
    {
        return match ($periodo) {
            'semanal' => [Paciente::whereBetween(\DB::raw('DATE(created_at)'), [now()->subDays(7)->format('Y-m-d'), now()->format('Y-m-d')])->get(), 'Pacientes Registrados en la Última Semana'],
            'mensual' => [Paciente::whereBetween(\DB::raw('DATE(created_at)'), [now()->subDays(30)->format('Y-m-d'), now()->format('Y-m-d')])->get(), 'Pacientes Registrados en el Último Mes'],
            default   => [Paciente::all(), 'Todos los Pacientes'],
        };
    }

    private function pacientesPDF($pacientes, string $titulo)
    {
        try {
            $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'margin_left' => 15, 'margin_right' => 15, 'margin_top' => 20, 'margin_bottom' => 20, 'tempDir' => storage_path('app/mpdf')]);
            $html = view('exports.pacientes-pdf', compact('pacientes', 'titulo'))->render();
            $mpdf->WriteHTML($html);
            $filename = 'Pacientes_' . now()->format('Y-m-d') . '.pdf';
            $content  = $mpdf->Output('', 'S');
            return response($content, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error PDF: ' . $e->getMessage()], 500);
        }
    }

    private function pacientesExcel($pacientes, string $titulo)
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet()->setTitle('Pacientes');

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', $titulo);
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF667EEA');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $headers = ['#', 'Nombre', 'Cédula', 'Teléfono', 'Email', 'Tipo Sangre', 'Fecha Registro'];
        foreach (range('A', 'G') as $i => $col) {
            $sheet->setCellValue("{$col}3", $headers[$i]);
            $sheet->getStyle("{$col}3")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("{$col}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF667EEA');
        }

        $row = 4;
        foreach ($pacientes as $i => $p) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $p->nombre_completo);
            $sheet->setCellValue("C{$row}", $p->cedula);
            $sheet->setCellValue("D{$row}", $p->telefono ?? '-');
            $sheet->setCellValue("E{$row}", $p->email ?? '-');
            $sheet->setCellValue("F{$row}", $p->tipo_sangre ?? '-');
            $sheet->setCellValue("G{$row}", $p->created_at->format('d/m/Y'));
            if (($i + 1) % 2 === 0) {
                $sheet->getStyle("A{$row}:G{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8F9FA');
            }
            $row++;
        }

        $sheet->getStyle("A3:G" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $filename = 'Pacientes_' . now()->format('Y-m-d') . '.xlsx';
        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    private function pacientesCSV($pacientes)
    {
        $filename = 'Pacientes_' . now()->format('Y-m-d') . '.csv';
        return response()->streamDownload(function () use ($pacientes) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['ID', 'Nombre', 'Apellido', 'Cédula', 'Teléfono', 'Email', 'Fecha Nacimiento', 'Género', 'Tipo Sangre', 'Alergias', 'Fecha Registro']);
            foreach ($pacientes as $p) {
                fputcsv($out, [$p->id, $p->nombre, $p->apellido, $p->cedula, $p->telefono ?? '', $p->email ?? '', $p->fecha_nacimiento?->format('d/m/Y') ?? '', $p->genero_texto, $p->tipo_sangre ?? '', $p->alergias ?? '', $p->created_at->format('d/m/Y')]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    // ─── Medicamentos ─────────────────────────────────────────────────────────

    public function exportarMedicamentos(Request $request)
    {
        $formato = $request->get('formato', 'pdf');
        $medicamentos = Medicamento::orderBy('nombre_medicamento')->get();
        $titulo = 'Inventario de Medicamentos';

        return match ($formato) {
            'pdf'   => $this->medicamentosPDF($medicamentos, $titulo),
            'excel' => $this->medicamentosExcel($medicamentos, $titulo),
            'csv'   => $this->medicamentosCSV($medicamentos),
            default => response()->json(['success' => false, 'message' => 'Formato no válido.'], 400),
        };
    }

    private function medicamentosPDF($medicamentos, string $titulo)
    {
        try {
            $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4-L', 'margin_left' => 12, 'margin_right' => 12, 'margin_top' => 15, 'margin_bottom' => 15, 'tempDir' => storage_path('app/mpdf')]);
            $html = view('exports.medicamentos-pdf', compact('medicamentos', 'titulo'))->render();
            $mpdf->WriteHTML($html);
            $filename = 'Medicamentos_' . now()->format('Y-m-d') . '.pdf';
            return response($mpdf->Output('', 'S'), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => "attachment; filename=\"{$filename}\""]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error PDF: ' . $e->getMessage()], 500);
        }
    }

    private function medicamentosExcel($medicamentos, string $titulo)
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet()->setTitle('Medicamentos');

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', $titulo);
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF198754');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $headers = ['#', 'Medicamento', 'Cantidad', 'Unidad', 'Precio', 'Stock Mín.', 'Vencimiento', 'Proveedor'];
        foreach (range('A', 'H') as $i => $col) {
            $sheet->setCellValue("{$col}3", $headers[$i]);
            $sheet->getStyle("{$col}3")->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
            $sheet->getStyle("{$col}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF198754');
            $sheet->getStyle("{$col}3")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $row = 4;
        foreach ($medicamentos as $i => $m) {
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $m->nombre_medicamento);
            $sheet->setCellValue("C{$row}", $m->cantidad);
            $sheet->setCellValue("D{$row}", $m->unidad);
            $sheet->setCellValue("E{$row}", '$' . number_format($m->precio, 2));
            $sheet->setCellValue("F{$row}", $m->stock_minimo);
            $sheet->setCellValue("G{$row}", $m->fecha_vencimiento?->format('d/m/Y') ?? '-');
            $sheet->setCellValue("H{$row}", $m->proveedor ?? '-');
            if ($m->tieneStockBajo()) {
                $sheet->getStyle("C{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFDC3545'))->setBold(true);
            }
            if (($i + 1) % 2 === 0) {
                $sheet->getStyle("A{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8F9FA');
            }
            $row++;
        }

        foreach (['A' => 5, 'B' => 28, 'C' => 10, 'D' => 12, 'E' => 10, 'F' => 10, 'G' => 14, 'H' => 22] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }
        $sheet->getStyle("A3:H" . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $filename = 'Medicamentos_' . now()->format('Y-m-d') . '.xlsx';
        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }

    private function medicamentosCSV($medicamentos)
    {
        $filename = 'Medicamentos_' . now()->format('Y-m-d') . '.csv';
        return response()->streamDownload(function () use ($medicamentos) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['ID', 'Medicamento', 'Descripción', 'Cantidad', 'Unidad', 'Precio', 'Stock Mínimo', 'Vencimiento', 'Proveedor', 'Estado Stock']);
            foreach ($medicamentos as $m) {
                fputcsv($out, [
                    $m->id, $m->nombre_medicamento, $m->descripcion ?? '',
                    $m->cantidad, $m->unidad, number_format($m->precio, 2, '.', ''),
                    $m->stock_minimo, $m->fecha_vencimiento?->format('d/m/Y') ?? '',
                    $m->proveedor ?? '', $m->tieneStockBajo() ? 'Stock Bajo' : 'Normal',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=utf-8']);
    }
}
