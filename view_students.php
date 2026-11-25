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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students - USIAMS</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="container">
        <header>
            <h1>Student Records</h1>
            <nav aria-label="Page Navigation">
                <a href="add_student.php">Add Student</a> |
                <a href="xml_view.php">View XML Records</a> |
                <a href="dashboard.php">Dashboard</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </header>

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
            <button type="submit">Search</button>
            <?php if ($search !== ''): ?>
                <a href="view_students.php">Clear</a>
            <?php endif; ?>
        </form>

        <?php if (empty($students)): ?>
            <p>No records found</p>
        <?php else: ?>
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
                            <td>
                                <a href="edit_student.php?reg_no=<?php echo urlencode($student['reg_no']); ?>">Edit</a>
                                |
                                <form action="delete_student.php" method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this student record?');">
                                    <input type="hidden" name="reg_no" value="<?php echo esc($student['reg_no']); ?>">
                                    <button type="submit" style="background:none;border:none;padding:0;color:inherit;text-decoration:underline;cursor:pointer;font:inherit;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>
