# USIAMS Validation Test Table

All rules listed below are enforced identically in `js/validation.js` (client-side on `blur` and `submit`) and `includes/validators.php` (server-side on `POST`).

| Form(s) | Field | Rule & Limits | Valid Sample | Invalid Sample | Expected Error Message |
| --- | --- | --- | --- | --- | --- |
| Login | `username` | Required (non-empty) | `admin_01` | *(empty)* | `Username is required.` *(JS inline; PHP redirects with generic `Invalid username or password.`)* |
| Login | `password` | Required (non-empty) | `secret123` | *(empty)* | `Password is required.` *(JS inline; PHP redirects with generic `Invalid username or password.`)* |
| Sign Up | `full_name` | Required, 2–100 characters | `Aarav Sharma` | `A` | `Full name must be 2 to 100 characters.` |
| Sign Up | `username` | Required, 4–30 chars (`/^[A-Za-z0-9_]{4,30}$/`) | `aarav_01` | `ab` or `user@1` | `Username must be 4-30 characters (letters, digits, underscore).` |
| Sign Up, Add/Edit Student | `email` | Required, <= 100 chars, valid format (`/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/`) | `aarav@example.edu` | `bad-email@` or `a@b.c` | `Please enter a valid email address.` |
| Sign Up | `password` | Required, min 6 characters | `pass123` | `12345` | `Password must be at least 6 characters.` |
| Sign Up | `confirm_password` | Required, must match `password` | `pass123` | `pass999` | `Passwords do not match.` |
| Add Student | `reg_no` | Required, 5–15 alphanumeric (`/^[A-Za-z0-9]{5,15}$/`), unique | `REG2026001` | `REG-01!` | `Registration number must be 5 to 15 alphanumeric characters.` |
| Add/Edit Student | `student_name` | Required, 2–100 letters & spaces (`/^[A-Za-z ]{2,100}$/`) | `Priya Nair` | `Priya123` | `Student name must be 2 to 100 letters and spaces only.` |
| Add/Edit Student | `course` | Required, 2–100 characters | `BTech Computer Science` | `B` | `Course must be 2 to 100 characters.` |
| Add/Edit Student | `semester` | Required, integer 1–12 | `4` | `0` or `13` | `Semester must be an integer from 1 to 12.` |
| Add/Edit Student | `contact_no` | Required, exactly 10 digits (`/^\d{10}$/`) | `9876543210` | `987654321` (9 digits) | `Contact number must be exactly 10 digits.` |
