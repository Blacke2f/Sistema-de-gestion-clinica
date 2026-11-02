@echo off
chcp 65001 >nul 2>&1
title Mantenimiento SISGECLIN - Limpieza de Cache (Win 7 / 8 / 10 / 11)

cd /d "%~dp0"
set "APP_DIR=%~dp0"

color 0B
cls
echo ====================================================================
echo          SISTEMA DE GESTION CLINICA SISGECLIN
echo            HERRAMIENTA DE LIMPIEZA DE CACHE TOTAL
echo ====================================================================
echo.
echo Este script limpia y reconstruye las caches temporales para resolver:
echo  - Cambios en el archivo .env que no surten efecto
echo  - Vistas o pantallas que muestran versiones en cache
echo  - Sesiones o rutas desactualizadas tras mover el sistema de servidor
echo.
echo Presiona cualquier tecla para iniciar la limpieza...
pause >nul

:: Detectar PHP automaticamente
set "PHP_EXE="
for /d %%D in ("%APP_DIR%..\..\bin\php\php-8.3*" "%APP_DIR%..\..\bin\php\php-8.2*" "%APP_DIR%..\..\bin\php\php*") do if exist "%%~fD\php.exe" set "PHP_EXE=%%~fD\php.exe" & goto :php_ok
for %%L in (C D E) do for /d %%D in ("%%L:\laragon\bin\php\php-8.3*" "%%L:\laragon\bin\php\php-8.2*" "%%L:\laragon\bin\php\php*") do if exist "%%~fD\php.exe" set "PHP_EXE=%%~fD\php.exe" & goto :php_ok
for %%L in (C D E) do if exist "%%L:\xampp\php\php.exe" set "PHP_EXE=%%L:\xampp\php\php.exe" & goto :php_ok

where php >nul 2>&1
if not errorlevel 1 set "PHP_EXE=php" & goto :php_ok

color 0C
echo.
echo [ERROR] No se encontro PHP en el sistema ni en Laragon/XAMPP.
pause
exit /b 1

:php_ok
echo.
echo [1/5] Limpiando cache de rutas...
"%PHP_EXE%" artisan route:clear

echo.
echo [2/5] Limpiando cache de configuracion...
"%PHP_EXE%" artisan config:clear

echo.
echo [3/5] Limpiando cache de vistas compiladas...
"%PHP_EXE%" artisan view:clear

echo.
echo [4/5] Limpiando cache general de la aplicacion...
"%PHP_EXE%" artisan cache:clear

echo.
echo [5/5] Limpiando cache de arranque...
"%PHP_EXE%" artisan optimize:clear

color 0A
echo.
echo ====================================================================
echo                     LIMPIEZA COMPLETADA!
echo ====================================================================
echo Todo el sistema se ha refrescado correctamente.
echo Ya puedes recargar la pagina en tu navegador con Ctrl + F5.
echo.
pause
exit /b 0
