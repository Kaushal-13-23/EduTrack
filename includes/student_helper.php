<?php
// Shared student input validation helper for add_student.php and edit_student.php.

/**
 * Validate and normalize student form input according to SRS rules.
 *
 * @param array $input           Raw input array (e.g. $_POST).
 * @param bool  $validate_reg_no Whether to validate reg_no (true on add, false on edit).
 * @return array{values: array, errors: array}
 */
function validate_student_fields(array $input, bool $validate_reg_no = true): array
{
    $values = [
        'reg_no'       => trim((string) ($input['reg_no'] ?? '')),
        'student_name' => trim((string) ($input['student_name'] ?? '')),
        'course'       => trim((string) ($input['course'] ?? '')),
        'semester'     => trim((string) ($input['semester'] ?? '')),
        'email'        => trim((string) ($input['email'] ?? '')),
        'contact_no'   => trim((string) ($input['contact_no'] ?? '')),
    ];

    $errors = [];

    if ($validate_reg_no) {
        if ($values['reg_no'] === '') {
            $errors['reg_no'] = 'Registration number is required.';
        } elseif (!preg_match('/^[A-Za-z0-9]{5,15}$/', $values['reg_no'])) {
            $errors['reg_no'] = 'Registration number must be 5 to 15 alphanumeric characters.';
        }
    }

    if ($values['student_name'] === '') {
        $errors['student_name'] = 'Student name is required.';
    } elseif (!preg_match('/^[A-Za-z ]{2,100}$/', $values['student_name'])) {
        $errors['student_name'] = 'Student name must be 2 to 100 letters and spaces only.';
    }

    if ($values['course'] === '') {
        $errors['course'] = 'Course is required.';
    } elseif (strlen($values['course']) < 2 || strlen($values['course']) > 100) {
        $errors['course'] = 'Course must be 2 to 100 characters.';
    }

    if ($values['semester'] === '') {
        $errors['semester'] = 'Semester is required.';
    } elseif (filter_var(
        $values['semester'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1, 'max_range' => 12]]
    ) === false) {
        $errors['semester'] = 'Semester must be an integer from 1 to 12.';
    }

    if ($values['email'] === '') {
        $errors['email'] = 'Email is required.';
    } elseif (strlen($values['email']) > 100 || filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($values['contact_no'] === '') {
        $errors['contact_no'] = 'Contact number is required.';
    } elseif (!preg_match('/^\d{10}$/', $values['contact_no'])) {
        $errors['contact_no'] = 'Contact number must be exactly 10 digits.';
    }

    return [
        'values' => $values,
        'errors' => $errors,
    ];
}
