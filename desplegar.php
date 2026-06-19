<?php
/**
 * ╔══════════════════════════════════════════════════════════════════════╗
 * ║          SISGECLIN v3.0 — Script de Deploy / Setup Seguro           ║
 * ║                                                                      ║
 * ║  Sistema de Gestión Clínica — Maxical C.A.                          ║
 * ║  Yaritagua, Estado Yaracuy, Venezuela                               ║
 * ║                                                                      ║
 * ║  Uso:                                                                ║
 * ║    Doble clic en desplegar.bat (recomendado — abre admin automático)  ║
 * ║    php desplegar.php           (manual desde consola)                 ║
 * ║                                                                      ║
 * ║  Lo que hace:                                                        ║
 * ║  ✓ Verifica PHP 8.2+ y extensiones requeridas                      ║
 * ║  ✓ Instala/verifica composer vendor/                                ║
 * ║  ✓ Crea .env desde .env.example si no existe                       ║
 * ║  ✓ Genera APP_KEY si falta                                          ║
 * ║  ✓ Crea directorios de storage y escaneos                          ║
 * ║  ✓ Enlace simbólico storage → public/storage                       ║
 * ║  ✓ Ejecuta migraciones pendientes (seguro con datos existentes)    ║
 * ║  ✓ Crea/verifica usuario admin (admin / Admin123)                  ║
 * ║  ✓ Limpia y regenera caches                                        ║
 * ║                                                                      ║
 * ║  MÓDULOS INCLUIDOS v3.0:                                            ║
 * ║  Pacientes, Expediente, Citas, Consultas, Signos Vitales           ║
 * ║  Recetas (digital+manuscrita), Informes Médicos Ocupacionales      ║
 * ║  Gastos Médicos, Exámenes de Laboratorio, Inventario               ║
 * ║  Nómina, Usuarios, Mensajes, Notificaciones, Bitácora              ║
 * ║  Reportes, Exportaciones, Permisos ACL, Respaldos Automáticos      ║
 * ║  (Scheduler, Alertas de Stock, Limpieza de Agenda)                 ║
 * ╚══════════════════════════════════════════════════════════════════════╝
 */

define('DEPLOY_START', microtime(true));

// ── Colores ANSI ──────────────────────────────────────────────────────────────
function c(string $msg, string $color = 'white'): void
{
    $map = [
        'green'  => "\033[0;32m",
        'red'    => "\033[0;31m",
        'yellow' => "\033[1;33m",
        'cyan'   => "\033[0;36m",
        'white'  => "\033[0;37m",
        'gray'   => "\033[0;90m",
        'bold'   => "\033[1m",
        'reset'  => "\033[0m",
    ];
    echo ($map[$color] ?? '') . $msg . $map['reset'] . PHP_EOL;
}

function run(string $cmd, bool $allowFail = false): bool
{
    c("  -> {$cmd}", 'cyan');
    exec($cmd . ' 2>&1', $output, $code);
    foreach ($output as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $lower = strtolower($line);
        if (str_contains($lower, 'error') || str_contains($lower, 'failed')) {
            c("    {$line}", 'red');
        } elseif (
            str_contains($line, 'DONE')  || str_contains($line, 'INFO') ||
            str_contains($line, 'OK')    || str_contains($line, 'already')
        ) {
            c("    {$line}", 'green');
        } else {
            c("    {$line}", 'gray');
        }
    }
    if ($code !== 0 && !$allowFail) {
        c("  X Comando fallo con codigo {$code}", 'red');
    }
    return $code === 0;
}

$root = __DIR__;

c('');
c('╔══════════════════════════════════════════════════════════════════════╗', 'bold');
c('║      SISGECLIN v3.0 — Deploy Seguro                                 ║', 'bold');
c('║      Maxical C.A. — Servicio Medico Ocupacional                     ║', 'bold');
c('╚══════════════════════════════════════════════════════════════════════╝', 'bold');
c('');

// ── PASO 1: PHP ──────────────────────────────────────────────────────────────
c('[ 1/8 ] Verificando PHP y extensiones...', 'yellow');
if (PHP_VERSION_ID < 80200) {
    c('  X PHP 8.2+ requerido. Version actual: ' . PHP_VERSION, 'red');
    exit(1);
}
c('  OK PHP ' . PHP_VERSION, 'green');

$ext_req = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json', 'fileinfo', 'gd', 'zip'];
$ext_miss = array_filter($ext_req, fn($e) => !extension_loaded($e));
if (!empty($ext_miss)) {
    c('  X Extensiones faltantes: ' . implode(', ', $ext_miss), 'red');
    c('  Habilitalas en php.ini y reinicia Apache.', 'white');
    exit(1);
}
c('  OK Todas las extensiones presentes', 'green');

// ── PASO 2: Composer ─────────────────────────────────────────────────────────
c('');
c('[ 2/8 ] Verificando Composer y vendor/...', 'yellow');
if (!file_exists($root . '/vendor/autoload.php')) {
    c('  vendor/ no encontrado — ejecutando composer install...', 'yellow');
    if (!run("composer install --no-interaction --prefer-dist --optimize-autoloader")) {
        c('  X composer install fallo. Instala Composer: https://getcomposer.org/', 'red');
        exit(1);
    }
} else {
    c('  OK vendor/ encontrado', 'green');
}

