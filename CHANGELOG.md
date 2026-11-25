# Changelog

All notable changes to **USIAMS (EduTrack)** are documented in this file.

## [1.0.0] - 2026-11-25
### Added
- **Repository & Documentation (`Phase 0`, `Phase 8`):** Project skeleton, `.gitignore` (tracking `students.xml`), `.github/pull_request_template.md`, `README.md`, `docs/SRS-summary.md`, `docs/DEMO.md`, `docs/architecture.md`, and `CHANGELOG.md`.
- **Database Foundation (`Phase 1`):** Idempotent MySQL schema and 2 demo student records in `sql/usiams.sql` (`usiams_db.users`, `usiams_db.students`), plus reusable `mysqli` (`utf8mb4`) connection and `execute_prepared()` helper in `includes/db.php`.
- **Authentication & Sessions (`Phase 2`):** Splash login (`index.php`), POST-only authentication (`login_process.php`) with `password_verify()` and `session_regenerate_id(true)`, sign-up with `password_hash()` and duplicate username check (`signup.php`), full session/cookie cleanup (`logout.php`), protected `dashboard.php`, and session/cache guards (`includes/auth.php`).
- **Student Management CRUD (`Phase 3`):** Protected `add_student.php`, `view_students.php` (ordered list + prepared `SQL LIKE` search), `edit_student.php` (`reg_no` read-only), and POST-only `delete_student.php` with JavaScript confirmation.
- **Dual Validation (`Phase 4`):** Matching client-side (`js/validation.js` on `blur` and `submit`) and centralized server-side (`includes/validators.php`) validators plus `docs/validation-tests.md`.
- **XML Synchronization & Viewer (`Phase 5`):** Atomic UTF-8 `DOMDocument` generator (`includes/xml_helper.php`) keeping `students.xml` synced on add/edit/delete, and protected `xml_view.php` using `simplexml_load_file()` with graceful missing/empty handling.
- **Responsive UI/UX (`Phase 6`):** Vanilla CSS stylesheet (`css/style.css`) with system fonts, accessible focus outlines, centered auth cards, shared header/footer (`includes/header.php`, `includes/footer.php`) with active navigation highlighting, and desktop/tablet responsive breakpoints.
- **Security Hardening & Acceptance Testing (`Phase 7`):** Security audit (`docs/security-review.md`), `display_errors = 0`, `session.use_strict_mode = 1`, direct-access `403` include guards, per-session CSRF tokens on all POST forms, 1,000-row performance benchmark (`view_students.php` 62.93 ms; `regenerate_students_xml()` 8.27 ms), and `TC-01`–`TC-20` acceptance report (`docs/test-report.md`).
