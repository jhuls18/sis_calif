@echo off
title Sistema de Calificaciones - Servidor Local
color 0A

echo ========================================================
echo         SISTEMA DE CALIFICACIONES Y ATENCION
echo ========================================================
echo.

:: Detectar ejecutable de PHP
set PHP_CMD=php
where php >nul 2>nul
if %errorlevel% neq 0 (
    if exist "C:\xampp\php\php.exe" (
        set PHP_CMD=C:\xampp\php\php.exe
    ) else (
        echo [ERROR] No se encontro PHP en el sistema.
        echo Por favor instale XAMPP o PHP y agreguelo al PATH.
        pause
        exit /b 1
    )
)

echo [OK] Usando PHP desde: %PHP_CMD%
echo.

:: Obtener direccion IP local
echo Direcciones IP para conectar otros dispositivos en la misma red WiFi:
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /c:"IPv4"') do (
    echo   - http:%%a:8080
)
echo.
echo Presione CTRL+C para detener el servidor.
echo ========================================================
echo.

%PHP_CMD% -S 0.0.0.0:8080 router.php
pause
