<?php
declare(strict_types=1);

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) return;
    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && strtolower((string) $_SERVER['HTTPS']) !== 'off';
    session_name('NABTA_SESSION');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function security_headers(): void
{
    if (headers_sent()) return;
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf_token']) || !is_string($_SESSION['_csrf_token'])) {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf_token(mixed $submitted): bool
{
    return is_string($submitted)
        && isset($_SESSION['_csrf_token'])
        && is_string($_SESSION['_csrf_token'])
        && hash_equals($_SESSION['_csrf_token'], $submitted);
}

function is_authenticated(): bool
{
    return isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']) && (int) $_SESSION['user_id'] > 0;
}

function require_authenticated_user(): void
{
    if (!is_authenticated()) {
        $_SESSION['auth_notice'] = 'Please sign in to continue.';
        header('Location: index.php?route=login');
        exit;
    }
}

function app_length(string $value): int
{
    if (function_exists('mb_strlen')) return mb_strlen($value, 'UTF-8');
    $count = preg_match_all('/./us', $value, $matches);
    return $count === false ? strlen($value) : $count;
}

function app_lower(string $value): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}

function enforce_session_timeout(): void
{
    if (!is_authenticated()) return;
    $lastActivity = (int) ($_SESSION['_last_activity'] ?? time());
    if (time() - $lastActivity > 7200) {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['auth_notice'] = 'Your session expired. Please sign in again.';
        return;
    }
    $_SESSION['_last_activity'] = time();
}

function require_role(PDO $db, array|string $allowedRoles): array
{
    require_authenticated_user();
    $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
    $stmt = $db->prepare('SELECT user_id, full_name, email, role, status FROM users WHERE user_id = :id LIMIT 1');
    $stmt->execute(['id' => (int) $_SESSION['user_id']]);
    $user = $stmt->fetch();
    if (!$user || $user['status'] !== 'active') {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['auth_notice'] = 'Please sign in to continue.';
        header('Location: index.php?route=login');
        exit;
    }
    $_SESSION['role'] = (string) $user['role'];
    if (!in_array((string) $user['role'], $roles, true)) {
        http_response_code(403);
        exit('You do not have permission to access this page.');
    }
    return $user;
}

function password_policy_passes(string $password): bool
{
    return app_length($password) >= 8
        && preg_match('/\p{L}/u', $password) === 1
        && preg_match('/\p{N}/u', $password) === 1
        && preg_match('/[^\p{L}\p{N}]/u', $password) === 1;
}

function google_client_id(): string
{
    $config = require dirname(__DIR__, 2) . '/config/google.php';
    $clientId = trim((string) ($config['client_id'] ?? ''));
    return str_contains($clientId, 'PASTE_YOUR') ? '' : $clientId;
}
