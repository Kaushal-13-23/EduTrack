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

document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', validateLoginForm);
    }
});
