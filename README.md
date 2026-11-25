# EduTrack

## University Student Information and Academic Management System (USIAMS)

### Purpose
USIAMS (EduTrack) is a web-based academic administration application for managing user authentication and university student records with synchronized MySQL and XML storage.

### Tech Stack
- **Frontend:** HTML, CSS, vanilla JavaScript (no frameworks, libraries, or CDNs)
- **Backend:** PHP 8.x (procedural / simple functions, `mysqli` with prepared statements)
- **Database:** MySQL (`usiams_db`)
- **Data Exchange:** UTF-8 XML (`students.xml`)
- **Server Environment:** XAMPP / WAMP (Apache + MySQL)

### Setup Steps (XAMPP / WAMP)
1. Place the project folder (`usiams`) inside your web server root (e.g., `C:\xampp\htdocs\usiams`).
2. Start **Apache** and **MySQL** from the XAMPP/WAMP Control Panel.
3. Open phpMyAdmin (or MySQL CLI), create/select database `usiams_db`, and import `sql/usiams.sql`.
4. Open your browser and navigate to `http://localhost/usiams/` (splash/login page `index.php`).

### Phase Checklist
- [x] **Phase 0:** Repository bootstrap, directory skeleton, documentation, `.gitignore`, and PR template
- [ ] **Phase 1:** Database schema (`sql/usiams.sql`) and connection helper (`includes/db.php`)
- [ ] **Phase 2:** Authentication, session guard, sign-up, login, logout, and dashboard
- [ ] **Phase 3:** Student CRUD (`add_student.php`, `view_students.php`, `edit_student.php`, `delete_student.php`) with dual JS + PHP validation
- [ ] **Phase 4:** XML synchronization (`includes/xml_helper.php`, `students.xml`) and XML viewer (`xml_view.php`)
- [ ] **Phase 5:** End-to-end styling, security audit, and final verification
