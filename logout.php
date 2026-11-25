<?php
// Logout handler: clears session data, expires session cookie, and destroys session.

require_once __DIR__ . '/includes/auth.php';

start_session_safely();

// Clear all session variables.
$_SESSION = [];

// Expire the session cookie in the client browser.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        (bool) $params['secure'],
        (bool) $params['httponly']
    );
}

session_destroy();

if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}
header('Location: index.php');
exit;
