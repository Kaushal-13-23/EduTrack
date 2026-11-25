# USIAMS Security Review

**Scope:** Read-only audit of all PHP, SQL, XML, and JavaScript files on `phase/7-security-testing` prior to remediation, followed by verification of fixes.

## 1. Security Controls Checklist
| Control Area | Audit Result | Notes |
| --- | --- | --- |
| Prepared statements on every query with user input | **PASS** | All SQL queries in `login_process.php`, `signup.php`, `add_student.php`, `edit_student.php`, `delete_student.php`, `view_students.php`, and `includes/xml_helper.php` execute via `execute_prepared()` (`mysqli::prepare` + `bind_param`). |
| `htmlspecialchars()` on every rendered output | **PASS (with 1 minor hardening)** | Every HTML output cell, alert, flash message, form value, and error uses `esc()` (`htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`). Minor hardening applied to wrap `edit_student.php?reg_no=...` URL attribute in `esc()` inside `view_students.php`. |
| Session guard on every protected page | **PASS** | `dashboard.php`, `add_student.php`, `view_students.php`, `edit_student.php`, `delete_student.php`, and `xml_view.php` all include `includes/auth.php` and call `require_login()` before any output or DB operation. |
| Session regeneration & full destruction | **PASS (hardened)** | `login_process.php` calls `session_regenerate_id(true)` on login. `logout.php` clears `$_SESSION`, expires the session cookie, and calls `session_destroy()`. Protected pages emit `Cache-Control: no-store, no-cache, must-revalidate, max-age=0`. Hardened with `session.use_strict_mode = 1`. |
| Generic login errors | **PASS** | `login_process.php` returns the identical message `"Invalid username or password."` for empty inputs, unknown usernames, and wrong passwords. |
| No raw PHP or SQL error display | **PARTIAL → FIXED** | `includes/db.php` catches `mysqli_sql_exception`, logs via `error_log()`, and outputs a friendly message; `includes/xml_helper.php` and `xml_view.php` suppress raw XML/filesystem warnings. Added `ini_set('display_errors', '0')` / `ini_set('log_errors', '1')` and direct-access `403` guards on `includes/*.php`. |
| Password hashing | **PASS** | `signup.php` hashes passwords with `password_hash($password, PASSWORD_DEFAULT)` (`$2y$` bcrypt); `login_process.php` verifies with `password_verify()`. |
| CSRF protection on POST forms | **MISSING → ADDED** | Added per-session cryptographic CSRF tokens (`get_csrf_token()`, `csrf_field()`, `verify_csrf_token()`) to all 5 POST forms and handlers. |

---

## 2. Audit Findings & Remediations
1. **Finding 1 (Medium) — Runtime `display_errors` not disabled in code**
   - *Risk:* Default XAMPP `php.ini` sets `display_errors = On`, which could expose raw warnings or paths if an unexpected runtime error occurred.
   - *Fix:* Added `ini_set('display_errors', '0')` and `ini_set('log_errors', '1')` in `includes/auth.php` and `includes/db.php`.
2. **Finding 2 (Medium) — Direct HTTP access allowed on `includes/*.php`**
   - *Risk:* Requesting `includes/header.php` directly via browser triggers an unguarded `esc()` call (`Call to undefined function esc()`).
   - *Fix:* Added direct-access `403 Forbidden` guards (`realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__`) at the top of `includes/auth.php`, `includes/db.php`, `includes/header.php`, `includes/footer.php`, `includes/validators.php`, and `includes/xml_helper.php`.
3. **Finding 3 (Medium) — `session.use_strict_mode` not enforced**
   - *Risk:* Without strict mode, PHP can accept uninitialized client-supplied session IDs.
   - *Fix:* Added `ini_set('session.use_strict_mode', '1')` in `start_session_safely()` (`includes/auth.php`).
4. **Finding 4 (Low) — URL attribute in `view_students.php` lacked `esc()` wrapper**
   - *Risk:* Defense-in-depth rule requires `htmlspecialchars()` on every output.
   - *Fix:* Wrapped `edit_student.php?reg_no=...` in `esc()` in `view_students.php`.
5. **Finding 5 (High) — Missing CSRF protection on POST forms**
   - *Risk:* State-changing POST endpoints (`login_process.php`, `signup.php`, `add_student.php`, `edit_student.php`, `delete_student.php`) could be targeted by cross-site request forgery.
   - *Fix:* Added per-session random CSRF token helpers (`bin2hex(random_bytes(32))` + `hash_equals()`) in `includes/auth.php`, embedded `csrf_field()` in all POST forms, and enforced `verify_csrf_token()` on all POST handlers.
