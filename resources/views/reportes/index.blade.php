@extends('layouts.app')

@section('title', 'Reportes Estadísticos')
@section('nav-title', 'Reportes')

@push('styles')
<style>
.rep-card { background: var(--bg-card); border-radius: 14px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,.07); }
.rep-card h3 { font-size: 14px; font-weight: 700; color: var(--text-primary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.rep-card h3 i { font-size: 18px; color: var(--primary); }
.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-bottom: 24px; }
.kpi { background: var(--bg-card); border-radius: 12px; padding: 18px; text-align: center; box-shadow: 0 2px 8px rgba(0,0,0,.06); border-top: 3px solid var(--primary); }
.kpi.green  { border-color: #198754; }
.kpi.orange { border-color: #fd7e14; }
.kpi.red    { border-color: #dc3545; }
.kpi.purple { border-color: #6f42c1; }
.kpi .num { font-size: 26px; font-weight: 800; color: var(--text-primary); }
.kpi .lbl { font-size: 11px; color: var(--text-muted); margin-top: 3px; }
.charts-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
.charts-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 24px; }
canvas { max-height: 260px; }
@media (max-width: 900px) {
    .charts-grid, .charts-grid-3 { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="head-title">
    <div class="left">
        <h1>Reportes Estadísticos</h1>
        <ul class="breadcrumb">
            <li><a href="{{ route('dashboard') }}">Inicio</a></li>
            <li><i class='bx bx-chevron-right'></i></li>
            <li><a class="active" href="#">Reportes</a></li>
        </ul>
    </div>
    <div class="head-actions">
        <form method="GET" style="display:flex;gap:8px;align-items:center;">
            <select name="año" class="form-control" style="max-width:100px;" onchange="this.form.submit()">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ $año == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="mes" class="form-control" style="max-width:130px;" onchange="this.form.submit()">
                @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $m)
                    <option value="{{ $i+1 }}" {{ $mes == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- KPIs principales --}}
<div class="kpi-grid">
    <div class="kpi">
        <div class="num">{{ number_format($resumen['total_pacientes']) }}</div>
        <div class="lbl">Pacientes Totales</div>
    </div>
    <div class="kpi green">
        <div class="num">{{ $resumen['pacientes_mes'] }}</div>
        <div class="lbl">Nuevos este mes</div>
    </div>
    <div class="kpi">
        <div class="num">{{ $resumen['consultas_mes'] }}</div>
        <div class="lbl">Consultas este mes</div>
    </div>
    <div class="kpi orange">
        <div class="num">{{ $resumen['recetas_mes'] }}</div>
        <div class="lbl">Recetas este mes</div>
    </div>
    <div class="kpi green">
        <div class="num">{{ $resumen['total_entradas'] }}</div>
        <div class="lbl">Unidades ingresadas</div>
    </div>
    <div class="kpi {{ $resumen['total_salidas'] > 0 ? 'orange' : 'green' }}">
        <div class="num">{{ $resumen['total_salidas'] }}</div>
        <div class="lbl">Unidades dispensadas</div>
    </div>
    <div class="kpi {{ $resumen['stock_bajo'] > 0 ? 'red' : 'green' }}">
        <div class="num">{{ $resumen['stock_bajo'] }}</div>
        <div class="lbl">Alertas de stock</div>
    </div>
</div>

{{-- Gráficas principales --}}
<div class="charts-grid">
    <div class="rep-card">
        <h3><i class='bx bx-line-chart'></i> Consultas por Mes (últimos 12 meses)</h3>
        <canvas id="chart-consultas-mes"></canvas>
    </div>
    <div class="rep-card">
        <h3><i class='bx bx-transfer'></i> Movimientos de Inventario (últimos 12 meses)</h3>
        <canvas id="chart-movimientos"></canvas>
    </div>
</div>

<div class="charts-grid-3">
    <div class="rep-card">
        <h3><i class='bx bx-pie-chart-alt'></i> Estados de Consultas</h3>
        <canvas id="chart-estados"></canvas>
    </div>
    <div class="rep-card">
        <h3><i class='bx bx-male-female'></i> Distribución por Género</h3>
        <canvas id="chart-generos"></canvas>
    </div>
    <div class="rep-card">
        <h3><i class='bx bxs-capsule'></i> Top 5 Medicamentos</h3>
        <canvas id="chart-medicamentos"></canvas>
    </div>
</div>

{{-- Pacientes nuevos por mes --}}
<div class="rep-card" style="margin-bottom:24px;">
    <h3><i class='bx bx-user-plus'></i> Pacientes Nuevos por Mes</h3>
    <canvas id="chart-pacientes-mes" style="max-height:200px;"></canvas>
</div>

@endsection

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
// Paleta de colores adaptada al modo oscuro
const isDark = document.body.classList.contains('dark-mode') || localStorage.getItem('darkMode') === 'true';
const gridColor  = isDark ? 'rgba(255,255,255,.08)' : 'rgba(0,0,0,.06)';
const textColor  = isDark ? '#94a3b8' : '#6c757d';
const COLORS = ['#0d6efd','#198754','#ffc107','#dc3545','#6f42c1','#0dcaf0','#fd7e14'];

Chart.defaults.color = textColor;
Chart.defaults.borderColor = gridColor;

// ── Consultas por mes ─────────────────────────────────────────────────────────
const consultasMesData = @json($consultasPorMes);
new Chart(document.getElementById('chart-consultas-mes'), {
    type: 'bar',
    data: {
        labels: consultasMesData.map(d => d.label),
        datasets: [{
            label: 'Consultas',
            data: consultasMesData.map(d => d.total),
            backgroundColor: 'rgba(13,110,253,.7)',
            borderColor: '#0d6efd',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: gridColor } },
            x: { grid: { display: false } }
        }
    }
});

// ── Movimientos de inventario ─────────────────────────────────────────────────
const movData = @json($movimientosGrafica);
new Chart(document.getElementById('chart-movimientos'), {
    type: 'bar',
    data: {
        labels: movData.labels,
        datasets: [
            {
                label: 'Entradas',
                data: movData.entradas,
                backgroundColor: 'rgba(25,135,84,.7)',
                borderColor: '#198754',
                borderWidth: 1,
                borderRadius: 4,
            },
            {
                label: 'Salidas (Dispensados)',
                data: movData.salidas,
                backgroundColor: 'rgba(220,53,69,.7)',
                borderColor: '#dc3545',
                borderWidth: 1,
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: gridColor } },
            x: { grid: { display: false } }
        }
    }
});

// ── Estados de consultas ──────────────────────────────────────────────────────
const estadosData = @json($estadosConsultas);
new Chart(document.getElementById('chart-estados'), {
    type: 'doughnut',
    data: {
        labels: estadosData.map(d => d.estado),
        datasets: [{
            data: estadosData.map(d => d.total),
            backgroundColor: COLORS,
            borderWidth: 2,
            borderColor: isDark ? '#1e293b' : '#fff',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 12, font: { size: 11 } } }
        }
    }
});

// ── Géneros ───────────────────────────────────────────────────────────────────
const generosData = @json($generosPacientes);
new Chart(document.getElementById('chart-generos'), {
    type: 'pie',
    data: {
        labels: generosData.map(d => d.genero),
        datasets: [{
            data: generosData.map(d => d.total),
            backgroundColor: ['#0d6efd','#e91e8c','#6f42c1'],
            borderWidth: 2,
            borderColor: isDark ? '#1e293b' : '#fff',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 12, font: { size: 11 } } }
        }
    }
});

// ── Top medicamentos ──────────────────────────────────────────────────────────
const medData = @json($topMedicamentos);
new Chart(document.getElementById('chart-medicamentos'), {
    type: 'bar',
    data: {
        labels: medData.map(d => d.nombre_medicamento.length > 18 ? d.nombre_medicamento.substring(0,18)+'…' : d.nombre_medicamento),
        datasets: [{
            label: 'Veces recetado',
            data: medData.map(d => d.veces_recetado),
            backgroundColor: COLORS.slice(0, medData.length),
            borderRadius: 6,
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: gridColor } },
            y: { grid: { display: false } }
        }
    }
});

// ── Pacientes por mes ─────────────────────────────────────────────────────────
const pacMesData = @json($pacientesPorMes);
new Chart(document.getElementById('chart-pacientes-mes'), {
    type: 'line',
    data: {
        labels: pacMesData.map(d => d.label),
        datasets: [{
            label: 'Pacientes nuevos',
            data: pacMesData.map(d => d.total),
            borderColor: '#6f42c1',
            backgroundColor: 'rgba(111,66,193,.12)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#6f42c1',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: gridColor } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush
