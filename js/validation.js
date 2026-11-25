// Client-side form validation for USIAMS (login, sign-up, add student, edit student).
// Rules and error messages intentionally mirror includes/validators.php.

const VALIDATION_RULES = {
    regNo: /^[A-Za-z0-9]{5,15}$/,
    studentName: /^[A-Za-z ]{2,100}$/,
    email: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/,
    contactNo: /^\d{10}$/,
    semesterDigits: /^\d+$/,
    username: /^[A-Za-z0-9_]{4,30}$/
};

/**
 * Display or clear an inline error message beside a field with the .error class.
 */
function setFieldError(fieldId, message) {
    const errorEl = document.getElementById(fieldId + '-error');
    if (!errorEl) {
        return;
    }

    errorEl.textContent = message;
    if (message !== '') {
        errorEl.classList.add('error');
        errorEl.style.color = '#b91c1c';
    } else {
        errorEl.classList.remove('error');
        errorEl.style.color = '';
    }
}

/**
 * Get trimmed or raw value of a form field by ID.
 */
function getFieldValue(fieldId, trimValue = true) {
    const el = document.getElementById(fieldId);
    if (!el) {
        return '';
    }
    return trimValue ? el.value.trim() : el.value;
}

/**
 * Shared single-field validators returning '' when valid or an error string.
 */
const FIELD_VALIDATORS = {
    login_username: function () {
        return getFieldValue('username') === '' ? 'Username is required.' : '';
    },
    login_password: function () {
        return getFieldValue('password', false) === '' ? 'Password is required.' : '';
    },
    full_name: function () {
        const val = getFieldValue('full_name');
        if (val === '') {
            return 'Full name is required.';
        }
        if (val.length < 2 || val.length > 100) {
            return 'Full name must be 2 to 100 characters.';
        }
        return '';
    },
    username: function () {
        const val = getFieldValue('username');
        if (val === '') {
            return 'Username is required.';
        }
        if (!VALIDATION_RULES.username.test(val)) {
            return 'Username must be 4-30 characters (letters, digits, underscore).';
        }
        return '';
    },
    email: function () {
        const val = getFieldValue('email');
        if (val === '') {
            return 'Email is required.';
        }
        if (val.length > 100 || !VALIDATION_RULES.email.test(val)) {
            return 'Please enter a valid email address.';
        }
        return '';
    },
    password: function () {
        const val = getFieldValue('password', false);
        if (val === '') {
            return 'Password is required.';
        }
        if (val.length < 6) {
            return 'Password must be at least 6 characters.';
        }
        return '';
    },
    confirm_password: function () {
        const val = getFieldValue('confirm_password', false);
        const passwordVal = getFieldValue('password', false);
        if (val === '') {
            return 'Please confirm your password.';
        }
        if (val !== passwordVal) {
            return 'Passwords do not match.';
        }
        return '';
    },
    reg_no: function () {
        const val = getFieldValue('reg_no');
        if (val === '') {
            return 'Registration number is required.';
        }
        if (!VALIDATION_RULES.regNo.test(val)) {
            return 'Registration number must be 5 to 15 alphanumeric characters.';
        }
        return '';
    },
    student_name: function () {
        const val = getFieldValue('student_name');
        if (val === '') {
            return 'Student name is required.';
        }
        if (!VALIDATION_RULES.studentName.test(val)) {
            return 'Student name must be 2 to 100 letters and spaces only.';
        }
        return '';
    },
    course: function () {
        const val = getFieldValue('course');
        if (val === '') {
            return 'Course is required.';
        }
        if (val.length < 2 || val.length > 100) {
            return 'Course must be 2 to 100 characters.';
        }
        return '';
    },
    semester: function () {
        const val = getFieldValue('semester');
        if (val === '') {
            return 'Semester is required.';
        }
        if (!VALIDATION_RULES.semesterDigits.test(val)) {
            return 'Semester must be an integer from 1 to 12.';
        }
        const num = Number(val);
        if (num < 1 || num > 12) {
            return 'Semester must be an integer from 1 to 12.';
        }
        return '';
    },
    contact_no: function () {
        const val = getFieldValue('contact_no');
        if (val === '') {
            return 'Contact number is required.';
        }
        if (!VALIDATION_RULES.contactNo.test(val)) {
            return 'Contact number must be exactly 10 digits.';
        }
        return '';
    }
};

/**
 * Bind blur and submit validation handlers for a form using field descriptors.
 * Each descriptor maps an input ID to a validator key in FIELD_VALIDATORS.
 */
function attachFormValidator(formId, fields) {
    const form = document.getElementById(formId);
    if (!form) {
        return;
    }

    fields.forEach(function (field) {
        const input = document.getElementById(field.id);
        if (!input) {
            return;
        }
        input.addEventListener('blur', function () {
            const msg = FIELD_VALIDATORS[field.rule]();
            setFieldError(field.id, msg);

            if (field.id === 'password' && formId === 'signupForm') {
                const confirmVal = getFieldValue('confirm_password', false);
                if (confirmVal !== '') {
                    setFieldError('confirm_password', FIELD_VALIDATORS.confirm_password());
                }
            }
        });
    });

    form.addEventListener('submit', function (event) {
        let valid = true;
        fields.forEach(function (field) {
            const msg = FIELD_VALIDATORS[field.rule]();
            setFieldError(field.id, msg);
            if (msg !== '') {
                valid = false;
            }
        });

        if (!valid) {
            event.preventDefault();
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    attachFormValidator('loginForm', [
        { id: 'username', rule: 'login_username' },
        { id: 'password', rule: 'login_password' }
    ]);

    attachFormValidator('signupForm', [
        { id: 'full_name', rule: 'full_name' },
        { id: 'username', rule: 'username' },
        { id: 'email', rule: 'email' },
        { id: 'password', rule: 'password' },
        { id: 'confirm_password', rule: 'confirm_password' }
    ]);

    attachFormValidator('addStudentForm', [
        { id: 'reg_no', rule: 'reg_no' },
        { id: 'student_name', rule: 'student_name' },
        { id: 'course', rule: 'course' },
        { id: 'semester', rule: 'semester' },
        { id: 'email', rule: 'email' },
        { id: 'contact_no', rule: 'contact_no' }
    ]);

    attachFormValidator('editStudentForm', [
        { id: 'student_name', rule: 'student_name' },
        { id: 'course', rule: 'course' },
        { id: 'semester', rule: 'semester' },
        { id: 'email', rule: 'email' },
        { id: 'contact_no', rule: 'contact_no' }
    ]);
});
