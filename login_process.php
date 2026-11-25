<?php
// Login request handler for USIAMS (POST only).

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validators.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$validation = validate_login_fields($_POST);
$username = $validation['values']['username'];
$password = $validation['values']['password'];

if (!empty($validation['errors'])) {
    set_flash('old_username', $username);
    set_flash('error', 'Invalid username or password.');
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';

$result = execute_prepared(
    $conn,
    'SELECT user_id, username, password_hash FROM users WHERE username = ? LIMIT 1',
    's',
    [$username]
);

if ($result === false) {
    set_flash('old_username', $username);
    set_flash('error', 'Unable to process login right now. Please try again.');
    header('Location: index.php');
    exit;
}

$user = $result->fetch_assoc();
$result->free();

if (!$user || !password_verify($password, $user['password_hash'])) {
    set_flash('old_username', $username);
    set_flash('error', 'Invalid username or password.');
    header('Location: index.php');
    exit;
}

start_session_safely();
session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['user_id'];
$_SESSION['username'] = $user['username'];

header('Location: dashboard.php');
exit;
