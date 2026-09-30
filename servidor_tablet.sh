#!/bin/sh
# ========================================================
# SISTEMA DE CALIFICACIONES - SERVIDOR TABLET ANDROID
# ========================================================

find_project_dir() {
    if [ -f "./router.php" ] && [ -f "./index.php" ]; then
        pwd
        return 0
    fi
    for d in "$HOME/sis_calif" "/storage/emulated/0/vuela/sis_calif" "/storage/emulated/0/Download/vuela/sis_calif" "/sdcard/sis_calif"; do
        if [ -d "$d" ] && [ -f "$d/router.php" ]; then
            echo "$d"
            return 0
        fi
    done
    FOUND=$(find /sdcard/ /storage/emulated/0/ $HOME/ -name "router.php" 2>/dev/null | head -n 1)
    if [ -n "$FOUND" ]; then
        dirname "$FOUND"
        return 0
    fi
    return 1
}

TARGET_DIR=$(find_project_dir)
if [ -z "$TARGET_DIR" ] || [ ! -d "$TARGET_DIR" ]; then
    echo "[ERROR] No se encontro la carpeta del proyecto 'sis_calif'."
    exit 1
fi

cd "$TARGET_DIR" || exit 1

clear
echo "========================================================"
echo "    SISTEMA DE CALIFICACIONES - SERVIDOR EN TABLET"
echo "========================================================"
echo " [OK] Carpeta detectada en: $TARGET_DIR"
echo "========================================================"
echo ""

if ! command -v php >/dev/null 2>&1 || ! command -v ssh >/dev/null 2>&1; then
    echo "[!] Instalando PHP y SSH en Termux..."
    pkg update -y && pkg install -y php openssh curl wget
fi

# Matar cualquier proceso PHP viejo en la tablet para tener puerto 8080 100% limpio
pkill -9 -f "php" >/dev/null 2>&1
sleep 1

# Iniciar servidor PHP en puerto 8080
php -S 0.0.0.0:8080 router.php >/dev/null 2>&1 &
PHP_PID=$!
disown $PHP_PID 2>/dev/null
sleep 2

LOCAL_IPS=$(ifconfig 2>/dev/null | grep -E 'inet ' | grep -v '127.0.0.1' | awk '{print $2}' | sed 's/addr://')

echo "--------------------------------------------------------"
echo " 📌 ENLACES RED WIFI LOCAL (Sin Internet):"
echo " 👉 http://127.0.0.1:8080 (En esta tablet)"
if [ -n "$LOCAL_IPS" ]; then
    for ip in $LOCAL_IPS; do
        echo " 👉 http://$ip:8080"
    done
fi
echo "--------------------------------------------------------"
echo ""

if command -v termux-open-url >/dev/null 2>&1; then
    termux-open-url "http://127.0.0.1:8080"
fi

echo "🌐 GENERANDO ENLACE PUBLICO GLOBAL PARA INTERNET..."
echo "--------------------------------------------------------"
echo " Busca abajo el enlace HTTPS que termina en .lhr.life"
echo "--------------------------------------------------------"
echo ""

ssh -o StrictHostKeyChecking=no -R 80:127.0.0.1:8080 nokey@localhost.run
