# EduTrack

## University Student Information and Academic Management System (USIAMS)

### Overview
USIAMS (EduTrack) is a lightweight, framework-free academic administration web application for managing authenticated staff access and university student records with synchronized MySQL storage and UTF-8 XML export.

### Features
- **Authentication & Session Management:** User sign-up (`password_hash()`), login (`password_verify()`), session ID regeneration (`session_regenerate_id(true)`), strict session guard (`includes/auth.php`), anti-cache headers on protected pages, and full session/cookie destruction on logout.
- **Student Management (CRUD):** Add, view, search (`SQL LIKE` by `reg_no` or `student_name`), edit (`reg_no` read-only), and POST-only delete with JavaScript `confirm()`.
- **Dual Client & Server Validation:** Identical regex patterns, limits, and inline error messages across `js/validation.js` (`blur` and `submit`) and `includes/validators.php`.
- **Synchronized XML Storage & Viewer:** Atomic `DOMDocument` regeneration of `students.xml` after every add, edit, or delete, paired with `xml_view.php` displaying record counts and escaped records.
- **Responsive UI/UX:** Vanilla CSS (`css/style.css`) with system typography, centered auth cards, shared 5-link navbar with active-page highlighting, accessible focus rings, and desktop/tablet responsive breakpoints.
- **Security Hardening:** `mysqli` prepared statements (`execute_prepared()`), `htmlspecialchars()` (`esc()`) output escaping, per-session CSRF tokens on all POST forms, generic login errors, `display_errors = 0`, and `403 Forbidden` guards on direct `includes/*.php` requests.

### Tech Stack
- **Frontend:** HTML5, CSS3 (Flexbox/Grid), vanilla JavaScript (no frameworks, libraries, or CDNs)
- **Backend:** PHP 8.x (procedural/simple functions with `mysqli` prepared statements, `DOMDocument`, `SimpleXML`)
- **Database:** MySQL / MariaDB (`usiams_db`)
- **Data Exchange:** UTF-8 XML (`students.xml`)
- **Server Environment:** XAMPP or WAMP (Apache + MySQL)

### Project Structure
```text
usiams/
├── .github/
│   └── pull_request_template.md
├── css/
│   └── style.css
├── docs/
│   ├── DEMO.md
│   ├── SRS-summary.md
│   ├── architecture.md
│   ├── security-review.md
│   ├── test-report.md
│   └── validation-tests.md
├── includes/
│   ├── auth.php
│   ├── db.php
│   ├── footer.php
│   ├── header.php
│   ├── validators.php
│   └── xml_helper.php
├── js/
│   └── validation.js
├── sql/
│   └── usiams.sql
├── .gitignore
├── CHANGELOG.md
├── README.md
├── add_student.php
├── dashboard.php
├── delete_student.php
├── edit_student.php
├── index.php
├── login_process.php
├── logout.php
├── signup.php
├── students.xml
├── view_students.php
└── xml_view.php
```

### Installation & Setup

#### Option A: XAMPP (Windows / Linux / macOS)
1. Clone or copy this repository into your XAMPP web root as `usiams`:
   ```bash
   git clone https://github.com/Kaushal-13-23/EduTrack.git C:\xampp\htdocs\usiams
   ```
2. Open the **XAMPP Control Panel** and start **Apache** and **MySQL**.
3. Ensure `C:\xampp\htdocs\usiams\` is writable by Apache so `students.xml` can be updated atomically.

#### Option B: WAMP (Windows)
1. Clone or copy this repository into your WAMP web root as `usiams`:
   ```bash
   git clone https://github.com/Kaushal-13-23/EduTrack.git C:\wamp64\www\usiams
   ```
2. Launch **WampServer** and verify the tray icon turns green (Apache and MySQL running).
3. Ensure `C:\wamp64\www\usiams\` is writable by Apache.

### Database Import (`sql/usiams.sql`)
- **Using phpMyAdmin:**
  1. Open `http://localhost/phpmyadmin/`.
  2. Click the **Import** tab, choose `sql/usiams.sql`, and click **Import** (or **Go**).
  3. Confirm database `usiams_db` is created with tables `users` and `students` (seeded with 2 demo student records).
- **Using MySQL CLI (XAMPP):**
  ```bash
  C:\xampp\mysql\bin\mysql.exe -u root < sql/usiams.sql
  ```
