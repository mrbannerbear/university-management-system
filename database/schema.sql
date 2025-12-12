-- University Result Management System Database Schema
-- Drop existing database if exists and create new
DROP DATABASE IF EXISTS university_db;
CREATE DATABASE university_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE university_db;

-- Table: departments
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(10) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'faculty', 'student') NOT NULL,
    department_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Table: students
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    department_id INT,
    year INT NOT NULL,
    semester INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Table: faculty
CREATE TABLE faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    department_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Table: courses
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) NOT NULL UNIQUE,
    course_name VARCHAR(150) NOT NULL,
    credit DECIMAL(3,1) NOT NULL,
    department_id INT,
    semester INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);

-- Table: faculty_courses (links faculty to courses)
CREATE TABLE faculty_courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    course_id INT NOT NULL,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_assignment (faculty_id, course_id)
);

-- Table: results
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    marks DECIMAL(5,2) NOT NULL,
    grade VARCHAR(5),
    grade_point DECIMAL(3,2),
    faculty_id INT,
    semester INT NOT NULL,
    academic_year VARCHAR(10) NOT NULL,
    published TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (faculty_id) REFERENCES faculty(id) ON DELETE SET NULL
);

-- Insert Sample Departments
INSERT INTO departments (name, code) VALUES
('Computer Science', 'CSE'),
('Electrical Engineering', 'EEE'),
('Business Administration', 'BBA');

-- Insert Sample Users (password: admin123, faculty123, student123 for all respective users)
-- Password hash for 'admin123'
INSERT INTO users (name, email, password, role, department_id) VALUES
('System Admin', 'admin@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL);

-- Password hash for 'faculty123'
INSERT INTO users (name, email, password, role, department_id) VALUES
('Dr. John Smith', 'faculty1@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'faculty', 1),
('Dr. Sarah Johnson', 'faculty2@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'faculty', 2),
('Prof. Michael Brown', 'faculty3@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'faculty', 3);

-- Password hash for 'student123'
INSERT INTO users (name, email, password, role, department_id) VALUES
('Alice Williams', 'student1@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 1),
('Bob Davis', 'student2@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 1),
('Carol Martinez', 'student3@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 2),
('David Garcia', 'student4@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 2),
('Emma Rodriguez', 'student5@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 3),
('Frank Wilson', 'student6@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 3),
('Grace Taylor', 'student7@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 1),
('Henry Anderson', 'student8@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 2),
('Isabel Thomas', 'student9@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 3),
('Jack Martinez', 'student10@university.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 1);

-- Insert Faculty Members
INSERT INTO faculty (user_id, name, department_id) VALUES
(2, 'Dr. John Smith', 1),
(3, 'Dr. Sarah Johnson', 2),
(4, 'Prof. Michael Brown', 3);

-- Insert Students
INSERT INTO students (user_id, student_id, name, department_id, year, semester) VALUES
(5, 'CSE2021001', 'Alice Williams', 1, 3, 5),
(6, 'CSE2021002', 'Bob Davis', 1, 3, 5),
(7, 'EEE2021001', 'Carol Martinez', 2, 2, 3),
(8, 'EEE2021002', 'David Garcia', 2, 2, 3),
(9, 'BBA2021001', 'Emma Rodriguez', 3, 4, 7),
(10, 'BBA2021002', 'Frank Wilson', 3, 4, 7),
(11, 'CSE2022001', 'Grace Taylor', 1, 2, 3),
(12, 'EEE2022001', 'Henry Anderson', 2, 1, 1),
(13, 'BBA2022001', 'Isabel Thomas', 3, 1, 1),
(14, 'CSE2022002', 'Jack Martinez', 1, 2, 3);

-- Insert Courses
INSERT INTO courses (course_code, course_name, credit, department_id, semester) VALUES
('CSE101', 'Introduction to Programming', 3.0, 1, 1),
('CSE201', 'Data Structures', 3.0, 1, 3),
('CSE301', 'Database Management Systems', 3.0, 1, 5),
('CSE401', 'Software Engineering', 3.0, 1, 7),
('EEE101', 'Circuit Analysis', 3.0, 2, 1),
('EEE201', 'Digital Electronics', 3.0, 2, 3),
('EEE301', 'Microprocessors', 3.0, 2, 5),
('BBA101', 'Principles of Management', 3.0, 3, 1),
('BBA201', 'Marketing Management', 3.0, 3, 3),
('BBA301', 'Financial Management', 3.0, 3, 7);

-- Assign Courses to Faculty
INSERT INTO faculty_courses (faculty_id, course_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4),  -- Dr. John Smith teaches CSE courses
(2, 5), (2, 6), (2, 7),          -- Dr. Sarah Johnson teaches EEE courses
(3, 8), (3, 9), (3, 10);         -- Prof. Michael Brown teaches BBA courses

-- Insert Sample Results
INSERT INTO results (student_id, course_id, marks, grade, grade_point, faculty_id, semester, academic_year, published) VALUES
-- Alice Williams (CSE2021001) - Semester 5
(1, 3, 92, 'A+', 4.00, 1, 5, '2023-2024', 1),
-- Bob Davis (CSE2021002) - Semester 5
(2, 3, 85, 'A', 3.75, 1, 5, '2023-2024', 1),
-- Carol Martinez (EEE2021001) - Semester 3
(3, 6, 78, 'B+', 3.25, 2, 3, '2023-2024', 1),
-- David Garcia (EEE2021002) - Semester 3
(4, 6, 88, 'A', 3.75, 2, 3, '2023-2024', 1),
-- Emma Rodriguez (BBA2021001) - Semester 7
(5, 10, 95, 'A+', 4.00, 3, 7, '2023-2024', 1),
-- Frank Wilson (BBA2021002) - Semester 7
(6, 10, 72, 'B', 3.00, 3, 7, '2023-2024', 1),
-- Grace Taylor (CSE2022001) - Semester 3
(7, 2, 89, 'A', 3.75, 1, 3, '2023-2024', 1),
-- Henry Anderson (EEE2022001) - Semester 1
(8, 5, 65, 'B-', 2.75, 2, 1, '2023-2024', 1),
-- Isabel Thomas (BBA2022001) - Semester 1
(9, 8, 80, 'A-', 3.50, 3, 1, '2023-2024', 1),
-- Jack Martinez (CSE2022002) - Semester 3
(10, 2, 76, 'B+', 3.25, 1, 3, '2023-2024', 1);
