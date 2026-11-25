<?php
// Protected page to list and search student records in USIAMS.

require_once __DIR__ . '/includes/auth.php';
require_login();

require_once __DIR__ . '/includes/db.php';

$success_msg = get_flash('success');
$error_msg = get_flash('error');
$search = trim((string) ($_GET['q'] ?? ''));
$students = [];
$query_error = '';

if ($search !== '') {
    $escaped_search = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
    $like_term = '%' . $escaped_search . '%';
    $result = execute_prepared(
        $conn,
        'SELECT reg_no, student_name, course, semester, email, contact_no FROM students WHERE reg_no LIKE ? OR student_name LIKE ? ORDER BY reg_no ASC',
        'ss',
        [$like_term, $like_term]
    );
} else {
    $result = execute_prepared(
        $conn,
        'SELECT reg_no, student_name, course, semester, email, contact_no FROM students ORDER BY reg_no ASC'
    );
}

if ($result === false) {
    $query_error = 'Unable to load student records right now.';
} else {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $result->free();
}

$page_title = 'View Students - USIAMS';
$active_page = 'view_students.php';
require_once __DIR__ . '/includes/header.php';
?>
<section class="card">
    <div class="page-header">
        <h1>Student Records</h1>
        <div class="form-actions">
            <a class="btn btn-primary" href="add_student.php">Add Student</a>
            <a class="btn btn-secondary" href="xml_view.php">View XML Records</a>
        </div>
    </div>

    <?php if ($success_msg !== ''): ?>
        <p class="alert alert-success" role="status"><?php echo esc($success_msg); ?></p>
    <?php endif; ?>

    <?php if ($error_msg !== ''): ?>
        <p class="alert alert-error" role="alert"><?php echo esc($error_msg); ?></p>
    <?php endif; ?>

    <?php if ($query_error !== ''): ?>
        <p class="alert alert-error" role="alert"><?php echo esc($query_error); ?></p>
    <?php endif; ?>

    <form action="view_students.php" method="get" class="search-form">
        <label for="q">Search by Reg No or Name:</label>
        <input type="text" id="q" name="q" value="<?php echo esc($search); ?>" placeholder="Enter reg no or name">
        <button type="submit" class="btn btn-primary">Search</button>
        <?php if ($search !== ''): ?>
            <a class="btn btn-secondary" href="view_students.php">Clear</a>
        <?php endif; ?>
    </form>

    <?php if (empty($students)): ?>
        <p class="empty-state">No records found</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Reg No</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Semester</th>
                        <th>Email</th>
                        <th>Contact No</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo esc($student['reg_no']); ?></td>
                            <td><?php echo esc($student['student_name']); ?></td>
                            <td><?php echo esc($student['course']); ?></td>
                            <td><?php echo esc((string) $student['semester']); ?></td>
                            <td><?php echo esc($student['email']); ?></td>
                            <td><?php echo esc($student['contact_no']); ?></td>
                            <td class="actions-cell">
                                <a href="edit_student.php?reg_no=<?php echo urlencode($student['reg_no']); ?>">Edit</a>
                                <span class="action-sep">|</span>
                                <form action="delete_student.php" method="post" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this student record?');">
                                    <input type="hidden" name="reg_no" value="<?php echo esc($student['reg_no']); ?>">
                                    <button type="submit" class="btn-link-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
