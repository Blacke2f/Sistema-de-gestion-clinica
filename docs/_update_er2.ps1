# Update modelo_er.html - using here-strings for HTML content
$path = "c:\laragon\www\SISGECLIN\docs\modelo_er.html"
$content = [System.IO.File]::ReadAllText($path)

# === Change 1: Add Ocupacional tab button before Completo tab ===
$old1 = @'
          <button class="diagram-tab" onclick="mostrarDiagrama('er-completo')">
            <i class='bx bx-expand'></i> Completo v3.0
          </button>
'@
$new1 = @'
          <button class="diagram-tab" onclick="mostrarDiagrama('er-ocupacional')">
            <i class='bx bx-id-card'></i> Ocupacional
          </button>
          <button class="diagram-tab" onclick="mostrarDiagrama('er-completo')">
            <i class='bx bx-expand'></i> Completo v3.0
          </button>
'@
$content = $content.Replace($old1, $new1)
Write-Host "Change 1 (tab button) done: $($content.Contains('er-ocupacional'))"

# === Change 2: Add Ocupacional panel before Completo comment ===
$old2 = "        <!-- Panel: Completo v3.0 -->"
$new2 = @'
        <!-- Panel: Ocupacional -->
        <div class="diagram-panel" id="er-ocupacional">
          <div class="mermaid-wrap">
            <div class="mermaid" id="mermaid-ocupacional">
