@echo off
chcp 65001 >nul
title SISGECLIN v3.0 - Deploy / Setup

:: ============================================================
:: Verificar si se esta ejecutando como Administrador
:: Si no, relanzar con elevacion UAC automaticamente
:: ============================================================
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo Solicitando permisos de Administrador...
    powershell -Command "Start-Process "%~f0" -Verb RunAs"
    exit /b
)

:: ============================================================
:: Cabecera
:: ============================================================
color 0A
echo.
echo  =================================================================
echo    SISGECLIN v3.0 ^| Maxical C.A. ^| Deploy Seguro
echo    Sistema de Gestion Clinica Ocupacional
echo  =================================================================
echo.

:: ============================================================
:: Detectar PHP de Laragon automaticamente
:: ============================================================
set "PHP_EXE="

:: Buscar en el PATH del sistema primero
where php >nul 2>&1
if %errorlevel% equ 0 (
    set "PHP_EXE=php"
    echo  [OK] PHP encontrado en PATH del sistema
    goto :php_found
)

:: Buscar en Laragon bin\php dinamicamente
for /d %%D in (C:\laragon\bin\php\php-*) do (
    if exist "%%D\php.exe" (
        set "PHP_EXE=%%D\php.exe"
        echo  [OK] PHP encontrado en Laragon: %%D\php.exe
        goto :php_found
    )
)

:: PHP no encontrado
echo  [ERROR] PHP no encontrado.
echo  Asegurate de tener Laragon instalado en C:\laragon
echo  o agrega PHP al PATH del sistema.
echo.
pause
exit /b 1

:php_found
echo.

:: ============================================================
:: Verificar que deploy.php existe
:: ============================================================
set "DEPLOY_DIR=%~dp0"
set "DEPLOY_PHP=%DEPLOY_DIR%desplegar.php"

if not exist "%DEPLOY_PHP%" (
    echo  [ERROR] No se encontro desplegar.php en:
    echo  %DEPLOY_PHP%
    echo.
    pause
    exit /b 1
)

:: ============================================================
:: Ejecutar deploy.php
:: ============================================================
echo  Iniciando deploy desde: %DEPLOY_DIR%
echo  ---------------------------------------------------------------
echo.

"%PHP_EXE%" "%DEPLOY_PHP%"

:: ============================================================
:: Finalizar
:: ============================================================
echo.
echo  ---------------------------------------------------------------
if %errorlevel% equ 0 (
    echo  [COMPLETADO] Deploy ejecutado correctamente.
) else (
    echo  [ADVERTENCIA] El deploy finalizo con errores. Revisa el log.
)
echo.
echo  Presiona cualquier tecla para cerrar esta ventana...
pause >nul
