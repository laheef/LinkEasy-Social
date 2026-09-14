<?php
/**
 * LinkEasy Social — application bootstrap.
 *
 * Loads environment + config, starts a hardened session, registers the
 * autoloader and helper functions. This file is designed to be included
 * from your existing application too — see README "Integrating with the
 * existing app" — without creating a second auth system.
 */

declare(strict_types=1);

define('LES_BASE_PATH', dirname(__DIR__));
define('LES_STARTED_AT', microtime(true));

/* ---------------------------------------------------------------------
 * Tiny .env loader (no Composer dependency)
 * ------------------------------------------------------------------- */
if (is_file(LES_BASE_PATH . '/.env')) {
    foreach (file(LES_BASE_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        // Quoted secrets may contain #; strip comments only outside quotes.
        if (preg_match('/^([\"\'])(.*?)\\1(?:\\s+#.*)?$/', $value, $quoted)) {
            $value = $quoted[2];
        } else {
            $value = preg_replace('/\\s+#.*$/', '', $value);
        }
        if (getenv($key) === false) {
            putenv("{$key}={$value}");
            $_ENV[$key] = $value;
        }
    }
}

function env(string $key, mixed $default = null): mixed
{
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $default;
    }
    return match (strtolower($value)) {
        'true'  => true,
        'false' => false,
        'null'  => null,
        default => $value,
    };
}

/* ---------------------------------------------------------------------
 * Autoloader for App\ classes in /src
 * ------------------------------------------------------------------- */
spl_autoload_register(function (string $class): void {
    if (!str_starts_with($class, 'App\\')) {
        return;
    }
    $file = LES_BASE_PATH . '/src/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

/* ---------------------------------------------------------------------
 * Config singleton
 * ------------------------------------------------------------------- */
function config(?string $key = null, mixed $default = null): mixed
{
    static $config;
    $config ??= require LES_BASE_PATH . '/config/app.php';
    if ($key === null) {
        return $config;
    }
    $value = $config;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }
    return $value;
}

/* ---------------------------------------------------------------------
 * Output / routing helpers
 * ------------------------------------------------------------------- */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Resolve a key from the central routes map (e.g. url('signup') => /signup). */
function url(string $routeKey, array $params = []): string
{
    $path = config("routes.{$routeKey}") ?? '/';
    if ($params) {
        $path .= (str_contains($path, '?') ? '&' : '?') . http_build_query($params);
    }
    return $path;
}

/** Absolute URL (for canonical/OG/sitemaps). Honors configurable APP_URL. */
function absolute_url(string $path = '/'): string
{
    return config('brand.app_url') . $path;
}

function asset(string $path): string
{
    $path = '/' . ltrim($path, '/');
    $file = LES_BASE_PATH . '/public' . $path;
    if (!is_file($file) && !empty($_SERVER['DOCUMENT_ROOT'])) {
        $file = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $path;
    }
    $version = is_file($file) ? substr(hash_file('sha256',$file),0,12) : null;
    return $path . ($version ? "?v={$version}" : '');
}

function redirect(string $path): never
{
    header('Location: ' . $path, true, 302);
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

/* ---------------------------------------------------------------------
 * Hardened session — call once, before any output
 * ------------------------------------------------------------------- */
function les_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $https = \App\Security::https() || (PHP_SAPI !== 'cli-server' && env('APP_FORCE_HTTPS',true));
    ini_set('session.use_strict_mode','1');
    ini_set('session.use_only_cookies','1');
    session_cache_limiter(''); // Security owns the no-store policy.
    session_name('les_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();

    // Idle timeout (default 60 minutes)
    $idleTimeout = 3600;
    if (!empty($_SESSION['_last_activity']) && time() - $_SESSION['_last_activity'] > $idleTimeout) {
        les_logout(true);
    }
    if ((parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH) ?: '/') !== '/auth/session-check') $_SESSION['_last_activity'] = time();
}

/* ---------------------------------------------------------------------
 * CSRF
 * ------------------------------------------------------------------- */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/',$token) || empty($_SESSION['_csrf']) || !hash_equals($_SESSION['_csrf'], $token)) {
        http_response_code(419);
        if (is_ajax_request()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'csrf_failed', 'message' => 'Your session expired. Please refresh and try again.']);
        } else {
            les_render_error(419, 'Your session expired. Please refresh and try again.');
        }
        exit;
    }
}

function is_ajax_request(): bool
{
    return strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
}

/* ---------------------------------------------------------------------
 * Flash messages
 * ------------------------------------------------------------------- */
function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function flashes(): array
{
    $messages = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $messages;
}

/* ---------------------------------------------------------------------
 * Auth + logout wiring (implementation in src/Auth.php)
 * ------------------------------------------------------------------- */
function les_logout(bool $timeout = false): void
{
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) session_regenerate_id(true);
    if ($timeout) $_SESSION['_timed_out'] = true;

}

/* ---------------------------------------------------------------------
 * View rendering
 * ------------------------------------------------------------------- */
function view(string $template, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require LES_BASE_PATH . '/views/' . ltrim($template, '/') . '.php';
}

function les_render_error(int $code = 500, ?string $message = null): void
{
    http_response_code($code);
    $messages = [
        404 => "Looks like this page got lost.",
        403 => "You don't have permission to access this page.",
        419 => "Your session has expired. Please sign in again.",
        500 => "Something went wrong on our side.",
    ];
    view('errors/error', [
        'code' => $code,
        'message' => $message ?? ($messages[$code] ?? 'Something went wrong.'),
    ]);
}

\App\Security::initialize();
if (PHP_SAPI !== 'cli') les_start_session();
