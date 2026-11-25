# USIAMS — SRS Summary

This document summarizes the Software Requirements Specification (SRS) for the **University Student Information and Academic Management System (USIAMS)**, grouped by module with traceable requirement IDs derived from `.agent/rules/usiams.md`.

---

## 1. Technical Constraints & Architecture (`TECH`)
| ID | Requirement |
| --- | --- |
| `TECH-01` | Use only vanilla HTML, CSS, vanilla JavaScript, PHP 8.x (procedural or simple functions with `mysqli`), MySQL, XML, and XAMPP/WAMP. |
| `TECH-02` | Do not use any front-end or back-end frameworks, libraries, Composer packages, npm build steps, or CDNs. |
| `TECH-03` | Follow the exact project directory and file layout under `usiams/` without renaming required files. |
| `TECH-04` | Serve the application at `http://localhost/usiams/` with `index.php` as the splash/login entry page. |

## 2. Database Schema (`DB`)
| ID | Requirement |
| --- | --- |
| `DB-01` | Provide database `usiams_db` defined in `sql/usiams.sql` and connected via `includes/db.php`. |
| `DB-02` | `users` table: `user_id` (`INT PK AI`), `full_name` (`VARCHAR(100) NN`), `username` (`VARCHAR(30) NN UNIQUE`), `email` (`VARCHAR(100) NN`), `password_hash` (`VARCHAR(255) NN`), `created_at` (`TIMESTAMP DEFAULT CURRENT_TIMESTAMP`). |
| `DB-03` | `students` table: `reg_no` (`VARCHAR(15) PK`), `student_name` (`VARCHAR(100) NN`), `course` (`VARCHAR(100) NN`), `semester` (`TINYINT NN`), `email` (`VARCHAR(100) NN`), `contact_no` (`CHAR(10) NN`), `created_at` (`TIMESTAMP DEFAULT CURRENT_TIMESTAMP`). |

## 3. Authentication & Session Management (`AUTH`)
| ID | Requirement |
| --- | --- |
| `AUTH-01` | `index.php` presents the login page; `login_process.php` authenticates users against `users`. |
| `AUTH-02` | `signup.php` registers new users with unique `username` and hashed passwords. |
| `AUTH-03` | `logout.php` terminates and fully destroys the user session. |
| `AUTH-04` | `includes/auth.php` enforces active session checks on every protected page and redirects unauthenticated requests to `index.php`. |
| `AUTH-05` | `dashboard.php` serves as the authenticated landing page with navigation to student management and `xml_view.php`. |

## 4. Student Record Management (`STU`)
| ID | Requirement |
| --- | --- |
| `STU-01` | `add_student.php` creates a new student record in `students` with unique `reg_no`. |
| `STU-02` | `view_students.php` lists student records and provides a visible "View XML Records" link. |
| `STU-03` | `edit_student.php` updates an existing student record in `students`. |
| `STU-04` | `delete_student.php` removes an existing student record from `students`. |

## 5. Input Validation (`VAL`)
All rules apply in **both** client-side JavaScript (`js/validation.js`) and server-side PHP:
| ID | Field / Scope | Rule |
| --- | --- | --- |
| `VAL-01` | `reg_no` | Required, alphanumeric, 5–15 characters, unique. |
| `VAL-02` | `student_name` | Required, letters and spaces only, 2–100 characters. |
| `VAL-03` | `course` | Required, 2–100 characters. |
| `VAL-04` | `semester` | Required, integer 1–12. |
| `VAL-05` | `email` | Required, valid email format. |
| `VAL-06` | `contact_no` | Required, exactly 10 digits. |
| `VAL-07` | `username` | Required, 4–30 characters, letters/digits/underscore only. |
| `VAL-08` | `password` | Required, minimum 6 characters; must match confirmation at sign-up. |

## 6. Security (`SEC`)
| ID | Requirement |
| --- | --- |
| `SEC-01` | Use `mysqli` prepared statements for every SQL query touching user input. |
| `SEC-02` | Hash passwords with `password_hash()` on sign-up and verify with `password_verify()` on login; never store plaintext passwords. |
| `SEC-03` | Escape all rendered output with `htmlspecialchars()`. |
| `SEC-04` | Return generic login failure messages that do not reveal which field (username or password) was incorrect. |
| `SEC-05` | Never expose raw PHP or SQL errors to end users; log internally or display friendly messages. |
| `SEC-06` | Regenerate the session ID on login (`session_regenerate_id(true)`) and fully destroy the session on logout. |

## 7. XML Synchronization & Viewer (`XML`)
| ID | Requirement |
| --- | --- |
| `XML-01` | `students.xml` uses UTF-8 encoding with root `<students>` and one `<student>` element per record containing `<regno>`, `<name>`, `<course>`, `<semester>`, `<email>`, and `<contact>`. |
| `XML-02` | `includes/xml_helper.php` regenerates `students.xml` from MySQL after every successful student add, edit, or delete. |
| `XML-03` | `xml_view.php` reads `students.xml`, renders an HTML table with total record count, gracefully handles an empty or missing file, and includes a back link to `dashboard.php`. |
| `XML-04` | Both `dashboard.php` and `view_students.php` display a visible "View XML Records" link to `xml_view.php`. |

## 8. Out of Scope
- Hosting, email sending, user roles/permissions, file uploads, reports, grading, fees, and attendance.
