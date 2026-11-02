@echo off
chcp 65001 >nul 2>&1
title SISGECLIN - Instalador Automatico del Sistema (Win 7 / 8 / 10 / 11)

:: Asegurar que el directorio de trabajo sea siempre la carpeta del proyecto
cd /d "%~dp0"
set "APP_DIR=%~dp0"

color 0A
cls
echo.
echo  ====================================================================
echo    SISGECLIN - INSTALADOR AUTOMATICO DEL SISTEMA
echo    Compatible con Windows 7, Windows 8, Windows 10 y Windows 11
echo  ====================================================================
echo.
echo  Este instalador configurara automaticamente:
echo    1. Variables de entorno .env, llave de seguridad APP_KEY y carpetas
echo    2. Creacion automatica de la Base de Datos en MySQL: sisgeclin_laravel
echo    3. Instalacion de todas las tablas del sistema - migraciones
echo    4. Creacion del usuario Administrador por defecto: admin / admin123
echo    5. Enlace de archivos escaneados storage y limpieza de cache
echo.
echo  NOTA: Asegurate de tener MySQL iniciado en Laragon o XAMPP.
echo.
echo  Presiona cualquier tecla para iniciar la instalacion...
pause >nul

:: ============================================================================
:: 1. DETECTAR PHP AUTOMATICAMENTE (Laragon, XAMPP o PATH del sistema)
:: ============================================================================
echo.
echo  --------------------------------------------------------------------
echo  [1/6] Detectando interprete PHP...
echo  --------------------------------------------------------------------
set "PHP_EXE="

:: 1a. Buscar en Laragon relativo a la carpeta actual
for /d %%D in ("%APP_DIR%..\..\bin\php\php-8.3*" "%APP_DIR%..\..\bin\php\php-8.2*" "%APP_DIR%..\..\bin\php\php*") do if exist "%%~fD\php.exe" set "PHP_EXE=%%~fD\php.exe" & goto :php_ready

:: 1b. Buscar en C:\laragon, D:\laragon y E:\laragon
for %%L in (C D E) do for /d %%D in ("%%L:\laragon\bin\php\php-8.3*" "%%L:\laragon\bin\php\php-8.2*" "%%L:\laragon\bin\php\php*") do if exist "%%~fD\php.exe" set "PHP_EXE=%%~fD\php.exe" & goto :php_ready

:: 1c. Buscar en XAMPP (C:\xampp, D:\xampp, E:\xampp)
for %%L in (C D E) do if exist "%%L:\xampp\php\php.exe" set "PHP_EXE=%%L:\xampp\php\php.exe" & goto :php_ready

:: 1d. Buscar en el PATH global de Windows
where php >nul 2>&1
if not errorlevel 1 set "PHP_EXE=php" & goto :php_ready

color 0C
echo.
echo  [ERROR] No se encontro PHP en esta computadora.
echo  Asegurate de tener instalado Laragon en C:\laragon o XAMPP en C:\xampp.
echo.
pause
exit /b 1

:php_ready
echo  [OK] PHP detectado: "%PHP_EXE%"

:: ============================================================================
:: 2. VERIFICAR ARCHIVO .env, CARPETAS Y DEPENDENCIAS VENDOR
:: ============================================================================
echo.
echo  --------------------------------------------------------------------
echo  [2/6] Preparando entorno, directorios y llave del sistema...
echo  --------------------------------------------------------------------

if exist "%APP_DIR%.env" goto :env_ok
if not exist "%APP_DIR%.env.example" goto :env_missing
copy /y "%APP_DIR%.env.example" "%APP_DIR%.env" >nul
echo  [OK] Archivo .env creado a partir de .env.example
goto :env_done

:env_missing
color 0C
echo  [ERROR] No se encontro el archivo .env ni .env.example
pause
exit /b 1

:env_ok
echo  [OK] Archivo .env verificado.