- **Connection credentials:** Configured in `includes/db.php` (`$db_host = 'localhost'`, `$db_user = 'root'`, `$db_pass = ''`, `$db_name = 'usiams_db'`).

### Default URL
Open your browser and visit:
- **http://localhost/usiams/** (loads `index.php`, the splash login page)

### Usage Guide
1. **Sign Up:** Click **Sign Up** on `index.php` (`signup.php`), fill in Full Name, Username, Email, Password, and Confirm Password, and submit to create an account.
2. **Log In:** Enter your Username and Password on `index.php` to open `dashboard.php`.
3. **Add Student:** Click **Add Student** (`add_student.php`), enter the 6 student fields, and click **Save**. MySQL and `students.xml` update immediately.
4. **View & Search Students:** Click **View Students** (`view_students.php`) to inspect all records ordered by `reg_no`, or search by partial name or `reg_no`.
5. **Edit Student:** Click **Edit** on a student row (`edit_student.php`), update editable fields (`reg_no` is read-only), and click **Update**.
6. **Delete Student:** Click **Delete** on a student row, confirm the prompt, and submit (`delete_student.php`).
7. **View XML Records:** Click **View XML Records** from the navigation bar, `dashboard.php`, or `view_students.php` (`xml_view.php`) to view records parsed from `students.xml`.
8. **Log Out:** Click **Logout** (`logout.php`) to destroy the session and return to `index.php`.

### SRS Traceability Summary
| Module | SRS IDs | Implemented Files | Reference Docs |
| --- | --- | --- | --- |
| Technical Constraints | `TECH-01`–`TECH-04` | All root & `includes/` files, `css/style.css`, `js/validation.js` | [docs/SRS-summary.md](docs/SRS-summary.md), [docs/architecture.md](docs/architecture.md) |
| Database Foundation | `DB-01`–`DB-03` | `sql/usiams.sql`, `includes/db.php` | [docs/SRS-summary.md](docs/SRS-summary.md) |
| Authentication & Sessions | `AUTH-01`–`AUTH-05` | `includes/auth.php`, `index.php`, `login_process.php`, `signup.php`, `logout.php`, `dashboard.php` | [docs/DEMO.md](docs/DEMO.md), [docs/test-report.md](docs/test-report.md) |
| Student Management (CRUD) | `STU-01`–`STU-04` | `add_student.php`, `view_students.php`, `edit_student.php`, `delete_student.php` | [docs/DEMO.md](docs/DEMO.md), [docs/test-report.md](docs/test-report.md) |
| Dual Validation | `VAL-01`–`VAL-08` | `js/validation.js`, `includes/validators.php` | [docs/validation-tests.md](docs/validation-tests.md) |
| Security & CSRF | `SEC-01`–`SEC-06` | `includes/auth.php`, `includes/db.php`, all form handlers | [docs/security-review.md](docs/security-review.md) |
| XML Sync & Viewer | `XML-01`–`XML-04` | `includes/xml_helper.php`, `students.xml`, `xml_view.php` | [docs/architecture.md](docs/architecture.md), [docs/test-report.md](docs/test-report.md) |

### Phase Checklist
- [x] **Phase 0:** Repository bootstrap, directory skeleton, documentation, `.gitignore`, and PR template
- [x] **Phase 1:** Database schema (`sql/usiams.sql`) and reusable `mysqli` connection (`includes/db.php`)
- [x] **Phase 2:** Authentication, session guard, sign-up, login, logout, and dashboard
- [x] **Phase 3:** Student CRUD (`add_student.php`, `view_students.php`, `edit_student.php`, `delete_student.php`)
- [x] **Phase 4:** Identical client-side (`js/validation.js`) and server-side (`includes/validators.php`) validation
- [x] **Phase 5:** Atomic XML generator (`includes/xml_helper.php`), `students.xml` sync, and `xml_view.php`
- [x] **Phase 6:** Shared responsive UI/UX (`css/style.css`, `includes/header.php`, `includes/footer.php`)
- [x] **Phase 7:** Security audit (`docs/security-review.md`), CSRF tokens, 1,000-row benchmark, and `TC-01`–`TC-20` acceptance testing (`docs/test-report.md`)
- [x] **Phase 8:** Final documentation (`README.md`, `docs/DEMO.md`, `docs/architecture.md`) and `CHANGELOG.md` (`v1.0.0`)
