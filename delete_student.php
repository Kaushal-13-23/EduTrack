<?php
// Protected POST-only handler to delete a student record in USIAMS.

require_once __DIR__ . '/includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('error', 'Invalid request method for deleting a student.');
    header('Location: view_students.php');
    exit;
}

$reg_no = trim((string) ($_POST['reg_no'] ?? ''));

if ($reg_no === '') {
    set_flash('error', 'Registration number is required to delete a student.');
    header('Location: view_students.php');
    exit;
}

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/xml_helper.php';

$existing = execute_prepared(
    $conn,
    'SELECT reg_no FROM students WHERE reg_no = ? LIMIT 1',
    's',
    [$reg_no]
);

if ($existing === false) {
    set_flash('error', 'Unable to delete student record. Please try again.');
} elseif ($existing->num_rows === 0) {
    $existing->free();
    set_flash('error', 'Student record not found.');
} else {
    $existing->free();
    $deleted = execute_prepared(
        $conn,
        'DELETE FROM students WHERE reg_no = ?',
        's',
        [$reg_no]
    );

    if ($deleted === true) {
        $xml_ok = regenerate_students_xml($conn);
        if ($xml_ok) {
            set_flash('success', 'Student record deleted successfully.');
        } else {
            set_flash('success', 'Student record deleted, but XML export could not be updated.');
        }
    } else {
        set_flash('error', 'Unable to delete student record. Please try again.');
    }
}

header('Location: view_students.php');
exit;
