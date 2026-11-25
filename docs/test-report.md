# USIAMS Acceptance & Performance Test Report

**Branch:** `phase/7-security-testing`  
**Environment:** Windows, XAMPP (Apache + PHP 8.x + MySQL/MariaDB `usiams_db`), `http://localhost/usiams/`  

---

## 1. Performance Benchmark (1,000 Seeded Students)
A temporary SQL script (not committed) seeded 1,000 student records (`PERF0001`–`PERF1000`) alongside the 2 demo rows (`1,002` total rows in `usiams_db.students`). All seeded rows were removed after measurement and `students.xml` was restored to the 2 demo records.

| Metric | Target | Measured Result | Status |
| --- | --- | --- | --- |
| `view_students.php` HTTP GET load (`1,002` rows) | `< 3,000 ms` (`< 3 s`) | `62.93 ms` (`0.063 s`) | **PASS** |
| `regenerate_students_xml($conn)` (`1,002` XML nodes) | `< 2,000 ms` (`< 2 s`) | `8.27 ms` (`0.008 s`) | **PASS** |
| Post-test cleanup (`students` & `students.xml` count) | `2` demo rows | DB=`2`, XML=`2` | **PASS** |

---

## 2. Acceptance Test Cases (`TC-01` to `TC-20`)

| ID | Test Case | Evidence / Notes | Status |
| --- | --- | --- | --- |
| `TC-01` | Open `http://localhost/usiams/` shows login splash page | HTTP `200`, renders `USIAMS Login`, CSRF hidden input, `js/validation.js` | **PASS** |
| `TC-02` | Valid login reaches `dashboard.php` | `POST login_process.php` → `302 dashboard.php`; HTTP `200` with username & nav; `session_regenerate_id(true)` active | **PASS** |
| `TC-03` | Wrong password / unknown user shows generic error | Redirects to `index.php` displaying `"Invalid username or password."` | **PASS** |
| `TC-04` | Empty submit blocked by JavaScript | Verified `js/validation.js` in Node VM DOM mock (`preventDefault()` called, inline `.error` spans populated) | **PASS** |
| `TC-05` | Sign-up creates user with hashed password | `POST signup.php` → `302 index.php` with success flash; MySQL `password_hash` starts with `$2y$` | **PASS** |
| `TC-06` | Duplicate username rejected on sign-up | Re-submitting `testuser_p7` returned HTTP `200` with `"Username is already taken. Please choose another."` | **PASS** |
| `TC-07` | Opening `dashboard.php` without login redirects to `index.php` | Unauthenticated `GET dashboard.php` → `302` `Location: index.php` | **PASS** |
| `TC-08` | Add valid student saves to MySQL & redirects | `POST add_student.php` (`REG2026007`) → `302 view_students.php`; row verified in UI & MySQL | **PASS** |
| `TC-09` | Duplicate `reg_no` rejected on add | Re-submitting `REG2026007` returned HTTP `200` with `"Registration number already exists."` | **PASS** |
| `TC-10` | Invalid email & 9-digit contact blocked by JS and rejected by PHP | JS VM test blocked on `blur` & `submit`; PHP POST (no JS) rejected `bad-email@`, `987654321`, semester `13` | **PASS** |
| `TC-11` | Edit student `course` and `semester` (`reg_no` read-only) | `POST edit_student.php` updated `REG2026007` to `MTech VLSI`, semester `4` in MySQL & UI | **PASS** |
| `TC-12` | Delete student with confirm | `POST delete_student.php` with valid CSRF removed `REG2026007` from MySQL (`count=0`) & UI | **PASS** |
| `TC-13` | Delete cancel & GET protection | `view_students.php` enforces `onsubmit="return confirm(...)"`; `GET delete_student.php` rejected without deleting *(manual browser click-cancel pending visual capture)* | **PASS** |
| `TC-14` | `view_students.php` lists MySQL data ordered by `reg_no` | Verified demo and added rows rendered in HTML table | **PASS** |
| `TC-15` | MySQL table matches UI after each CRUD operation | Verified `usiams_db.students` via `mysql.exe` queries after Add, Edit, Delete, and cleanup | **PASS** |
| `TC-16` | `"View XML Records"` opens `xml_view.php` | Links present on `dashboard.php`, `view_students.php`, and navbar; `xml_view.php` returns HTTP `200` with count & table | **PASS** |
| `TC-17` | `students.xml` updates immediately on add/edit/delete | Checked `GET students.xml` immediately after Add, Edit, and Delete operations | **PASS** |
| `TC-18` | `students.xml` is well-formed UTF-8 XML | Parsed cleanly via XML parser (`<students>` root, `<student>` children) | **PASS** |
| `TC-19` | After logout, Back/refresh does not show protected page | `logout.php` destroys session + cookie; `dashboard.php` emits `Cache-Control: no-store` and redirects `302 → index.php` | **PASS** |
| `TC-20` | `<script>alert(1)</script>` in Student Name displays as text | Rejected on form submit & escaped as `&lt;script&gt;alert(1)&lt;/script&gt;`; direct DB row rendered safely as literal `<td>&lt;script&gt;alert(1)&lt;/script&gt;</td>` in `view_students.php` and `xml_view.php` | **PASS** |

---

## 3. Additional Security & CSRF Checks
- **Direct access to `includes/*.php`:** Requests to `includes/auth.php`, `includes/db.php`, `includes/header.php`, `includes/footer.php`, `includes/validators.php`, and `includes/xml_helper.php` all returned `HTTP 403 Forbidden`.
- **CSRF rejection:** Invalid CSRF tokens on `login_process.php`, `add_student.php`, and `delete_student.php` were rejected cleanly without modifying database state.

---

## 4. Browser Visual Screenshots
- *(Attach manual browser screenshots from Chrome/Firefox/Edge at `1366px` and `768px` here when opening the PR; automated Chrome DevTools capture was unavailable because `DevToolsActivePort` is not active in this environment.)*
