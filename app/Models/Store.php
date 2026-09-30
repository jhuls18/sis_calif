<?php
declare(strict_types=1);

final class Store
{
    public static function boot(): void
    {
        if (!is_dir(DATA_PATH)) {
            mkdir(DATA_PATH, 0775, true);
        }
        self::ensure('config', self::defaultConfig());
        self::ensure('users', []);
        self::ensure('clients', []);
        self::ensure('ratings', []);
        self::seedUsers();
    }

    public static function path(string $name): string
    {
        return DATA_PATH . '/' . $name . '.json';
    }

    public static function read(string $name)
    {
        $file = self::path($name);
        if (!is_file($file)) {
            return [];
        }
        $h = fopen($file, 'rb');
        if (!$h) {
            return [];
        }
        flock($h, LOCK_SH);
        $raw = stream_get_contents($h);
        flock($h, LOCK_UN);
        fclose($h);
        $data = json_decode((string)$raw, true);
        return $data === null ? [] : $data;
    }

    public static function write(string $name, $data): void
    {
        $file = self::path($name);
        $h = fopen($file, 'c+b');
        if (!$h) {
            throw new RuntimeException('No se pudo escribir ' . $name);
        }
        flock($h, LOCK_EX);
        ftruncate($h, 0);
        rewind($h);
        fwrite($h, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        fflush($h);
        flock($h, LOCK_UN);
        fclose($h);
    }

    private static function ensure(string $name, $default): void
    {
        if (!is_file(self::path($name))) {
            self::write($name, $default);
        }
    }

    private static function defaultConfig(): array
    {
        return [
            'google_script_url' => '',
            'google_sheet_view_url' => '',
            'niveles_caritas' => 5,
            'empresa' => 'Vuela Internet',
            'mensajes_custom' => [],
            'permitir_visitante' => true,
            'cuestionarios' => [
                [
                    'id' => 'cuest-general',
                    'titulo' => 'Atención General',
                    'pregunta' => '¿Cómo califica la atención recibida hoy en caja u oficina?',
                    'escala' => 5,
                    'icono' => 'fa-headset',
                ],
                [
                    'id' => 'cuest-baja',
                    'titulo' => 'Solicitud de Baja',
                    'pregunta' => '¿Cómo evalúa la atención durante su trámite de baja de servicio?',
                    'escala' => 4,
                    'icono' => 'fa-file-excel',
                ],
                [
                    'id' => 'cuest-noc',
                    'titulo' => 'Soporte Técnico NOC',
                    'pregunta' => '¿Cómo califica la atención y solución brindada por Soporte Técnico?',
                    'escala' => 5,
                    'icono' => 'fa-wrench',
                ],
                [
                    'id' => 'cuest-instalacion',
                    'titulo' => 'Instalación Nueva',
                    'pregunta' => '¿Qué tan satisfecho quedó con la instalación de su servicio Vuela?',
                    'escala' => 3,
                    'icono' => 'fa-bolt',
                ],
            ],
        ];
    }

    private static function seedUsers(): void
    {
        $users = self::read('users');
        if (!empty($users)) {
            $changed = false;
            foreach ($users as &$u) {
                if (!empty($u['password']) && !str_starts_with((string)$u['password'], '$2y$')) {
                    $u['password'] = password_hash((string)$u['password'], PASSWORD_DEFAULT);
                    $changed = true;
                }
            }
            unset($u);
            if ($changed) {
                self::write('users', $users);
            }
            $users = self::read('users');
            $prot = false;
            foreach ($users as &$u) {
                if (($u['id'] ?? '') === 'u-admin' || mb_strtolower((string)($u['usuario'] ?? '')) === 'admin') {
                    if (empty($u['protegido'])) {
                        $u['protegido'] = true;
                        $prot = true;
                    }
                }
            }
            unset($u);
            if ($prot) {
                self::write('users', $users);
            }
            return;
        }
        $seed = [
            [
                'id' => 'u-admin',
                'usuario' => 'admin',
                'nombre' => 'Administrador Vuela',
                'password' => password_hash('admin', PASSWORD_DEFAULT),
                'protegido' => true,
                'rol' => 'Administrador',
                'activo' => true,
                'creado' => now(),
                'last_login' => null,
                'last_ip' => null,
            ],
            [
                'id' => 'u-atc',
                'usuario' => 'atc',
                'nombre' => 'Agente ATC Demo',
                'password' => password_hash('atc123', PASSWORD_DEFAULT),
                'rol' => 'ATC',
                'activo' => true,
                'creado' => now(),
                'last_login' => null,
                'last_ip' => null,
            ],
            [
                'id' => 'u-ventas',
                'usuario' => 'ventas',
                'nombre' => 'Setter de Ventas Demo',
                'password' => password_hash('ventas123', PASSWORD_DEFAULT),
                'rol' => 'Setter de Ventas',
                'activo' => true,
                'creado' => now(),
                'last_login' => null,
                'last_ip' => null,
            ],
        ];
        self::write('users', $seed);
    }
}
