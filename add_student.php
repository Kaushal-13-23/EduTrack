<?php
// Protected page to add a new student record in USIAMS.

require_once __DIR__ . '/includes/auth.php';
require_login();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/student_helper.php';

$values = [
    'reg_no'       => '',
    'student_name' => '',
    'course'       => '',
    'semester'     => '',
    'email'        => '',
    'contact_no'   => '',
];
$errors = [];
$general_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validation = validate_student_fields($_POST, true);
    $values = $validation['values'];
    $errors = $validation['errors'];

    if (empty($errors)) {
        // Explicit duplicate reg_no check before relying on the primary key constraint.
        $existing = execute_prepared(
            $conn,
            'SELECT reg_no FROM students WHERE reg_no = ? LIMIT 1',
            's',
            [$values['reg_no']]
        );

        if ($existing === false) {
            $general_error = 'Unable to verify registration number. Please try again.';
        } elseif ($existing->num_rows > 0) {
            $existing->free();
            $errors['reg_no'] = 'Registration number already exists.';
        } else {
            $existing->free();
            $inserted = execute_prepared(
                $conn,
                'INSERT INTO students (reg_no, student_name, course, semester, email, contact_no) VALUES (?, ?, ?, ?, ?, ?)',
                'sssiss',
                [
                    $values['reg_no'],
                    $values['student_name'],
                    $values['course'],
                    (int) $values['semester'],
                    $values['email'],
                    $values['contact_no'],
                ]
            );

            if ($inserted === true) {
                // TODO (Phase 5): Call XML regeneration helper (includes/xml_helper.php) to update students.xml.
                set_flash('success', 'Student record added successfully.');
                header('Location: view_students.php');
                exit;
            }

            if ($conn->errno === 1062) {
                $errors['reg_no'] = 'Registration number already exists.';
            } else {
                $general_error = 'Unable to add student record. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - USIAMS</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js" defer></script>
</head>
<body>
    <main class="container">
        <header>
            <h1>Add Student</h1>
            <nav aria-label="Page Navigation">
                <a href="view_students.php">View Students</a> |
                <a href="dashboard.php">Dashboard</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <?php if ($general_error !== ''): ?>
            <p class="alert alert-error" role="alert"><?php echo esc($general_error); ?></p>
        <?php endif; ?>

        <form id="addStudentForm" action="add_student.php" method="post" novalidate>
            <div class="form-group">
                <label for="reg_no">Registration Number</label>
                <input type="text" id="reg_no" name="reg_no" maxlength="15" value="<?php echo esc($values['reg_no']); ?>" required>
                <span class="field-error error" id="reg_no-error" style="color: #b91c1c;"><?php echo esc($errors['reg_no'] ?? ''); ?></span>
            </div>

            <div class="form-group">
                <label for="student_name">Student Name</label>
                <input type="text" id="student_name" name="student_name" maxlength="100" value="<?php echo esc($values['student_name']); ?>" required>
                <span class="field-error error" id="student_name-error" style="color: #b91c1c;"><?php echo esc($errors['student_name'] ?? ''); ?></span>
            </div>

            <div class="form-group">
                <label for="course">Course</label>
                <input type="text" id="course" name="course" maxlength="100" value="<?php echo esc($values['course']); ?>" required>
                <span class="field-error error" id="course-error" style="color: #b91c1c;"><?php echo esc($errors['course'] ?? ''); ?></span>
            </div>

            <div class="form-group">
                <label for="semester">Semester</label>
                <input type="number" id="semester" name="semester" min="1" max="12" value="<?php echo esc($values['semester']); ?>" required>
                <span class="field-error error" id="semester-error" style="color: #b91c1c;"><?php echo esc($errors['semester'] ?? ''); ?></span>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" maxlength="100" value="<?php echo esc($values['email']); ?>" required>
                <span class="field-error error" id="email-error" style="color: #b91c1c;"><?php echo esc($errors['email'] ?? ''); ?></span>
            </div>

            <div class="form-group">
                <label for="contact_no">Contact Number</label>
                <input type="text" id="contact_no" name="contact_no" maxlength="10" value="<?php echo esc($values['contact_no']); ?>" required>
                <span class="field-error error" id="contact_no-error" style="color: #b91c1c;"><?php echo esc($errors['contact_no'] ?? ''); ?></span>
            </div>

            <button type="submit">Save</button>
            <button type="reset">Reset</button>
        </form>
    </main>
</body>
</html>
