<?php
// Protected user dashboard for USIAMS.

require_once __DIR__ . '/includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - USIAMS</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="container">
        <header>
            <h1>USIAMS Dashboard</h1>
            <p>Welcome, <strong><?php echo esc($_SESSION['username']); ?></strong>!</p>
        </header>

        <nav aria-label="Main Navigation">
            <ul>
                <li><a href="add_student.php">Add Student</a></li>
                <li><a href="view_students.php">View Students</a></li>
                <li><a href="xml_view.php">View XML Records</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </main>
</body>
</html>
