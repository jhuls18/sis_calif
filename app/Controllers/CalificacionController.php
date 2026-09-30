<?php
declare(strict_types=1);

final class CalificacionController
{
    public static function form(): void
    {
        $u = require_login();
        $cfg = Store::read('config');
        $n = (int)($cfg['niveles_caritas'] ?? 5);
        $customs = $cfg['cuestionarios'] ?? [];

        $defaultCuest = [
            'id' => 'cuest-gen',
            'titulo' => 'Atención General',
            'pregunta' => '¿Cómo califica la atención recibida hoy en caja u oficina?',
            'escala' => $n,
            'icono' => 'fa-headset'
        ];

        $hasGen = false;
        foreach ($customs as $c) {
            if (($c['titulo'] ?? '') === 'Atención General' || ($c['id'] ?? '') === 'cuest-gen') {
                $hasGen = true;
                break;
            }
        }
        if (!$hasGen) {
            array_unshift($customs, $defaultCuest);
        }

        $firstScale = (int)($customs[0]['escala'] ?? $n);

        view('calificar', [
            'title' => 'Calificar atención',
            'user' => $u,
            'cfg' => $cfg,
            'niveles' => niveles_activos($firstScale),
            'cuestionarios' => $customs,
            'niveles_all' => niveles_activos(11),
            'max' => $firstScale,
            'extraJs' => 'calificar.js',
            'error' => flash('error'),
        ]);
    }

    public static function apiCliente(): void
    {
        require_login();
        $codigo = Cliente::normalize((string)($_GET['codigo'] ?? ''));
        $c = $codigo ? Cliente::lookup($codigo) : null;
        json_out([
            'ok' => true,
            'existe' => (bool)$c,
            'cliente' => $c,
        ]);
    }

    public static function guardar(): void
    {
        require_login();
        csrf_verify();
        $res = Calificacion::registrar([
            'codigo' => $_POST['codigo'] ?? '',
            'tipo' => $_POST['tipo'] ?? 'cliente',
            'score' => $_POST['score'] ?? 0,
            'encuesta_tipo' => $_POST['encuesta_tipo'] ?? 'Atención General',
        ]);
        if (empty($res['ok'])) {
            flash('error', $res['error'] ?? 'No se pudo guardar.');
            redirect('/calificar');
        }
        $_SESSION['last_rating'] = $res['rating'];
        $_SESSION['last_cliente'] = $res['cliente'];
        redirect('/calificar/ok');
    }

    public static function ok(): void
    {
        require_login();
        $rating = $_SESSION['last_rating'] ?? null;
        if (!$rating) {
            redirect('/calificar');
        }
        view('calificar_ok', [
            'title' => 'Calificación registrada',
            'rating' => $rating,
            'cliente' => $_SESSION['last_cliente'] ?? null,
        ]);
    }

    public static function apiCrearTokenLink(): void
    {
        $u = require_login();
        $encuesta = trim((string)($_POST['encuesta_tipo'] ?? 'Atención General'));
        $duration = 300; // 5 minutos de validez estricta
        $now = time();
        $expiresAt = $now + $duration;
        $token = bin2hex(random_bytes(16));

        $tokens = Store::read('tokens');
        if (!is_array($tokens)) {
            $tokens = [];
        }

        // Limpiar tokens con más de 10 minutos de antigüedad
        $tokens = array_values(array_filter($tokens, function ($t) use ($now) {
            return (int)($t['expires_at'] ?? 0) > ($now - 600);
        }));

        $record = [
            'token' => $token,
            'agente_id' => $u['id'] ?? '',
            'agente_usuario' => $u['usuario'] ?? '',
            'agente_nombre' => $u['nombre'] ?? 'Agente',
            'agente_rol' => $u['rol'] ?? 'ATC',
            'encuesta_tipo' => $encuesta,
            'created_at' => $now,
            'expires_at' => $expiresAt,
            'usado' => false,
        ];

        $tokens[] = $record;
        Store::write('tokens', $tokens);

        $linkUrl = url('/calificar/link?t=' . $token);

        json_out([
            'ok' => true,
            'token' => $token,
            'url' => $linkUrl,
            'expires_at' => $expiresAt,
            'expires_in' => $duration,
            'agente_nombre' => $u['nombre'] ?? 'Agente',
            'agente_rol' => $u['rol'] ?? 'ATC',
            'encuesta_tipo' => $encuesta,
        ]);
    }