erDiagram
    informes_medicos {
        int id PK
        string numero_informe
        int paciente_id FK
        int consulta_id FK
        int empleado_id FK
        string nombre_empresa
        string tipo_consulta
        string aptitud
        string diagnostico
        string tipo_documento
        string imagen_escaneo
        date fecha
    }
    gastos_medicos {
        int id PK
        string numero_gasto
        int paciente_id FK
        int consulta_id FK
        int empleado_id FK
        string nombre_empresa
        string tipo_documento
        string imagen_escaneo
        date fecha
    }
    gasto_medico_items {
        int id PK
        int gasto_medico_id FK
        string concepto
        decimal monto
    }
    examenes_laboratorio {
        int id PK
        string numero_solicitud
        int paciente_id FK
        int consulta_id FK
        int empleado_id FK
        boolean hematologia_completa
        boolean glicemia_ayunas
        boolean urea
        boolean creatinina
        boolean colesterol_total
        boolean trigliceridos
        boolean examen_orina
        boolean examen_heces
        string tipo_documento
        string imagen_escaneo
        date fecha
    }
    pacientes ||--o{ informes_medicos : "tiene"
    consultas ||--o{ informes_medicos : "genera"
    nomina ||--o{ informes_medicos : "atiende"
    pacientes ||--o{ gastos_medicos : "tiene"
    consultas ||--o{ gastos_medicos : "genera"
    nomina ||--o{ gastos_medicos : "atiende"
    gastos_medicos ||--o{ gasto_medico_items : "contiene"
    pacientes ||--o{ examenes_laboratorio : "tiene"
    consultas ||--o{ examenes_laboratorio : "genera"
    nomina ||--o{ examenes_laboratorio : "solicita"
            </div>
          </div>
          <p class="zoom-hint"><i class='bx bx-mouse'></i> Modulos ocupacionales: Informes Medicos, Gastos Medicos, Examenes de Laboratorio</p>
        </div>

        <!-- Panel: Completo v3.0 -->
'@
$content = $content.Replace($old2, $new2)
Write-Host "Change 2 (ocupacional panel) done: $($content.Contains('mermaid-ocupacional'))"

# === Change 3: Add new relationships to completo diagram ===
$old3 = @'
    RECETAS ||--o{ RECETA_ITEMS             : "contiene"
            </div>
          </div>
          <p class="zoom-hint"><i class='bx bx-mouse'></i> Diagrama completo v3.0 — 17 tablas activas (sistema institucional sin facturación)</p>
'@
$new3 = @'
    RECETAS ||--o{ RECETA_ITEMS             : "contiene"
    PACIENTES ||--o{ informes_medicos       : "tiene"
    CONSULTAS ||--o{ informes_medicos       : "genera"
    NOMINA    ||--o{ informes_medicos       : "atiende"
    PACIENTES ||--o{ gastos_medicos         : "tiene"
    CONSULTAS ||--o{ gastos_medicos         : "genera"
    NOMINA    ||--o{ gastos_medicos         : "atiende"
    gastos_medicos ||--o{ gasto_medico_items : "contiene"
    PACIENTES ||--o{ examenes_laboratorio   : "tiene"
    CONSULTAS ||--o{ examenes_laboratorio   : "genera"
    NOMINA    ||--o{ examenes_laboratorio   : "solicita"
            </div>
          </div>
          <p class="zoom-hint"><i class='bx bx-mouse'></i> Diagrama completo v3.0 — 21 tablas activas (sistema institucional)</p>
'@
$content = $content.Replace($old3, $new3)
Write-Host "Change 3 (completo relationships) done: $($content.Contains('informes_medicos'))"

# === Change 4: Add new table cards before the info alert in tablas-nuevas ===
$insertBefore = @'
        <div class="doc-alert info" style="margin-top:16px;"><i class='bx bx-info-circle'></i><div>Las tablas <strong>PACIENTES, CONSULTAS, NOMINA e INVENTARIO</strong>
'@

$newCards = @'
            <!-- INFORMES_MEDICOS -->
            <div class="table-card">
                <div class="table-card-header">INFORMES_MEDICOS <span>Informes medico-ocupacionales</span></div>
                <table>
                    <thead><tr><th>Columna</th><th>Tipo</th><th>Restriccion</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>BIGINT</td><td><span class="badge badge-pk">PK</span></td></tr>
                        <tr><td><code>numero_informe</code></td><td>VARCHAR(20)</td><td>UNIQUE</td></tr>
                        <tr><td><code>paciente_id</code></td><td>BIGINT</td><td><span class="badge badge-fk">FK</span> &rarr; PACIENTES</td></tr>
                        <tr><td><code>consulta_id</code></td><td>BIGINT</td><td><span class="badge badge-fk">FK</span> &rarr; CONSULTAS <span class="badge badge-null">NULL</span></td></tr>
                        <tr><td><code>empleado_id</code></td><td>BIGINT</td><td><span class="badge badge-fk">FK</span> &rarr; NOMINA <span class="badge badge-null">NULL</span></td></tr>
                        <tr><td><code>nombre_empresa</code></td><td>VARCHAR(150)</td><td>NOT NULL</td></tr>
                        <tr><td><code>tipo_consulta</code></td><td>ENUM</td><td>preempleo|prevacacional|postvacacional|egreso|reubicacion|otros</td></tr>
                        <tr><td><code>aptitud</code></td><td>ENUM</td><td>apto|no_apto|apto_condicionado</td></tr>
                        <tr><td><code>diagnostico</code></td><td>TEXT</td><td><span class="badge badge-null">NULL</span></td></tr>
                        <tr><td><code>tipo_documento</code></td><td>ENUM</td><td>digital|manuscrita</td></tr>
                        <tr><td><code>imagen_escaneo</code></td><td>VARCHAR(255)</td><td><span class="badge badge-null">NULL</span></td></tr>
                        <tr><td><code>fecha</code></td><td>DATE</td><td>NOT NULL</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- GASTOS_MEDICOS -->
            <div class="table-card">
                <div class="table-card-header">GASTOS_MEDICOS <span>Gastos de atencion medica</span></div>
                <table>
                    <thead><tr><th>Columna</th><th>Tipo</th><th>Restriccion</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>BIGINT</td><td><span class="badge badge-pk">PK</span></td></tr>
                        <tr><td><code>numero_gasto</code></td><td>VARCHAR(20)</td><td>UNIQUE</td></tr>
                        <tr><td><code>paciente_id</code></td><td>BIGINT</td><td><span class="badge badge-fk">FK</span> &rarr; PACIENTES</td></tr>
                        <tr><td><code>consulta_id</code></td><td>BIGINT</td><td><span class="badge badge-fk">FK</span> &rarr; CONSULTAS <span class="badge badge-null">NULL</span></td></tr>
                        <tr><td><code>empleado_id</code></td><td>BIGINT</td><td><span class="badge badge-fk">FK</span> &rarr; NOMINA <span class="badge badge-null">NULL</span></td></tr>
                        <tr><td><code>nombre_empresa</code></td><td>VARCHAR(150)</td><td>NOT NULL</td></tr>
                        <tr><td><code>descripcion</code></td><td>TEXT</td><td><span class="badge badge-null">NULL</span></td></tr>
                        <tr><td><code>tipo_documento</code></td><td>ENUM</td><td>digital|manuscrita</td></tr>
                        <tr><td><code>imagen_escaneo</code></td><td>VARCHAR(255)</td><td><span class="badge badge-null">NULL</span></td></tr>
                        <tr><td><code>fecha</code></td><td>DATE</td><td>NOT NULL</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- GASTO_MEDICO_ITEMS -->
            <div class="table-card">
                <div class="table-card-header">GASTO_MEDICO_ITEMS <span>Conceptos de cada gasto</span></div>
                <table>
                    <thead><tr><th>Columna</th><th>Tipo</th><th>Restriccion</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>BIGINT</td><td><span class="badge badge-pk">PK</span></td></tr>
                        <tr><td><code>gasto_medico_id</code></td><td>BIGINT</td><td><span class="badge badge-fk">FK</span> &rarr; GASTOS_MEDICOS ON DELETE CASCADE</td></tr>
                        <tr><td><code>concepto</code></td><td>VARCHAR(255)</td><td>NOT NULL</td></tr>
                        <tr><td><code>monto</code></td><td>DECIMAL(10,2)</td><td>NOT NULL</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- EXAMENES_LABORATORIO -->
            <div class="table-card">
                <div class="table-card-header">EXAMENES_LABORATORIO <span>Solicitudes de examenes</span></div>
                <table>
                    <thead><tr><th>Columna</th><th>Tipo</th><th>Restriccion</th></tr></thead>
                    <tbody>
                        <tr><td><code>id</code></td><td>BIGINT</td><td><span class="badge badge-pk">PK</span></td></tr>
                        <tr><td><code>numero_solicitud</code></td><td>VARCHAR(20)</td><td>UNIQUE</td></tr>
                        <tr><td><code>paciente_id</code></td><td>BIGINT</td><td><span class="badge badge-fk">FK</span> &rarr; PACIENTES</td></tr>
                        <tr><td><code>consulta_id</code></td><td>BIGINT</td><td><span class="badge badge-fk">FK</span> &rarr; CONSULTAS <span class="badge badge-null">NULL</span></td></tr>
                        <tr><td><code>empleado_id</code></td><td>BIGINT</td><td><span class="badge badge-fk">FK</span> &rarr; NOMINA <span class="badge badge-null">NULL</span></td></tr>
                        <tr><td><code>hematologia_completa</code></td><td>BOOLEAN</td><td>default: false</td></tr>
                        <tr><td><code>glicemia_ayunas</code></td><td>BOOLEAN</td><td>default: false</td></tr>
                        <tr><td><code>urea</code></td><td>BOOLEAN</td><td>default: false</td></tr>
                        <tr><td><code>creatinina</code></td><td>BOOLEAN</td><td>default: false</td></tr>
                        <tr><td><code>colesterol_total</code></td><td>BOOLEAN</td><td>default: false</td></tr>
                        <tr><td><code>trigliceridos</code></td><td>BOOLEAN</td><td>default: false</td></tr>
                        <tr><td><code>examen_orina</code></td><td>BOOLEAN</td><td>default: false</td></tr>
                        <tr><td><code>examen_heces</code></td><td>BOOLEAN</td><td>default: false</td></tr>
                        <tr><td><code>otros</code></td><td>TEXT</td><td><span class="badge badge-null">NULL</span></td></tr>
                        <tr><td><code>tipo_documento</code></td><td>ENUM</td><td>digital|manuscrita</td></tr>
                        <tr><td><code>imagen_escaneo</code></td><td>VARCHAR(255)</td><td><span class="badge badge-null">NULL</span></td></tr>
                        <tr><td><code>fecha</code></td><td>DATE</td><td>NOT NULL</td></tr>
                    </tbody>
                </table>
            </div>

        <div class="doc-alert info" style="margin-top:16px;"><i class='bx bx-info-circle'></i><div>Las tablas <strong>PACIENTES, CONSULTAS, NOMINA e INVENTARIO</strong>
'@
$content = $content.Replace($insertBefore, $newCards)
Write-Host "Change 4 (new table cards) done: $($content.Contains('INFORMES_MEDICOS'))"

# === Change 5: Add new relationships ===
$oldRel = @'
            <li><code>INVENTARIO</code> <span class="rel-arrow">→</span> <code>RECETA_ITEMS</code> <span class="rel-type">1 a N</span> Un medicamento puede aparecer en muchas recetas. ON DELETE SET NULL.</li>
        </ul>
'@
$newRel = @'
            <li><code>INVENTARIO</code> <span class="rel-arrow">→</span> <code>RECETA_ITEMS</code> <span class="rel-type">1 a N</span> Un medicamento puede aparecer en muchas recetas. ON DELETE SET NULL.</li>
            <!-- Nuevas relaciones modulos ocupacionales v3.0 -->
            <li><code>PACIENTES</code> <span class="rel-arrow">→</span> <code>INFORMES_MEDICOS</code> <span class="rel-type">1 a N</span> Un paciente puede tener muchos informes medico-ocupacionales. ON DELETE CASCADE.</li>
            <li><code>CONSULTAS</code> <span class="rel-arrow">→</span> <code>INFORMES_MEDICOS</code> <span class="rel-type">1 a N</span> Una consulta puede generar informes medicos. ON DELETE SET NULL.</li>
            <li><code>NOMINA</code> <span class="rel-arrow">→</span> <code>INFORMES_MEDICOS</code> <span class="rel-type">1 a N</span> Un empleado medico puede atender muchos informes. ON DELETE SET NULL.</li>
            <li><code>PACIENTES</code> <span class="rel-arrow">→</span> <code>GASTOS_MEDICOS</code> <span class="rel-type">1 a N</span> Un paciente puede tener muchos gastos medicos. ON DELETE CASCADE.</li>
            <li><code>CONSULTAS</code> <span class="rel-arrow">→</span> <code>GASTOS_MEDICOS</code> <span class="rel-type">1 a N</span> Una consulta puede generar gastos medicos. ON DELETE SET NULL.</li>
            <li><code>NOMINA</code> <span class="rel-arrow">→</span> <code>GASTOS_MEDICOS</code> <span class="rel-type">1 a N</span> Un empleado puede atender gastos medicos. ON DELETE SET NULL.</li>
            <li><code>GASTOS_MEDICOS</code> <span class="rel-arrow">→</span> <code>GASTO_MEDICO_ITEMS</code> <span class="rel-type">1 a N</span> Un gasto contiene uno o varios conceptos. ON DELETE CASCADE.</li>
            <li><code>PACIENTES</code> <span class="rel-arrow">→</span> <code>EXAMENES_LABORATORIO</code> <span class="rel-type">1 a N</span> Un paciente puede tener muchas solicitudes de examenes. ON DELETE CASCADE.</li>
            <li><code>CONSULTAS</code> <span class="rel-arrow">→</span> <code>EXAMENES_LABORATORIO</code> <span class="rel-type">1 a N</span> Una consulta puede generar solicitudes de examenes. ON DELETE SET NULL.</li>
            <li><code>NOMINA</code> <span class="rel-arrow">→</span> <code>EXAMENES_LABORATORIO</code> <span class="rel-type">1 a N</span> Un empleado puede solicitar examenes de laboratorio. ON DELETE SET NULL.</li>
        </ul>
'@
$content = $content.Replace($oldRel, $newRel)
Write-Host "Change 5 (new relationships) done: $($content.Contains('INFORMES_MEDICOS'))"

# === Change 6: Update footer count ===
$old6 = "17 tablas activas (sin facturas — sistema institucional) — © 2026"
$new6 = "21 tablas activas (sistema institucional) — © 2026"
$content = $content.Replace($old6, $new6)
Write-Host "Change 6 (footer) done: $($content.Contains('21 tablas activas (sistema institucional)'))"

# === Change 7: Update permisos_usuario modulo list ===
$old7 = "pacientes|consultas|medicina|inventario|mensajes|nomina|usuarios|solicitudes|bitacora"
$new7 = "pacientes|consultas|citas|recetas|informes|gastos|examenes|medicina|inventario|mensajes|nomina|usuarios|solicitudes|bitacora|reportes"
$content = $content.Replace($old7, $new7)
Write-Host "Change 7 (permisos modulo list) done: $($content.Contains('informes|gastos|examenes'))"

[System.IO.File]::WriteAllText($path, $content)
Write-Host "modelo_er.html saved successfully"