// ── PASO 3: .env ─────────────────────────────────────────────────────────────
c('');
c('[ 3/8 ] Verificando .env...', 'yellow');
if (!file_exists($root . '/.env')) {
    if (file_exists($root . '/.env.example')) {
        copy($root . '/.env.example', $root . '/.env');
        c('  OK .env creado desde .env.example', 'green');
        c('  IMPORTANTE: Edita .env — configura DB_DATABASE, DB_USERNAME, DB_PASSWORD', 'yellow');
        run("php \"{$root}/artisan\" key:generate --force");
    } else {
        c('  X .env ni .env.example encontrado', 'red');
        exit(1);
    }
} else {
    c('  OK .env encontrado', 'green');
    $envContent = file_get_contents($root . '/.env');
    if (!str_contains($envContent, 'APP_KEY=base64:')) {
        c('  APP_KEY no generada — generando...', 'yellow');
        run("php \"{$root}/artisan\" key:generate --force");
    } else {
        c('  OK APP_KEY configurada', 'green');
    }
}

// ── PASO 4: Directorios ──────────────────────────────────────────────────────
c('');
c('[ 4/8 ] Creando directorios de almacenamiento...', 'yellow');
$dirs = [
    'storage/app/public/informes-escaneos',
    'storage/app/public/gastos-escaneos',
    'storage/app/public/examenes-escaneos',
    'storage/app/public/recetas-escaneos',
    'storage/app/mpdf',
    'storage/app/SISGECLIN',
    'storage/logs',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache/data',
    'bootstrap/cache',
];
foreach ($dirs as $dir) {
    $path = $root . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        c("  + Creado: {$dir}", 'green');
    } else {
        c("  . Existe: {$dir}", 'gray');
    }
}

// ── PASO 5: Storage link ─────────────────────────────────────────────────────
c('');
c('[ 5/8 ] Enlace simbolico storage...', 'yellow');
run("php \"{$root}/artisan\" storage:link --force", true);

// ── PASO 6: Migraciones ──────────────────────────────────────────────────────
c('');
c('[ 6/8 ] Ejecutando migraciones...', 'yellow');
c('  Seguro con datos existentes (hasColumn / hasTable)', 'gray');
c('  Tablas: usuarios, pacientes, nomina, inventario, consultas,', 'gray');
c('  citas, recetas, signos_vitales, movimientos, mensajes,', 'gray');
c('  notificaciones, bitacora, permisos_usuario, solicitudes,', 'gray');
c('  informes_medicos, gastos_medicos, gasto_medico_items,', 'gray');
c('  examenes_laboratorio (con tipo_documento + imagen_escaneo)', 'gray');
c('');
if (!run("php \"{$root}/artisan\" migrate --force")) {
    c('  X Las migraciones fallaron.', 'red');
    c('  Verifica MySQL en .env: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD', 'white');
    exit(1);
}

// ── PASO 7: Admin seeder ─────────────────────────────────────────────────────
c('');
c('[ 7/8 ] Verificando usuario administrador y catálogos...', 'yellow');
run("php \"{$root}/artisan\" db:seed --class=AdminSeeder --force");
run("php \"{$root}/artisan\" db:seed --class=ServicioMedicoSeeder --force");

// ── PASO 8: Caches ───────────────────────────────────────────────────────────
c('');
c('[ 8/8 ] Limpiando y regenerando caches...', 'yellow');
run("php \"{$root}/artisan\" config:clear");
run("php \"{$root}/artisan\" route:clear");
run("php \"{$root}/artisan\" view:clear");
run("php \"{$root}/artisan\" cache:clear");
c('  Regenerando caches optimizados...', 'gray');
run("php \"{$root}/artisan\" config:cache");
run("php \"{$root}/artisan\" route:cache");

// ── Resumen ───────────────────────────────────────────────────────────────────
$elapsed = round(microtime(true) - DEPLOY_START, 2);
c('');
c('╔══════════════════════════════════════════════════════════════════════╗', 'green');
c('║   OK  Deploy completado en ' . str_pad($elapsed . 's', 43) . '║', 'green');
c('╠══════════════════════════════════════════════════════════════════════╣', 'green');
c('║                                                                      ║', 'green');
c('║   ACCESO AL SISTEMA                                                  ║', 'green');
c('║   URL local  : http://localhost/SISGECLIN/public                    ║', 'green');
c('║   URL red    : http://<IP_DEL_SERVIDOR>/SISGECLIN/public            ║', 'green');
c('║                                                                      ║', 'green');
c('║   CREDENCIALES POR DEFECTO                                           ║', 'green');
c('║   Usuario    : admin                                                 ║', 'green');
c('║   Contrasena : Admin123                                              ║', 'green');
c('║                                                                      ║', 'green');
c('╠══════════════════════════════════════════════════════════════════════╣', 'yellow');
c('║   PROXIMOS PASOS DESPUES DEL PRIMER LOGIN:                          ║', 'yellow');
c('║   1. Cambiar la contrasena del admin en Mi Perfil                   ║', 'yellow');
c('║   2. Crear usuarios con sus roles en el modulo Usuarios             ║', 'yellow');
c('║   3. Registrar medicos en Nomina (para que aparezcan en consultas)  ║', 'yellow');
c('║   4. Agregar medicamentos en el modulo Inventario                   ║', 'yellow');
c('║   5. IMPORTANTE: Configurar el Scheduler (Cron) en el servidor      ║', 'yellow');
c('║      para respaldos, limpieza de citas y alertas de inventario.     ║', 'yellow');
c('╚══════════════════════════════════════════════════════════════════════╝', 'yellow');
c('');
