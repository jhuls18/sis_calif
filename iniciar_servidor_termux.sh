#!/bin/sh
echo "========================================================"
echo "   SISTEMA DE CALIFICACIONES - SERVIDOR TABLET (TERMUX)"
echo "========================================================"
echo ""
echo "[1/2] Verificando PHP..."
if ! command -v php >/dev/null 2>&1; then
    echo "[!] Instalando PHP en Termux..."
    pkg update -y && pkg install -y php
fi

echo "[2/2] Iniciando Servidor Web local en puerto 8080..."
echo "Abre tu navegador en la tableta e ingresa:"
echo "👉 http://localhost:8080"
echo ""
echo "Presiona CTRL+C para detener el servidor."
echo "========================================================"
php -S 0.0.0.0:8080 router.php
