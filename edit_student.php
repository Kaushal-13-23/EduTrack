<?php
// Protected page to edit an existing student record in USIAMS.

require_once __DIR__ . '/includes/auth.php';
require_login();

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validators.php';
require_once __DIR__ . '/includes/xml_helper.php';

$reg_no = trim((string) ($_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['reg_no'] ?? '') : ($_GET['reg_no'] ?? '')));
$student_found = false;
$values = [
    'reg_no'       => $reg_no,
    'student_name' => '',
    'course'       => '',
    'semester'     => '',
    'email'        => '',
    'contact_no'   => '',
];
$errors = [];
$general_error = '';

if ($reg_no === '') {
    $general_error = 'Student record not found.';
} else {
    $result = execute_prepared(
        $conn,
        'SELECT reg_no, student_name, course, semester, email, contact_no FROM students WHERE reg_no = ? LIMIT 1',
        's',
        [$reg_no]
    );

    if ($result === false) {
        $general_error = 'Unable to load student record right now.';
    } else {
        $existing = $result->fetch_assoc();
        $result->free();

        if (!$existing) {
            $general_error = 'Student record not found.';
        } else {
            $student_found = true;
            $values = [
                'reg_no'       => $existing['reg_no'],
                'student_name' => $existing['student_name'],
                'course'       => $existing['course'],
                'semester'     => (string) $existing['semester'],
                'email'        => $existing['email'],
                'contact_no'   => $existing['contact_no'],
            ];
        }
    }
}

if ($student_found && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $validation = validate_student_fields($_POST, false);
    $values['student_name'] = $validation['values']['student_name'];
    $values['course'] = $validation['values']['course'];
    $values['semester'] = $validation['values']['semester'];
    $values['email'] = $validation['values']['email'];
    $values['contact_no'] = $validation['values']['contact_no'];
    $errors = $validation['errors'];

    if (empty($errors)) {
        $updated = execute_prepared(
            $conn,
            'UPDATE students SET student_name = ?, course = ?, semester = ?, email = ?, contact_no = ? WHERE reg_no = ?',
            'ssisss',
            [
                $values['student_name'],
                $values['course'],
                (int) $values['semester'],
                $values['email'],
                $values['contact_no'],
                $values['reg_no'],
            ]
        );

        if ($updated === true) {
            $xml_ok = regenerate_students_xml($conn);
            if ($xml_ok) {
                set_flash('success', 'Student record updated successfully.');
            } else {
                set_flash('success', 'Student record updated, but XML export could not be updated.');
            }
            header('Location: view_students.php');
            exit;
        }

        $general_error = 'Unable to update student record. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student - USIAMS</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js" defer></script>
</head>
<body>
    <main class="container">
        <header>
            <h1>Edit Student</h1>
            <nav aria-label="Page Navigation">
                <a href="view_students.php">View Students</a> |
                <a href="dashboard.php">Dashboard</a> |
                <a href="logout.php">Logout</a>
            </nav>
        </header>

        <?php if ($general_error !== ''): ?>
            <p class="alert alert-error" role="alert"><?php echo esc($general_error); ?></p>
        <?php endif; ?>

        <?php if ($student_found): ?>
            <form id="editStudentForm" action="edit_student.php" method="post" novalidate>
                <div class="form-group">
                    <label for="reg_no">Registration Number</label>
                    <input type="text" id="reg_no" name="reg_no" value="<?php echo esc($values['reg_no']); ?>" readonly>
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

                <button type="submit">Update</button>
                <a href="view_students.php">Cancel</a>
            </form>
        <?php else: ?>
            <p><a href="view_students.php">Back to Student Records</a></p>
        <?php endif; ?>
    </main>
</body>
</html>
