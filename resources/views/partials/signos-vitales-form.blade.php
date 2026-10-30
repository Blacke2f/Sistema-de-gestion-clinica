{{--
    Partial: Sección de Signos Vitales (SIN form anidado)
    Se usa dentro del modal de edición de consulta.
    Los datos se envían via AJAX independiente al guardar signos vitales.
    Uso: @include('partials.signos-vitales-form', ['consultaId' => '', 'pacienteId' => ''])
--}}
<div id="signos-vitales-section" style="background:var(--bg-body);border-radius:12px;padding:16px;margin-top:14px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <strong style="font-size:14px;"><i class='bx bx-heart-circle' style="color:#dc3545;"></i> Signos Vitales</strong>
        <span id="sv-imc-badge" style="display:none;" class="badge badge-info"></span>
    </div>

    {{-- NO usar <form> aquí — está dentro del form-consulta y HTML no permite forms anidados --}}
    <div id="sv-fields">
        {{-- Campos ocultos para IDs — se actualizan via JS al abrir el modal --}}
        <input type="hidden" id="sv-consulta-id" value="{{ $consultaId ?? '' }}">
        <input type="hidden" id="sv-paciente-id" value="{{ $pacienteId ?? '' }}">

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px;">
            <div class="form-group">
                <label style="font-size:11px;"><i class='bx bx-heart'></i> Presión Sistólica (mmHg)</label>
                <input type="number" id="sv-presion-sistolica" placeholder="120" min="50" max="300" step="0.1">
            </div>
            <div class="form-group">
                <label style="font-size:11px;"><i class='bx bx-heart'></i> Presión Diastólica (mmHg)</label>
                <input type="number" id="sv-presion-diastolica" placeholder="80" min="30" max="200" step="0.1">
            </div>
            <div class="form-group">
                <label style="font-size:11px;"><i class='bx bx-pulse'></i> Frec. Cardíaca (lpm)</label>
                <input type="number" id="sv-frecuencia-cardiaca" placeholder="72" min="20" max="300" step="0.1">
            </div>
            <div class="form-group">
                <label style="font-size:11px;"><i class='bx bx-wind'></i> Frec. Respiratoria (rpm)</label>
                <input type="number" id="sv-frecuencia-respiratoria" placeholder="16" min="5" max="60" step="0.1">
            </div>
            <div class="form-group">
                <label style="font-size:11px;"><i class='bx bx-thermometer'></i> Temperatura (°C)</label>
                <input type="number" id="sv-temperatura" placeholder="36.5" min="30" max="45" step="0.1">
            </div>
            <div class="form-group">
                <label style="font-size:11px;"><i class='bx bx-droplet'></i> SpO₂ (%)</label>
                <input type="number" id="sv-saturacion-oxigeno" placeholder="98" min="50" max="100" step="0.1">
            </div>
            <div class="form-group">
                <label style="font-size:11px;"><i class='bx bx-body'></i> Peso (kg)</label>
                <input type="number" id="sv-peso" placeholder="70" min="1" max="500" step="0.1" oninput="calcularIMC()">
            </div>
            <div class="form-group">
                <label style="font-size:11px;"><i class='bx bx-ruler'></i> Talla (cm)</label>
                <input type="number" id="sv-talla" placeholder="170" min="30" max="250" step="0.1" oninput="calcularIMC()">
            </div>
            <div class="form-group">
                <label style="font-size:11px;"><i class='bx bx-stats'></i> IMC (calculado)</label>
                <input type="number" id="sv-imc" placeholder="Auto" readonly style="background:var(--bg-card);cursor:not-allowed;">
            </div>
            <div class="form-group">
                <label style="font-size:11px;"><i class='bx bx-droplet-half'></i> Glucosa (mg/dL)</label>
                <input type="number" id="sv-glucosa" placeholder="90" min="20" max="800" step="0.1">
            </div>
        </div>

        <div class="form-group" style="margin-top:6px;">
            <label style="font-size:11px;"><i class='bx bx-note'></i> Observaciones</label>
            <textarea id="sv-observaciones" rows="2" placeholder="Observaciones clínicas..."></textarea>
        </div>

        <div style="display:flex;justify-content:flex-end;margin-top:10px;">
            <button type="button" id="btn-guardar-sv" onclick="guardarSignosVitales()" class="btn-primary btn-sm">
                <i class='bx bx-save'></i> Guardar Signos Vitales
            </button>
        </div>
    </div>
</div>

<script>
function calcularIMC() {
    const peso  = parseFloat(document.getElementById('sv-peso')?.value);
    const talla = parseFloat(document.getElementById('sv-talla')?.value);
    const imcEl = document.getElementById('sv-imc');
    const badge = document.getElementById('sv-imc-badge');
    if (peso > 0 && talla > 0) {
        const tallaM = talla / 100;
        const imc    = (peso / (tallaM * tallaM)).toFixed(2);
        if (imcEl) imcEl.value = imc;
        const clasificacion = parseFloat(imc) < 18.5 ? 'Bajo peso'
            : parseFloat(imc) < 25 ? 'Normal'
            : parseFloat(imc) < 30 ? 'Sobrepeso' : 'Obesidad';
        if (badge) { badge.textContent = `IMC ${imc} — ${clasificacion}`; badge.style.display = 'inline-block'; }
    }
}

async function guardarSignosVitales() {
    const btn = document.getElementById('btn-guardar-sv');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Guardando...'; }

    // Recolectar datos manualmente (sin <form> para evitar anidamiento)
    const fd = new FormData();
    fd.append('_token',                  window.CSRF_TOKEN);
    fd.append('consulta_id',             document.getElementById('sv-consulta-id')?.value || '');
    fd.append('paciente_id',             document.getElementById('sv-paciente-id')?.value || '');
    fd.append('presion_sistolica',       document.getElementById('sv-presion-sistolica')?.value || '');
    fd.append('presion_diastolica',      document.getElementById('sv-presion-diastolica')?.value || '');
    fd.append('frecuencia_cardiaca',     document.getElementById('sv-frecuencia-cardiaca')?.value || '');
    fd.append('frecuencia_respiratoria', document.getElementById('sv-frecuencia-respiratoria')?.value || '');
    fd.append('temperatura',             document.getElementById('sv-temperatura')?.value || '');
    fd.append('saturacion_oxigeno',      document.getElementById('sv-saturacion-oxigeno')?.value || '');
    fd.append('peso',                    document.getElementById('sv-peso')?.value || '');
    fd.append('talla',                   document.getElementById('sv-talla')?.value || '');
    fd.append('imc',                     document.getElementById('sv-imc')?.value || '');
    fd.append('glucosa',                 document.getElementById('sv-glucosa')?.value || '');
    fd.append('observaciones',           document.getElementById('sv-observaciones')?.value || '');

    try {
        const res  = await fetch('{{ route('signos.store') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' },
            body: fd
        });
        const data = await res.json();
        if (data.success) {
            showToast('Signos vitales guardados correctamente.', 'success');
            if (data.imc_clasificacion) {
                const badge = document.getElementById('sv-imc-badge');
                if (badge) { badge.textContent = `IMC — ${data.imc_clasificacion}`; badge.style.display = 'inline-block'; }
            }
        } else {
            showToast(data.message || 'Error al guardar.', 'error');
        }
    } catch(e) {
        showToast('Error de conexión.', 'error');
    } finally {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bx bx-save"></i> Guardar Signos Vitales'; }
    }
}
</script>
