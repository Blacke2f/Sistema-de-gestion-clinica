{{--
    Modal de exportación profesional reutilizable.
    Parámetros:
      $exportModule  — nombre del módulo (ej: 'pacientes', 'consultas', 'nomina')
      $exportTitle   — título del modal (ej: 'Exportar Pacientes')
      $exportColor   — color del header (ej: '#0d6efd', '#198754', '#6f42c1')
      $exportIcon    — clase boxicon (ej: 'bxs-user-detail', 'bx-clipboard')
      $exportRoutes  — array con claves 'pdf', 'excel', 'csv' (URLs)
      $exportHasPeriod — bool, si muestra selector de período (default false)
--}}
@php
    $exportColor    = $exportColor    ?? '#0d6efd';
    $exportIcon     = $exportIcon     ?? 'bx-export';
    $exportHasPeriod = $exportHasPeriod ?? false;
    $modalId        = 'modal-export-' . ($exportModule ?? 'default');
@endphp

<div class="modal" id="{{ $modalId }}">
    <div class="modal-content" style="max-width:480px;">
        <div class="modal-header" style="background:{{ $exportColor }};color:#fff;">
            <h2><i class='bx {{ $exportIcon }}'></i> {{ $exportTitle ?? 'Exportar' }}</h2>
            <button class="modal-close" onclick="closeModal('{{ $modalId }}')" style="color:#fff;">&times;</button>
        </div>
        <div style="padding:24px;">

            @if($exportHasPeriod)
            {{-- Selector de período --}}
            <div class="form-group" style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;color:var(--text-primary);margin-bottom:6px;display:block;">
                    <i class='bx bx-calendar'></i> Período
                </label>
                <select id="exp-{{ $exportModule }}-periodo" class="form-control" onchange="toggleFechasExportModal('{{ $exportModule }}')">
                    <option value="todos">Todos los registros</option>
                    <option value="mensual">Último mes</option>
                    <option value="semanal">Última semana</option>
                    <option value="rango">Rango de fechas</option>
                </select>
            </div>
            <div id="exp-{{ $exportModule }}-fechas" style="display:none;margin-bottom:16px;">
                <div class="form-row">
                    <div class="form-group">
                        <label style="font-size:12px;font-weight:600;color:var(--text-secondary);margin-bottom:4px;display:block;">Fecha inicio</label>
                        <input type="date" id="exp-{{ $exportModule }}-fi" style="width:100%;padding:8px 10px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13px;background:var(--bg-card);color:var(--text-primary);">
                    </div>
                    <div class="form-group">
                        <label style="font-size:12px;font-weight:600;color:var(--text-secondary);margin-bottom:4px;display:block;">Fecha fin</label>
                        <input type="date" id="exp-{{ $exportModule }}-ff" style="width:100%;padding:8px 10px;border:1.5px solid var(--border-color);border-radius:8px;font-size:13px;background:var(--bg-card);color:var(--text-primary);">
                    </div>
                </div>
            </div>
            @endif

            {{-- Botones de formato --}}
            <p style="font-size:12px;font-weight:600;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">Selecciona el formato:</p>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">

                {{-- PDF --}}
                @if(!empty($exportRoutes['pdf']))
                <button onclick="exportarModulo('{{ $exportModule }}', 'pdf', '{{ $exportRoutes['pdf'] }}')"
                    style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:18px 12px;background:var(--bg-card);border:2px solid var(--border-color);border-radius:12px;cursor:pointer;transition:all .2s;color:var(--text-primary);"
                    onmouseover="this.style.borderColor='#dc3545';this.style.background='rgba(220,53,69,.06)'"
                    onmouseout="this.style.borderColor='var(--border-color)';this.style.background='var(--bg-card)'">
                    <i class='bx bxs-file-pdf' style="font-size:32px;color:#dc3545;"></i>
                    <span style="font-size:13px;font-weight:700;">PDF</span>
                    <small style="font-size:10px;color:var(--text-muted);">Diseño profesional</small>
                </button>
                @endif

                {{-- Excel --}}
                @if(!empty($exportRoutes['excel']))
                <button onclick="exportarModulo('{{ $exportModule }}', 'excel', '{{ $exportRoutes['excel'] }}')"
                    style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:18px 12px;background:var(--bg-card);border:2px solid var(--border-color);border-radius:12px;cursor:pointer;transition:all .2s;color:var(--text-primary);"
                    onmouseover="this.style.borderColor='#198754';this.style.background='rgba(25,135,84,.06)'"
                    onmouseout="this.style.borderColor='var(--border-color)';this.style.background='var(--bg-card)'">
                    <i class='bx bxs-file' style="font-size:32px;color:#198754;"></i>
                    <span style="font-size:13px;font-weight:700;">Excel</span>
                    <small style="font-size:10px;color:var(--text-muted);">.xlsx</small>
                </button>
                @endif

                {{-- CSV --}}
                @if(!empty($exportRoutes['csv']))
                <button onclick="exportarModulo('{{ $exportModule }}', 'csv', '{{ $exportRoutes['csv'] }}')"
                    style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:18px 12px;background:var(--bg-card);border:2px solid var(--border-color);border-radius:12px;cursor:pointer;transition:all .2s;color:var(--text-primary);"
                    onmouseover="this.style.borderColor='#0d6efd';this.style.background='rgba(13,110,253,.06)'"
                    onmouseout="this.style.borderColor='var(--border-color)';this.style.background='var(--bg-card)'">
                    <i class='bx bx-spreadsheet' style="font-size:32px;color:#0d6efd;"></i>
                    <span style="font-size:13px;font-weight:700;">CSV</span>
                    <small style="font-size:10px;color:var(--text-muted);">Texto plano</small>
                </button>
                @endif

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('{{ $modalId }}')">
                <i class='bx bx-x'></i> Cerrar
            </button>
        </div>
    </div>
</div>
