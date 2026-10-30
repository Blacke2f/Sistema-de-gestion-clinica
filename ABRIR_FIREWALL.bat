@echo off
echo ============================================
echo  SISGECLIN - Abrir Puerto 80 en Firewall
echo ============================================
echo.

:: Verificar si se ejecuta como administrador
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: Ejecuta este archivo como Administrador.
    echo Clic derecho sobre el archivo ^> "Ejecutar como administrador"
    pause
    exit /b 1
)

echo Agregando regla de firewall para Apache (Puerto 80)...
netsh advfirewall firewall add rule name="XAMPP Apache HTTP" dir=in action=allow protocol=TCP localport=80

echo.
echo Agregando regla para el ejecutable de Apache...
netsh advfirewall firewall add rule name="XAMPP Apache EXE" dir=in action=allow program="E:\Xampp\apache\bin\httpd.exe"

echo.
echo ============================================
echo  LISTO. Ahora puedes acceder desde:
echo  http://192.168.1.3/SISGECLIN/public
echo ============================================
echo.
pause
