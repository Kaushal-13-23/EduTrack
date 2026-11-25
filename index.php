<?php
// Splash login page for USIAMS.

require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$success_msg = get_flash('success');
$error_msg = get_flash('error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - USIAMS</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js" defer></script>
</head>
<body>
    <main class="container">
        <h1>USIAMS Login</h1>

        <?php if ($success_msg !== ''): ?>
            <p class="alert alert-success" role="status"><?php echo esc($success_msg); ?></p>
        <?php endif; ?>

        <?php if ($error_msg !== ''): ?>
            <p class="alert alert-error" role="alert"><?php echo esc($error_msg); ?></p>
        <?php endif; ?>

        <form id="loginForm" action="login_process.php" method="post" novalidate>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" maxlength="30" required>
                <span class="field-error" id="username-error"></span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <span class="field-error" id="password-error"></span>
            </div>

            <button type="submit">Login</button>
        </form>

        <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
    </main>
</body>
</html>
