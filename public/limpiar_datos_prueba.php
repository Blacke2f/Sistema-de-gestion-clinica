<?php
/**
 * ╔══════════════════════════════════════════════════════════════════════════╗
 * ║          SISGECLIN v2.5 — Script de Limpieza de Datos de Prueba         ║
 * ║                                                                          ║
 * ║  ✅ CONSERVA: Usuarios, contraseñas, roles, estructura de la BD          ║
 * ║  🗑️  ELIMINA: Pacientes, consultas, inventario, nómina, mensajes,        ║
 * ║               notificaciones, bitácora, solicitudes                      ║
 * ║                                                                          ║
 * ║  URL de acceso: http://localhost/SISGECLIN/public/limpiar_datos_prueba.php
 * ╚══════════════════════════════════════════════════════════════════════════╝
 */

// ── Ruta al .env de Laravel (está un nivel arriba de public/) ────────────────
$envPath = dirname(__DIR__) . '/.env';

if (!file_exists($envPath)) {
    die('<p style="color:red;font-family:monospace;padding:20px;">❌ No se encontró el archivo .env en: ' . htmlspecialchars($envPath) . '</p>');
}

// ── Parsear el archivo .env ───────────────────────────────────────────────────
$env = [];
foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
    if (strpos(trim($linea), '#') === 0 || strpos($linea, '=') === false) continue;
    [$clave, $valor] = explode('=', $linea, 2);
    $env[trim($clave)] = trim($valor, " \t\n\r\0\x0B\"'");
}

$dbHost = $env['DB_HOST']     ?? '127.0.0.1';
$dbPort = $env['DB_PORT']     ?? '3306';
$dbName = $env['DB_DATABASE'] ?? 'sisgeclin_laravel';
$dbUser = $env['DB_USERNAME'] ?? 'root';
$dbPass = $env['DB_PASSWORD'] ?? '';