    public static function formLink(): void
    {
        no_cache_headers();
        $tokenStr = trim((string)($_GET['t'] ?? ''));
        if ($tokenStr === '') {
            view_plain('calificar_expired', ['error' => 'Enlace no especificado.']);
            return;
        }

        $tokens = Store::read('tokens');
        $found = null;
        $now = time();

        if (is_array($tokens)) {
            foreach ($tokens as $t) {
                if (($t['token'] ?? '') === $tokenStr) {
                    $found = $t;
                    break;
                }
            }
        }

        if (!$found || (int)($found['expires_at'] ?? 0) < $now) {
            view_plain('calificar_expired', ['error' => 'Este enlace de calificación ha expirado (Validez: 5 minutos).']);
            return;
        }

        $cfg = Store::read('config');
        $n = (int)($cfg['niveles_caritas'] ?? 5);
        $encuestaTipo = $found['encuesta_tipo'] ?? 'Atención General';

        $escala = $n;
        $pregunta = '¿Cómo califica la atención recibida hoy en caja u oficina?';
        $customs = $cfg['cuestionarios'] ?? [];
        foreach ($customs as $c) {
            if (($c['titulo'] ?? '') === $encuestaTipo) {
                $escala = (int)($c['escala'] ?? $n);
                $pregunta = $c['pregunta'] ?? $pregunta;
                break;
            }
        }

        view_plain('calificar_public', [
            'title' => 'Calificar atención de ' . ($found['agente_nombre'] ?? 'Agente') . ' - Vuela Internet',
            'agentName' => $found['agente_nombre'] ?? 'Agente',
            'agentRol' => $found['agente_rol'] ?? 'ATC',
            'token' => $found,
            'cfg' => $cfg,
            'pregunta' => $pregunta,
            'niveles' => niveles_activos($escala),
            'niveles_all' => niveles_activos(11),
            'max' => $escala,
            'encuesta_tipo' => $encuestaTipo,
        ]);
    }

    public static function guardarLink(): void
    {
        no_cache_headers();
        $tokenStr = trim((string)($_POST['token'] ?? ''));
        $tokens = Store::read('tokens');
        $found = null;
        $now = time();

        if (is_array($tokens)) {
            foreach ($tokens as $t) {
                if (($t['token'] ?? '') === $tokenStr) {
                    $found = $t;
                    break;
                }
            }
        }

        if (!$found || (int)($found['expires_at'] ?? 0) < $now) {
            view_plain('calificar_expired', ['error' => 'El enlace expiró antes de enviar su calificación.']);
            return;
        }

        $res = Calificacion::registrar([
            'codigo' => $_POST['codigo'] ?? '',
            'tipo' => $_POST['tipo'] ?? 'visitante',
            'score' => $_POST['score'] ?? 0,
            'encuesta_tipo' => $found['encuesta_tipo'] ?? 'Atención General',
            'agente_id' => $found['agente_id'],
            'agente_usuario' => $found['agente_usuario'] ?? '',
            'agente_nombre' => $found['agente_nombre'],
            'rol' => $found['agente_rol'],
        ]);

        if (empty($res['ok'])) {
            view_plain('calificar_public', [
                'title' => 'Calificar atención',
                'agentName' => $found['agente_nombre'] ?? 'Agente',
                'agentRol' => $found['agente_rol'] ?? 'ATC',
                'token' => $found,
                'cfg' => Store::read('config'),
                'pregunta' => '¿Cómo califica la atención recibida?',
                'niveles' => niveles_activos(5),
                'niveles_all' => niveles_activos(11),
                'max' => 5,
                'encuesta_tipo' => $found['encuesta_tipo'] ?? 'Atención General',
                'error' => $res['error'] ?? 'Error al registrar la calificación.',
            ]);
            return;
        }

        view_plain('calificar_public_ok', [
            'title' => '¡Gracias por su calificación!',
            'rating' => $res['rating'] ?? null,
            'cliente' => $res['cliente'] ?? null,
            'agentName' => $found['agente_nombre'] ?? 'Agente',
        ]);
    }
}
