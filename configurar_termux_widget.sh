#!/bin/sh
# Script inteligente para registrar el acceso directo en Termux:Widget

mkdir -p ~/.shortcuts

cat << 'EOF' > ~/.shortcuts/INICIAR_SERVIDOR.sh
#!/bin/sh

# Buscador Inteligente de la carpeta del proyecto en cualquier ubicacion de la tablet
find_script() {
    for d in \
        "$HOME/sis_calif" \
        "/sdcard/sis_calif" \
        "/sdcard/Download/sis_calif" \
        "/sdcard/Download/vuela/sis_calif" \
        "/sdcard/Downloads/sis_calif" \
        "/storage/emulated/0/Download/vuela/sis_calif" \
        "/storage/emulated/0/Download/sis_calif" \
        "/storage/emulated/0/sis_calif"
    do
        if [ -f "$d/iniciar_servidor_android.sh" ]; then
            echo "$d/iniciar_servidor_android.sh"
            return 0
        fi
    done

    FOUND=$(find /sdcard/ /storage/emulated/0/ $HOME/ -name "iniciar_servidor_android.sh" 2>/dev/null | head -n 1)
    if [ -n "$FOUND" ]; then
        echo "$FOUND"
        return 0
    fi

    return 1
}

SCRIPT_PATH=$(find_script)

if [ -n "$SCRIPT_PATH" ] && [ -f "$SCRIPT_PATH" ]; then
    sh "$SCRIPT_PATH"
else
    echo "[ERROR] No se pudo encontrar 'iniciar_servidor_android.sh'."
    echo "Asegurate de haber copiado la carpeta 'sis_calif' en la tablet."
    read -r _
fi
EOF

chmod +x ~/.shortcuts/INICIAR_SERVIDOR.sh
if [ -f "iniciar_servidor_android.sh" ]; then
    chmod +x iniciar_servidor_android.sh
fi

echo "========================================================"
echo " ¡CONFIGURACION INTELIGENTE COMPLETADA CON EXITO!"
echo "========================================================"
echo " El acceso directo 'INICIAR_SERVIDOR.sh' ha sido guardado."
echo " Funcionara sin importar en que carpeta guardes la app."
echo "========================================================"