// ── ¿Se confirmó la ejecución? ────────────────────────────────────────────────
$confirmado = isset($_POST['confirmar']) && $_POST['confirmar'] === 'SI_LIMPIAR';
$ejecutar   = isset($_POST['ejecutar'])  && $_POST['ejecutar']  === '1';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISGECLIN — Limpiar Datos de Prueba</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #1e293b;
            border-radius: 16px;
            padding: 40px;
            max-width: 680px;
            width: 100%;
            border: 1px solid #334155;
            box-shadow: 0 20px 60px rgba(0,0,0,.5);
        }
        .logo { display: flex; align-items: center; gap: 14px; margin-bottom: 28px; }
        .logo-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #dc3545, #f43f5e);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; flex-shrink: 0;
        }
        .logo h1 { font-size: 20px; font-weight: 800; color: #f1f5f9; }
        .logo p  { font-size: 12px; color: #94a3b8; margin-top: 2px; }

        .warning-box {
            background: rgba(220,53,69,.12);
            border: 1px solid rgba(220,53,69,.4);
            border-left: 4px solid #dc3545;
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 24px;
        }
        .warning-box h2 { font-size: 15px; color: #f87171; margin-bottom: 8px; }
        .warning-box p  { font-size: 13px; color: #fca5a5; line-height: 1.6; }

        .info-box {
            background: rgba(13,110,253,.1);
            border: 1px solid rgba(13,110,253,.3);
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 24px;
            font-family: monospace;
            font-size: 12px;
            color: #93c5fd;
            line-height: 1.8;
        }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px; }
        .title-red   { color: #f87171; }
        .title-green { color: #4ade80; }

        ul.lista { list-style: none; }
        ul.lista li { font-size: 13px; padding: 4px 0; color: #cbd5e1; display: flex; align-items: center; gap: 8px; }

        .confirm-section {
            background: #0f172a;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #334155;
        }
        .confirm-section label { font-size: 13px; color: #94a3b8; display: block; margin-bottom: 8px; }
        .confirm-section input[type="text"] {
            width: 100%;
            padding: 11px 14px;
            background: #1e293b;
            border: 1.5px solid #475569;
            border-radius: 8px;
            color: #f1f5f9;
            font-size: 15px;
            font-family: monospace;
            letter-spacing: 3px;
            transition: border-color .2s;
        }
        .confirm-section input[type="text"]:focus { outline: none; border-color: #dc3545; }

        .btn-danger {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #dc3545, #b91c1c);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s;
        }
        .btn-danger:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(220,53,69,.4); }
        .btn-danger:disabled { opacity: .4; cursor: not-allowed; }

        .btn-cancel { display: block; text-align: center; margin-top: 14px; color: #64748b; font-size: 13px; text-decoration: none; }
        .btn-cancel:hover { color: #94a3b8; }

        /* Resultados */
        .result-box {
            background: #0f172a;
            border-radius: 10px;
            padding: 20px;
            font-family: monospace;
            font-size: 12px;
            max-height: 380px;
            overflow-y: auto;
            border: 1px solid #334155;
            margin-bottom: 20px;
            line-height: 1.8;
        }
        .r-ok    { color: #4ade80; }
        .r-error { color: #f87171; }
        .r-info  { color: #93c5fd; }

        .success-banner {
            background: rgba(34,197,94,.12);
            border: 1px solid rgba(34,197,94,.4);
            border-radius: 10px;
            padding: 24px;
            text-align: center;
            margin-bottom: 20px;
        }
        .success-banner h2 { color: #4ade80; font-size: 20px; margin-bottom: 10px; }
        .success-banner p  { color: #86efac; font-size: 13px; line-height: 1.7; }
        .btn-go {
            display: inline-block;
            padding: 12px 32px;
            background: #0d6efd;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            margin-top: 18px;
        }
        .btn-go:hover { background: #0b5ed7; }
    </style>
</head>
<body>
<div class="card">

    <div class="logo">
        <div class="logo-icon">⚠️</div>
        <div>
            <h1>Limpiar Datos de Prueba</h1>
            <p>SISGECLIN v2.5 — Preparar sistema para producción</p>
        </div>
    </div>

<?php if ($confirmado && $ejecutar): ?>
<?php
    // ══════════════════════════════════════════════════════════════════════════
    // EJECUTAR LA LIMPIEZA
    // ══════════════════════════════════════════════════════════════════════════
    $resultados = [];
    $errores    = 0;

    try {
        $pdo = new PDO(
            "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4",
            $dbUser, $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $resultados[] = ['t' => 'info', 'm' => "✅ Conectado a la BD: {$dbName}"];

        // Desactivar claves foráneas para poder truncar sin conflictos
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $resultados[] = ['t' => 'info', 'm' => '🔓 Restricciones de FK desactivadas temporalmente'];

        // Tablas a limpiar en orden (dependientes primero, principales después)
        $tablas = [
            'consultas_medicamentos' => 'Relación consultas-medicamentos',
            'solicitud_items'        => 'Items de solicitudes',
            'inventario_movimientos' => 'Movimientos de inventario',
            'solicitudes_inventario' => 'Solicitudes de reposición',
            'permisos_usuario'       => 'Permisos de usuarios',
            'notificaciones'         => 'Notificaciones del sistema',
            'mensajes'               => 'Mensajes internos',
            'bitacora'               => 'Bitácora de auditoría',
            'consultas'              => 'Consultas médicas',
            'pacientes'              => 'Pacientes registrados',
            'inventario'             => 'Medicamentos e inventario',
            'nomina'                 => 'Nómina del personal',
        ];

        foreach ($tablas as $tabla => $desc) {
            try {
                $total = $pdo->query("SELECT COUNT(*) FROM `{$tabla}`")->fetchColumn();
                $pdo->exec("TRUNCATE TABLE `{$tabla}`");
                $resultados[] = ['t' => 'ok', 'm' => "🗑️  {$desc} ({$tabla}): {$total} registros eliminados"];
            } catch (PDOException $e) {
                $errores++;
                $resultados[] = ['t' => 'error', 'm' => "❌ Error en {$tabla}: " . $e->getMessage()];
            }
        }

        // Reactivar claves foráneas
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        $resultados[] = ['t' => 'info', 'm' => '🔒 Restricciones de FK reactivadas'];

        // Verificar usuarios conservados
        $usuarios = $pdo->query("SELECT COUNT(*) FROM `usuarios`")->fetchColumn();
        $resultados[] = ['t' => 'ok', 'm' => "👥 Usuarios conservados: {$usuarios} — el sistema sigue siendo accesible"];

        // Limpiar sesiones activas de Laravel
        $sessionPath = dirname(__DIR__) . '/storage/framework/sessions';
        if (is_dir($sessionPath)) {
            $count = 0;
            foreach (glob($sessionPath . '/*') as $f) {
                if (is_file($f)) { unlink($f); $count++; }
            }
            $resultados[] = ['t' => 'info', 'm' => "🔄 Sesiones activas eliminadas: {$count} archivo(s)"];
        }

    } catch (PDOException $e) {
        $errores++;
        $resultados[] = ['t' => 'error', 'm' => '❌ Error de conexión: ' . $e->getMessage()];
    }
?>

    <?php if ($errores === 0): ?>
    <div class="success-banner">
        <h2>✅ Limpieza completada</h2>
        <p>
            Todos los datos de prueba fueron eliminados correctamente.<br>
            Los usuarios del sistema se conservaron intactos.<br>
            El sistema está listo para uso en producción.
        </p>
        <a href="/SISGECLIN/public" class="btn-go">Ir al Sistema →</a>
    </div>
    <?php else: ?>
    <div class="warning-box">
        <h2>⚠️ Completado con <?= $errores ?> error(es)</h2>
        <p>Algunos datos pueden no haberse eliminado. Revisa el log abajo.</p>
    </div>
    <?php endif; ?>

    <div class="result-box">
        <?php foreach ($resultados as $r): ?>
            <div class="r-<?= $r['t'] ?>"><?= htmlspecialchars($r['m']) ?></div>
        <?php endforeach; ?>
    </div>

    <a href="/SISGECLIN/public/limpiar_datos_prueba.php" class="btn-cancel">← Volver</a>

<?php else: ?>

    <div class="warning-box">
        <h2>⚠️ Esta acción es IRREVERSIBLE</h2>
        <p>
            Se eliminarán permanentemente todos los datos de prueba.<br>
            <strong>Haz un backup de la base de datos antes de continuar.</strong>
        </p>
    </div>

    <div class="grid-2">
        <div>
            <div class="section-title title-red">🗑️ Se eliminará</div>
            <ul class="lista">
                <li>🗑️ Pacientes registrados</li>
                <li>🗑️ Consultas médicas</li>
                <li>🗑️ Medicamentos e inventario</li>
                <li>🗑️ Movimientos de inventario</li>
                <li>🗑️ Nómina del personal</li>
                <li>🗑️ Mensajes internos</li>
                <li>🗑️ Notificaciones</li>
                <li>🗑️ Bitácora de auditoría</li>
                <li>🗑️ Solicitudes de inventario</li>
                <li>🗑️ Permisos de usuarios</li>
                <li>🗑️ Sesiones activas</li>
            </ul>
        </div>
        <div>
            <div class="section-title title-green">✅ Se conservará</div>
            <ul class="lista">
                <li>✅ Todos los usuarios</li>
                <li>✅ Contraseñas y roles</li>
                <li>✅ Estructura de la BD</li>
                <li>✅ Configuración del sistema</li>
                <li>✅ Archivos del proyecto</li>
            </ul>
        </div>
    </div>

    <div class="info-box">
        📋 Base de datos detectada:<br>
        &nbsp;&nbsp;Host: <strong><?= htmlspecialchars($dbHost) ?>:<?= htmlspecialchars($dbPort) ?></strong><br>
        &nbsp;&nbsp;BD: <strong><?= htmlspecialchars($dbName) ?></strong><br>
        &nbsp;&nbsp;Usuario: <strong><?= htmlspecialchars($dbUser) ?></strong>
    </div>

    <form method="POST" onsubmit="return validarConfirmacion()">
        <div class="confirm-section">
            <label>
                Para confirmar, escribe exactamente:
                <strong style="color:#f87171;font-family:monospace;letter-spacing:2px;">SI_LIMPIAR</strong>
            </label>
            <input type="text" id="campo-conf" name="confirmar"
                   placeholder="Escribe SI_LIMPIAR aquí"
                   autocomplete="off" spellcheck="false">
        </div>
        <input type="hidden" name="ejecutar" value="1">
        <button type="submit" class="btn-danger" id="btn-exec" disabled>
            🗑️ Eliminar Todos los Datos de Prueba
        </button>
    </form>

    <a href="/SISGECLIN/public" class="btn-cancel">← Cancelar y volver al sistema</a>

    <script>
        document.getElementById('campo-conf').addEventListener('input', function() {
            document.getElementById('btn-exec').disabled = (this.value !== 'SI_LIMPIAR');
        });
        function validarConfirmacion() {
            if (document.getElementById('campo-conf').value !== 'SI_LIMPIAR') {
                alert('Debes escribir exactamente: SI_LIMPIAR');
                return false;
            }
            return confirm('⚠️ ÚLTIMA CONFIRMACIÓN\n\n¿Estás seguro de eliminar TODOS los datos de prueba?\n\nEsta acción NO se puede deshacer.');
        }
    </script>

<?php endif; ?>

</div>
</body>
</html>
