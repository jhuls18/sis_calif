<?php
declare(strict_types=1);

final class Sheets
{
    public static function url(): string
    {
        $cfg = Store::read('config');
        return trim((string)($cfg['google_script_url'] ?? ''));
    }

    public static function enabled(): bool
    {
        return self::url() !== '';
    }

    public static function request(array $payload): ?array
    {
        $url = self::url();
        if ($url === '') {
            return null;
        }
        
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $action = (string)($payload['action'] ?? '');

        // 1. Intento GET (Máxima compatibilidad con redirecciones 302 de Google Apps Script)
        $getUrl = $url . (str_contains($url, '?') ? '&' : '?') . 'action=' . urlencode($action) . '&data=' . urlencode($json);
        
        $ch = curl_init($getUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_CONNECTTIMEOUT => 4,
        ]);
        $raw = curl_exec($ch);
        curl_close($ch);

        if (is_string($raw) && $raw !== '') {
            $data = json_decode($raw, true);
            if (is_array($data) && !empty($data['ok'])) {
                return $data;
            }
        }

        // 2. Fallback POST en caso de que GET falle
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $json,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_POSTREDIR => CURL_REDIR_POST_ALL,
                CURLOPT_TIMEOUT => 6,
                CURLOPT_CONNECTTIMEOUT => 4,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $raw = curl_exec($ch);
            curl_close($ch);
            if (is_string($raw) && $raw !== '') {
                $data = json_decode($raw, true);
                if (is_array($data)) return $data;
            }
        }

        return ['ok' => false, 'error' => 'Sin respuesta de Google Apps Script.'];
    }

    public static function testConnection(): array
    {
        if (!self::enabled()) {
            return ['ok' => false, 'error' => 'No ha configurado una URL de Google Apps Script.'];
        }
        $testRow = [
            'fecha' => date('d/m/Y H:i:s'),
            'codigo' => 'TEST-PRUEBA',
            'tipo' => 'cliente',
            'encuesta_tipo' => 'Prueba de Conexión',
            'visita_nro' => 1,
            'score' => 5,
            'etiqueta' => 'Excelente',
            'puntos' => 1,
            'agente_nombre' => 'Sistema Vuela',
            'rol' => 'ADMIN',
            'ip' => client_ip(),
        ];
        $res = self::request(['action' => 'saveRating', 'row' => $testRow]);
        if (is_array($res) && !empty($res['ok'])) {
            return ['ok' => true, 'mensaje' => '¡Conexión Exitosa! Se insertó una fila de prueba en tu Hoja de Cálculo.'];
        }
        $err = $res['error'] ?? 'No se pudo conectar con Google Sheets.';
        return ['ok' => false, 'error' => $err];
    }

    public static function pushRating(array $row): void
    {
        if (!self::enabled()) {
            return;
        }
        try {
            self::request(['action' => 'saveRating', 'row' => $row]);
        } catch (Throwable $e) {
            // local-first: no bloquear la experiencia de calificación
        }
    }

    public static function pullRatings(): ?array
    {
        if (!self::enabled()) {
            return null;
        }
        $res = self::request(['action' => 'getRatings']);
        if (!$res || empty($res['ok'])) {
            return null;
        }
        return $res['ratings'] ?? null;
    }
}
