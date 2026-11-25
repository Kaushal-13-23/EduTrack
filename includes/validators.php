<?php
// Centralized server-side validation functions for USIAMS.
// Rules and error messages intentionally mirror js/validation.js.

if (PHP_SAPI !== 'cli' && isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * Validate a field as a non-empty email matching format and length <= 100.
 */
function validate_email_value(string $email): string
{
    if ($email === '') {
        return 'Email is required.';
    }
    if (
        strlen($email) > 100
        || !preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/', $email)
        || filter_var($email, FILTER_VALIDATE_EMAIL) === false
    ) {
        return 'Please enter a valid email address.';
    }
    return '';
}

/**
 * Validate login form fields (non-empty username and password).
 *
 * @param array $input Raw input array (e.g. $_POST).
 * @return array{values: array, errors: array<string, string>}
 */
function validate_login_fields(array $input): array
{
    $values = [
        'username' => trim((string) ($input['username'] ?? '')),
        'password' => (string) ($input['password'] ?? ''),
    ];

    $errors = [];

    if ($values['username'] === '') {
        $errors['username'] = 'Username is required.';
    }
    if ($values['password'] === '') {
        $errors['password'] = 'Password is required.';
    }

    return [
        'values' => $values,
        'errors' => $errors,
    ];
}

/**
 * Validate sign-up form fields according to SRS rules.
 *
 * @param array $input Raw input array (e.g. $_POST).
 * @return array{values: array, errors: array<string, string>}
 */
function validate_signup_fields(array $input): array
{
    $values = [
        'full_name'        => trim((string) ($input['full_name'] ?? '')),
        'username'         => trim((string) ($input['username'] ?? '')),
        'email'            => trim((string) ($input['email'] ?? '')),
        'password'         => (string) ($input['password'] ?? ''),
        'confirm_password' => (string) ($input['confirm_password'] ?? ''),
    ];

    $errors = [];

    if ($values['full_name'] === '') {
        $errors['full_name'] = 'Full name is required.';
    } elseif (strlen($values['full_name']) < 2 || strlen($values['full_name']) > 100) {
        $errors['full_name'] = 'Full name must be 2 to 100 characters.';
    }

    if ($values['username'] === '') {
        $errors['username'] = 'Username is required.';
    } elseif (!preg_match('/^[A-Za-z0-9_]{4,30}$/', $values['username'])) {
        $errors['username'] = 'Username must be 4-30 characters (letters, digits, underscore).';
    }

    $email_err = validate_email_value($values['email']);
    if ($email_err !== '') {
        $errors['email'] = $email_err;
    }

    if ($values['password'] === '') {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($values['password']) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    if ($values['confirm_password'] === '') {
        $errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($values['password'] !== $values['confirm_password']) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    return [
        'values' => $values,
        'errors' => $errors,
    ];
}

/**
 * Validate student form fields for add_student.php and edit_student.php.
 *
 * @param array $input           Raw input array (e.g. $_POST).
 * @param bool  $validate_reg_no Whether to validate reg_no (true on add, false on edit).
 * @return array{values: array, errors: array<string, string>}
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
    } elseif (
        !preg_match('/^\d+$/', $values['semester'])
        || filter_var($values['semester'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]]) === false
    ) {
        $errors['semester'] = 'Semester must be an integer from 1 to 12.';
    }

    $email_err = validate_email_value($values['email']);
    if ($email_err !== '') {
        $errors['email'] = $email_err;
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
