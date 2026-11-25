<?php
// Protected user dashboard for USIAMS.

require_once __DIR__ . '/includes/auth.php';
require_login();

$page_title = 'Dashboard - USIAMS';
$active_page = 'dashboard.php';
require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
    <div class="page-header">
        <div>
            <h1>USIAMS Dashboard</h1>
            <p class="subtitle">Welcome, <strong><?php echo esc($_SESSION['username']); ?></strong>!</p>
        </div>
    </div>

    <div class="dashboard-grid">
        <article class="dash-card">
            <div>
                <h2>Add Student</h2>
                <p>Register a new student record with dual client and server validation.</p>
            </div>
            <a class="btn btn-primary" href="add_student.php">Add Student</a>
        </article>

        <article class="dash-card">
            <div>
                <h2>View Students</h2>
                <p>Browse, search, update, or remove student records stored in MySQL.</p>
            </div>
            <a class="btn btn-primary" href="view_students.php">View Students</a>
        </article>

        <article class="dash-card">
            <div>
                <h2>View XML Records</h2>
                <p>Inspect the synchronized UTF-8 XML export generated from MySQL.</p>
            </div>
            <a class="btn btn-secondary" href="xml_view.php">View XML Records</a>
        </article>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
