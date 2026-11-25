<?php
// Session management, authentication guard, flash helpers, and output escaping for USIAMS.

if (PHP_SAPI !== 'cli' && isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    http_response_code(403);
    exit('Forbidden');
}

ini_set('display_errors', '0');
ini_set('log_errors', '1');

/**
 * Start the PHP session safely if one is not already active.
 */
function start_session_safely(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.use_strict_mode', '1');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

/**
 * Check whether a user is currently authenticated.
 */
function is_logged_in(): bool
{
    start_session_safely();
    return isset($_SESSION['user_id'], $_SESSION['username'])
        && (int) $_SESSION['user_id'] > 0
        && $_SESSION['username'] !== '';
}

/**
 * Require an authenticated user for protected pages and prevent browser caching.
 */
function require_login(): void
{
    start_session_safely();

    // Prevent back-button / cached rendering of protected pages after logout.
    if (!headers_sent()) {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    }

    if (!is_logged_in()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Store a one-time flash message in the session.
 */
function set_flash(string $type, string $message): void
{
    start_session_safely();
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][$type] = $message;
}

/**
 * Retrieve and clear a one-time flash message from the session.
 */
function get_flash(string $type): string
{
    start_session_safely();
    if (!isset($_SESSION['flash'][$type])) {
        return '';
    }
    $message = (string) $_SESSION['flash'][$type];
    unset($_SESSION['flash'][$type]);
    return $message;
}

/**
 * Escape output safely for HTML rendering.
 */
function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Return the per-session CSRF token, generating one if absent.
 */
function get_csrf_token(): string
{
    start_session_safely();
    if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render a hidden HTML input containing the current session CSRF token.
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . esc(get_csrf_token()) . '">';
}

/**
 * Verify a submitted CSRF token against the session token in constant time.
 */
function verify_csrf_token(?string $token = null): bool
{
    start_session_safely();
    $submitted = $token ?? (string) ($_POST['csrf_token'] ?? '');
    $stored = isset($_SESSION['csrf_token']) && is_string($_SESSION['csrf_token'])
        ? $_SESSION['csrf_token']
        : '';

    return $stored !== '' && $submitted !== '' && hash_equals($stored, $submitted);
}
