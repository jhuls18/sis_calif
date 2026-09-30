<?php
declare(strict_types=1);

function e(?string $v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = '/'): string
{
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    $path = '/' . ltrim($path, '/');
    return rtrim(BASE_URL, '/') . $path;
}

function asset(string $path): string
{
    return url('public/' . ltrim($path, '/'));
}

function redirect(string $path): void
{
    header('Location: ' . url($path), true, 303);
    exit;
}


function csrf_token(): string
{
    if (empty($_SESSION[CSRF_KEY])) {
        $_SESSION[CSRF_KEY] = bin2hex(random_bytes(16));
    }
    return $_SESSION[CSRF_KEY];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $ok = isset($_POST['csrf'], $_SESSION[CSRF_KEY])
        && hash_equals($_SESSION[CSRF_KEY], (string)$_POST['csrf']);
    if (!$ok) {
        http_response_code(419);
        exit('Sesión expirada. Recargue la página.');
    }
}

function json_out(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_login(): array
{
    $u = auth_user();
    if (!$u) {
        redirect('/login');
    }
    return $u;
}

function require_admin(): array
{
    $u = require_login();
    if (($u['rol'] ?? '') !== 'Administrador') {
        redirect('/panel');
    }
    return $u;
}

function is_admin(?array $u = null): bool
{
    $u = $u ?? auth_user();
    return ($u['rol'] ?? '') === 'Administrador';
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return $value;
    }
    $msg = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $msg;
}

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

function uuid(): string
{
    return bin2hex(random_bytes(8));
}

function puntos_por_score(int $score, int $max): int
{
    if ($max <= 1) {
        return 0;
    }
    $t = ($score - 1) / ($max - 1);
    if ($t >= 0.60) {
        return 1; // Positiva (+1 punto)
    }
    if ($t <= 0.40) {
        return -1; // Negativa (-1 punto)
    }
    return 0; // Neutral (0 puntos)
}

function user_avatar(?array $u, int $size = 40, string $class = ''): string
{
    $foto = !empty($u['foto']) ? trim((string)$u['foto']) : '';
    if ($foto !== '') {
        if (str_starts_with($foto, 'http://') || str_starts_with($foto, 'https://') || str_starts_with($foto, 'data:')) {
            $src = $foto;
        } else {
            $src = asset('img/avatars/' . ltrim($foto, '/'));
        }
        return '<img src="' . e($src) . '" width="' . $size . '" height="' . $size . '" class="rounded-circle object-fit-cover shadow-sm ' . e($class) . '" style="width:' . $size . 'px; height:' . $size . 'px;" alt="Avatar">';
    }

    $nombre = !empty($u['nombre']) ? $u['nombre'] : (!empty($u['usuario']) ? $u['usuario'] : 'U');
    $initial = mb_strtoupper(mb_substr(trim((string)$nombre), 0, 1));
    $bg = '#1e293b';
    $rol = (string)($u['rol'] ?? '');
    if ($rol === 'Administrador') $bg = '#F15A22';
    elseif ($rol === 'ATC') $bg = '#2563eb';
    elseif ($rol === 'Setter de Ventas') $bg = '#d97706';
    elseif ($rol === 'NOC') $bg = '#0891b2';
    elseif ($rol === 'Freelancer') $bg = '#7c3aed';

    return '<div class="rounded-circle d-inline-flex align-items-center justify-content-center fw-bold text-white shadow-sm ' . e($class) . '" style="width:' . $size . 'px; height:' . $size . 'px; background:' . $bg . '; font-size:' . round($size * 0.42) . 'px; line-height:1;">' . e($initial) . '</div>';
}

function etiqueta_score(int $score, int $max): string

{
    $t = $max <= 1 ? 1 : ($score - 1) / ($max - 1);
    if ($t <= 0.15) {
        return 'Muy mala';
    }
    if ($t <= 0.30) {
        return 'Mala';
    }
    if ($t <= 0.45) {
        return 'Regular baja';
    }
    if ($t <= 0.55) {
        return 'Regular';
    }
    if ($t <= 0.70) {
        return 'Buena';
    }
    if ($t <= 0.85) {
        return 'Muy buena';
    }
    return 'Excelente';
}

function catalogo_caritas(): array
{
    return [
        ['n' => 1,  'icon' => 'fa-face-angry',       'color' => '#b42318', 'mensaje' => 'Lamentamos profundamente esta experiencia. Priorizaremos su caso de inmediato.'],
        ['n' => 2,  'icon' => 'fa-face-tired',        'color' => '#c4320a', 'mensaje' => 'Sentimos no haber cubierto su expectativa. Escalaremos el caso con el área responsable.'],
        ['n' => 3,  'icon' => 'fa-face-frown',        'color' => '#dc6803', 'mensaje' => 'Tomamos nota de su insatisfacción. Vamos a mejorar su atención desde hoy.'],
        ['n' => 4,  'icon' => 'fa-face-frown-open',   'color' => '#e17100', 'mensaje' => 'Gracias por avisarnos. Revisaremos el proceso para no repetir este inconveniente.'],
        ['n' => 5,  'icon' => 'fa-face-meh',          'color' => '#ca8504', 'mensaje' => 'Agradecemos su tiempo. Trabajaremos para brindarle una mejor experiencia.'],
        ['n' => 6,  'icon' => 'fa-face-meh-blank',    'color' => '#a15c07', 'mensaje' => 'Registramos su valoración. Buscaremos que su próxima visita sea más ágil.'],
        ['n' => 7,  'icon' => 'fa-face-smile',        'color' => '#4ca30d', 'mensaje' => '¡Gracias! Nos alegra haber podido ayudarle. Seguimos mejorando para usted.'],
        ['n' => 8,  'icon' => 'fa-face-smile-beam',   'color' => '#3b7c0f', 'mensaje' => '¡Muy bien! Su opinión impulsa a todo el equipo de Vuela Internet.'],
        ['n' => 9,  'icon' => 'fa-face-laugh',        'color' => '#087443', 'mensaje' => '¡Excelente atención registrada! Gracias por confiar en Internet de verdad.'],
        ['n' => 10, 'icon' => 'fa-face-grin-stars',   'color' => '#067647', 'mensaje' => '¡Fantástico! El equipo de Vuela celebra su satisfacción.'],
        ['n' => 11, 'icon' => 'fa-face-grin-hearts',  'color' => '#F15A22', 'mensaje' => '¡Wooow! Atención de 10. Gracias por volar con nosotros. ¡Que tenga un gran día!'],
    ];
}

function niveles_activos(int $cantidad): array
{
    $all = catalogo_caritas();
    $cantidad = max(3, min(11, $cantidad));
    if ($cantidad === 11) {
        return $all;
    }
    $picked = [];
    for ($i = 0; $i < $cantidad; $i++) {
        $idx = (int) round($i * 10 / ($cantidad - 1));
        $item = $all[$idx];
        $item['n'] = $i + 1;
        $picked[] = $item;
    }
    return $picked;
}

function no_cache_headers(): void
{
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
}

function view(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $user = auth_user();
    $viewFile = APP_PATH . '/Views/' . $name . '.php';
    require APP_PATH . '/Views/layout.php';
}

function view_plain(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require APP_PATH . '/Views/' . $name . '.php';
}