:env_done
:: Crear directorios obligatorios de Laravel y almacenamiento de escaneos/PDF
if not exist "%APP_DIR%storage\app\public\recetas-escaneos" mkdir "%APP_DIR%storage\app\public\recetas-escaneos" >nul 2>&1
if not exist "%APP_DIR%storage\app\public\informes-escaneos" mkdir "%APP_DIR%storage\app\public\informes-escaneos" >nul 2>&1
if not exist "%APP_DIR%storage\app\public\gastos-escaneos" mkdir "%APP_DIR%storage\app\public\gastos-escaneos" >nul 2>&1
if not exist "%APP_DIR%storage\app\public\examenes-escaneos" mkdir "%APP_DIR%storage\app\public\examenes-escaneos" >nul 2>&1
if not exist "%APP_DIR%storage\app\mpdf" mkdir "%APP_DIR%storage\app\mpdf" >nul 2>&1
if not exist "%APP_DIR%storage\framework\cache\data" mkdir "%APP_DIR%storage\framework\cache\data" >nul 2>&1
if not exist "%APP_DIR%storage\framework\sessions" mkdir "%APP_DIR%storage\framework\sessions" >nul 2>&1
if not exist "%APP_DIR%storage\framework\views" mkdir "%APP_DIR%storage\framework\views" >nul 2>&1
if not exist "%APP_DIR%storage\logs" mkdir "%APP_DIR%storage\logs" >nul 2>&1
if not exist "%APP_DIR%bootstrap\cache" mkdir "%APP_DIR%bootstrap\cache" >nul 2>&1
echo  [OK] Estructura de carpetas storage y cache verificada.

:: Verificar vendor/autoload.php
if exist "%APP_DIR%vendor\autoload.php" goto :vendor_ok
echo  [*] Carpeta vendor no encontrada. Buscando Composer...
set "COMPOSER_CMD=composer"
if exist "C:\laragon\bin\composer\composer.bat" set "COMPOSER_CMD=C:\laragon\bin\composer\composer.bat"
if exist "%APP_DIR%..\..\bin\composer\composer.bat" set "COMPOSER_CMD=%APP_DIR%..\..\bin\composer\composer.bat"
call "%COMPOSER_CMD%" install --no-interaction --prefer-dist --optimize-autoloader
if errorlevel 1 goto :vendor_err
goto :vendor_ok

:vendor_err
color 0C
echo  [ERROR] No se pudieron instalar las dependencias de la carpeta vendor.
pause
exit /b 1

:vendor_ok
echo  [OK] Dependencias del sistema vendor listas.

:: Verificar APP_KEY y generarla si esta vacia
"%PHP_EXE%" -r "$env = file_get_contents('.env'); if (preg_match('/^APP_KEY=base64:.+/m', $env) === 1) { exit(0); } exit(1);"
if not errorlevel 1 goto :key_ok
echo  [*] Generando llave de seguridad APP_KEY...
"%PHP_EXE%" artisan key:generate --force
goto :key_done

:key_ok
echo  [OK] Llave de seguridad APP_KEY configurada.
:key_done

:: ============================================================================
:: 3. CREAR LA BASE DE DATOS EN MYSQL AUTOMATICAMENTE SI NO EXISTE
:: ============================================================================
echo.
echo  --------------------------------------------------------------------
echo  [3/6] Conectando con MySQL y verificando Base de Datos...
echo  --------------------------------------------------------------------

"%PHP_EXE%" -r "$lines = @file('.env') ?: []; $cfg = []; foreach ($lines as $l) { $l = trim($l); if ($l === '' || $l[0] === '#' || strpos($l, '=') === false) continue; list($k, $v) = explode('=', $l, 2); $cfg[trim($k)] = trim($v, ' ' . chr(34) . chr(39)); } $host = isset($cfg['DB_HOST']) && $cfg['DB_HOST'] !== '' ? $cfg['DB_HOST'] : '127.0.0.1'; $port = isset($cfg['DB_PORT']) && $cfg['DB_PORT'] !== '' ? $cfg['DB_PORT'] : '3306'; $db = isset($cfg['DB_DATABASE']) && $cfg['DB_DATABASE'] !== '' ? $cfg['DB_DATABASE'] : 'sisgeclin_laravel'; $user = isset($cfg['DB_USERNAME']) && $cfg['DB_USERNAME'] !== '' ? $cfg['DB_USERNAME'] : 'root'; $pass = isset($cfg['DB_PASSWORD']) ? $cfg['DB_PASSWORD'] : ''; try { $pdo = new PDO('mysql:host=' . $host . ';port=' . $port . ';charset=utf8mb4', $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]); $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . $db . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'); echo '  [OK] Base de datos ' . $db . ' verificada/creada exitosamente en MySQL (' . $host . ':' . $port . ').' . PHP_EOL; exit(0); } catch (Exception $e) { echo '  [ERROR MYSQL] ' . $e->getMessage() . PHP_EOL; exit(1); }"

