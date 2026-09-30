@echo off
title Sistema de Calificaciones - Servidor Publico (Cloudflare)
color 0B

echo ========================================================
echo   SISTEMA DE CALIFICACIONES - ENLACE PUBLICO GLOBAL
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
        echo Por favor instale XAMPP o PHP.
        pause
        exit /b 1
    )
)

:: Verificar cloudflared
if not exist "scratch\cloudflared.exe" (
    echo [ADVERTENCIA] No se encontro scratch\cloudflared.exe.
    echo Descargue cloudflared.exe desde https://github.com/cloudflare/cloudflared/releases
    echo y coloquelo en la carpeta 'scratch'.
    pause
    exit /b 1
)

echo [1/2] Iniciando Servidor PHP en puerto 8080...
start /b "" "%PHP_CMD%" -S 127.0.0.1:8080 router.php

timeout /t 2 >nul

echo [2/2] Generando Enlace Publico HTTPS con Cloudflare...
echo.
echo ========================================================
echo  BUSQUE EN LA PANTALLA EL ENLACE QUE TERMINA EN:
echo  .trycloudflare.com
echo  Ese es el enlace para compartir con otros usuarios!
echo ========================================================
echo.

scratch\cloudflared.exe tunnel --url http://127.0.0.1:8080
pause
