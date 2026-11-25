-- Database schema and sample seed data for USIAMS (usiams_db).

CREATE DATABASE IF NOT EXISTS usiams_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE usiams_db;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS students (
    reg_no VARCHAR(15) PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    course VARCHAR(100) NOT NULL,
    semester TINYINT NOT NULL,
    email VARCHAR(100) NOT NULL,
    contact_no CHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Demo student records (idempotent for repeated imports).
INSERT IGNORE INTO students (reg_no, student_name, course, semester, email, contact_no) VALUES
    ('REG2026001', 'Aarav Sharma', 'BTech Computer Science', 4, 'aarav.sharma@example.edu', '9876543210'),
    ('REG2026002', 'Priya Nair', 'MSc Data Science', 2, 'priya.nair@example.edu', '9123456780');