if not errorlevel 1 goto :db_ok
color 0E
echo.
echo  [ADVERTENCIA] No se pudo conectar a MySQL.
echo    1. Abre Laragon o XAMPP y asegurate de iniciar el servicio MySQL.
echo    2. Verifica DB_HOST, DB_USERNAME y DB_PASSWORD en el archivo .env
echo    3. Vuelve a ejecutar instalar_sistema.bat
echo.
pause
exit /b 1

:db_ok

:: ============================================================================
:: 4. EJECUTAR MIGRACIONES DE LA BASE DE DATOS
:: ============================================================================
echo.
echo  --------------------------------------------------------------------
echo  [4/6] Instalando tablas y estructura de la Base de Datos...
echo  --------------------------------------------------------------------

"%PHP_EXE%" artisan migrate --force
if errorlevel 1 goto :migrate_err
echo  [OK] Todas las tablas de la base de datos estan instaladas y al dia.
goto :migrate_ok

:migrate_err
color 0C
echo.
echo  [ERROR] Ocurrio un problema al ejecutar las migraciones.
echo  Revisa el mensaje superior para mas detalles.
echo.
pause
exit /b 1

:migrate_ok

:: ============================================================================
:: 5. CREAR USUARIO ADMINISTRADOR (admin / admin123) Y CATALOGOS BASE
:: ============================================================================
echo.
echo  --------------------------------------------------------------------
echo  [5/6] Configurando usuario Administrador y servicios medicos base...
echo  --------------------------------------------------------------------

"%PHP_EXE%" artisan db:seed --class=AdminSeeder --force
if errorlevel 1 goto :seed_err

"%PHP_EXE%" artisan db:seed --class=ServicioMedicoSeeder --force >nul 2>&1
echo  [OK] Usuario Administrador configurado - Usuario: admin / Contrasena: admin123
echo  [OK] Catalogo base de servicios medicos verificado.
goto :seed_ok

:seed_err
color 0C
echo  [ERROR] No se pudo crear el usuario administrador.
pause
exit /b 1

:seed_ok

:: ============================================================================
:: 6. ENLACE SIMBOLICO STORAGE Y LIMPIEZA DE CACHE
:: ============================================================================
echo.
echo  --------------------------------------------------------------------
echo  [6/6] Configurando acceso a archivos storage y limpiando cache...
echo  --------------------------------------------------------------------

if exist "%APP_DIR%public\storage" goto :storage_linked
mklink /J "%APP_DIR%public\storage" "%APP_DIR%storage\app\public" >nul 2>&1
if errorlevel 1 "%PHP_EXE%" artisan storage:link >nul 2>&1

:storage_linked
echo  [OK] Enlace public\storage configurado.

"%PHP_EXE%" artisan optimize:clear >nul 2>&1
echo  [OK] Cache de vistas, rutas y configuracion limpiada.

:: ============================================================================
:: RESUMEN FINAL
:: ============================================================================
color 0A
echo.
echo  ====================================================================
echo    INSTALACION COMPLETADA EXITOSAMENTE
echo  ====================================================================
echo.
echo    Base de Datos : sisgeclin_laravel - Operativa
echo    URL de Acceso : http://localhost/SISGECLIN/public
echo.
echo    CREDENCIALES DE ACCESO POR DEFECTO:
echo      Usuario     : admin
echo      Contrasena  : admin123
echo.
echo  ====================================================================
echo.
echo  Presiona cualquier tecla para finalizar y cerrar esta ventana...
pause >nul
exit /b 0
