<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Comando de Backup Automático de Base de Datos
 * Genera un dump SQL y lo guarda en storage/app/backups/
 * Se puede programar en el scheduler de Laravel.
 *
 * Uso: php artisan db:backup
 */
class BackupDatabase extends Command
{
    protected $signature   = 'db:backup {--keep=7 : Días de backups a conservar}';
    protected $description = 'Genera un backup de la base de datos MySQL';

    public function handle(): int
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host     = config('database.connections.mysql.host');
        $port     = config('database.connections.mysql.port', 3306);

        $filename  = "backup_{$database}_" . now()->format('Y-m-d_H-i-s') . '.sql';
        $directory = storage_path('app/backups');
        $filepath  = "{$directory}/{$filename}";

        // Crear directorio si no existe
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Construir comando mysqldump
        $mysqldump = $this->findMysqldump();

        if (!$mysqldump) {
            $this->error('No se encontró mysqldump en el sistema.');
            return self::FAILURE;
        }

        $passArg = $password ? "-p\"{$password}\"" : '';
        $command = "\"{$mysqldump}\" -h {$host} -P {$port} -u {$username} {$passArg} {$database} > \"{$filepath}\"";

        $this->info("Generando backup de {$database}...");

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($filepath) || filesize($filepath) === 0) {
            $this->error('Error al generar el backup.');
            return self::FAILURE;
        }

        $size = round(filesize($filepath) / 1024, 1);
        $this->info("✓ Backup generado: {$filename} ({$size} KB)");

        // Limpiar backups antiguos
        $keepDays = (int) $this->option('keep');
        $this->limpiarBackupsAntiguos($directory, $keepDays);

        return self::SUCCESS;
    }

    /**
     * Busca mysqldump en rutas comunes de Laragon y del sistema.
     */
    private function findMysqldump(): ?string
    {
        $rutas = [
            'C:\\laragon\\bin\\mysql\\mysql-8.4.3-winx64\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'mysqldump', // en PATH del sistema
        ];

        foreach ($rutas as $ruta) {
            if (file_exists($ruta) || $ruta === 'mysqldump') {
                return $ruta;
            }
        }

        // Buscar dinámicamente en C:\laragon\bin\mysql\
        $mysqlDir = 'C:\\laragon\\bin\\mysql\\';
        if (is_dir($mysqlDir)) {
            $dirs = glob($mysqlDir . '*', GLOB_ONLYDIR);
            foreach ($dirs as $dir) {
                $exe = $dir . '\\bin\\mysqldump.exe';
                if (file_exists($exe)) return $exe;
            }
        }

        return null;
    }

    /**
     * Elimina backups más antiguos que $days días.
     */
    private function limpiarBackupsAntiguos(string $directory, int $days): void
    {
        $archivos = glob("{$directory}/backup_*.sql");
        $limite   = now()->subDays($days)->timestamp;
        $eliminados = 0;

        foreach ($archivos as $archivo) {
            if (filemtime($archivo) < $limite) {
                unlink($archivo);
                $eliminados++;
            }
        }

        if ($eliminados > 0) {
            $this->info("✓ {$eliminados} backup(s) antiguo(s) eliminado(s).");
        }
    }
}
