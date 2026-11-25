<?php
// User registration (sign-up) page and handler for USIAMS.

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validators.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$full_name = '';
$username = '';
$email = '';
$errors = [];
$general_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validation = validate_signup_fields($_POST);
    $full_name = $validation['values']['full_name'];
    $username = $validation['values']['username'];
    $email = $validation['values']['email'];
    $password = $validation['values']['password'];
    $errors = $validation['errors'];

    if (empty($errors)) {
        require_once __DIR__ . '/includes/db.php';

        $existing = execute_prepared(
            $conn,
            'SELECT user_id FROM users WHERE username = ? LIMIT 1',
            's',
            [$username]
        );

        if ($existing === false) {
            $general_error = 'Unable to complete registration right now. Please try again.';
        } elseif ($existing->num_rows > 0) {
            $existing->free();
            $errors['username'] = 'Username is already taken. Please choose another.';
        } else {
            $existing->free();
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $inserted = execute_prepared(
                $conn,
                'INSERT INTO users (full_name, username, email, password_hash) VALUES (?, ?, ?, ?)',
                'ssss',
                [$full_name, $username, $email, $password_hash]
            );

            if ($inserted === true) {
                set_flash('success', 'Account created successfully. Please log in.');
                header('Location: index.php');
                exit;
            }

            $general_error = 'Unable to complete registration right now. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - USIAMS</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js" defer></script>
</head>
<body>
    <main class="auth-wrapper">
        <section class="auth-card">
            <h1>USIAMS Sign Up</h1>
            <p class="auth-subtitle">Create a new administrator account</p>

            <?php if ($general_error !== ''): ?>
                <p class="alert alert-error" role="alert"><?php echo esc($general_error); ?></p>
            <?php endif; ?>

            <form id="signupForm" action="signup.php" method="post" novalidate>
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" maxlength="100" value="<?php echo esc($full_name); ?>" required>
                    <span class="field-error error" id="full_name-error"><?php echo esc($errors['full_name'] ?? ''); ?></span>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" maxlength="30" value="<?php echo esc($username); ?>" required>
                    <span class="field-error error" id="username-error"><?php echo esc($errors['username'] ?? ''); ?></span>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" maxlength="100" value="<?php echo esc($email); ?>" required>
                    <span class="field-error error" id="email-error"><?php echo esc($errors['email'] ?? ''); ?></span>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <span class="field-error error" id="password-error"><?php echo esc($errors['password'] ?? ''); ?></span>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <span class="field-error error" id="confirm_password-error"><?php echo esc($errors['confirm_password'] ?? ''); ?></span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Sign Up</button>
                </div>
            </form>

            <p class="auth-footer">Already have an account? <a href="index.php">Login</a></p>
        </section>
    </main>
    <footer class="site-footer">
        University Student Information and Academic Management System (USIAMS)
    </footer>
</body>
</html>
