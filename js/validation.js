// Client-side form validation for USIAMS (Phase 2: login and sign-up).

/**
 * Display an inline validation error message beside a field.
 */
function setFieldError(fieldId, message) {
    const el = document.getElementById(fieldId + '-error');
    if (el) {
        el.textContent = message;
    }
}

/**
 * Clear inline validation errors for a list of field IDs.
 */
function clearFieldErrors(fieldIds) {
    fieldIds.forEach(function (fieldId) {
        setFieldError(fieldId, '');
    });
}

/**
 * Validate login form inputs (non-empty username and password).
 */
function validateLoginForm(event) {
    const username = (document.getElementById('username')?.value || '').trim();
    const password = document.getElementById('password')?.value || '';
    clearFieldErrors(['username', 'password']);

    let valid = true;

    if (username === '') {
        setFieldError('username', 'Username is required.');
        valid = false;
    }

    if (password === '') {
        setFieldError('password', 'Password is required.');
        valid = false;
    }

    if (!valid) {
        event.preventDefault();
    }
}

/**
 * Validate sign-up form inputs (required fields, formats, length, password match).
 */
function validateSignupForm(event) {
    const fullName = (document.getElementById('full_name')?.value || '').trim();
    const username = (document.getElementById('username')?.value || '').trim();
    const email = (document.getElementById('email')?.value || '').trim();
    const password = document.getElementById('password')?.value || '';
    const confirmPassword = document.getElementById('confirm_password')?.value || '';

    clearFieldErrors(['full_name', 'username', 'email', 'password', 'confirm_password']);

    let valid = true;

    if (fullName === '') {
        setFieldError('full_name', 'Full name is required.');
        valid = false;
    } else if (fullName.length < 2 || fullName.length > 100) {
        setFieldError('full_name', 'Full name must be 2 to 100 characters.');
        valid = false;
    }

    if (username === '') {
        setFieldError('username', 'Username is required.');
        valid = false;
    } else if (!/^[A-Za-z0-9_]{4,30}$/.test(username)) {
        setFieldError('username', 'Username must be 4-30 characters (letters, digits, underscore).');
        valid = false;
    }

    if (email === '') {
        setFieldError('email', 'Email is required.');
        valid = false;
    } else if (email.length > 100 || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        setFieldError('email', 'Please enter a valid email address.');
        valid = false;
    }

    if (password === '') {
        setFieldError('password', 'Password is required.');
        valid = false;
    } else if (password.length < 6) {
        setFieldError('password', 'Password must be at least 6 characters.');
        valid = false;
    }

    if (confirmPassword === '') {
        setFieldError('confirm_password', 'Please confirm your password.');
        valid = false;
    } else if (password !== confirmPassword) {
        setFieldError('confirm_password', 'Passwords do not match.');
        valid = false;
    }

    if (!valid) {
        event.preventDefault();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', validateLoginForm);
    }

    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', validateSignupForm);
    }
});
