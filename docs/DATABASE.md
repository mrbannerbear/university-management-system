# Database Documentation 🗄️

## What is a Database?

A **database** is like a digital filing cabinet.  It stores all the information the application needs - students, faculty, courses, grades, everything!

We use **MySQL** as our database system. 

---

## Database Name

The database is called:  **`university_db`**

---

## Tables Overview

Think of **tables** like spreadsheets.  Each table stores a specific type of information.

| Table Name | What It Stores |
|------------|----------------|
| `users` | Login information for everyone |
| `departments` | List of departments (CSE, EEE, etc.) |
| `students` | Student details |
| `faculty` | Faculty member details |
| `courses` | Course information |
| `faculty_courses` | Which faculty teaches which course |
| `results` | Student marks and grades |

---

## Table Details

### 1. `users` Table

Stores login credentials for all users.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Unique identifier (auto-generated) |
| name | VARCHAR(100) | Full name |
| email | VARCHAR(100) | Email address (used for login) |
| password | VARCHAR(255) | Encrypted password |
| role | ENUM | 'admin', 'faculty', or 'student' |
| department_id | INT | Which department they belong to |
| created_at | TIMESTAMP | When the account was created |

**Example data:**
```
| id | name       | email              | role    |
|----|------------|--------------------|---------|
| 1  | Admin User | admin@univ.edu     | admin   |
| 2  | Dr. Smith  | smith@univ.edu     | faculty |
| 3  | John Doe   | john@student.edu   | student |
```

---

### 2. `departments` Table

Stores information about university departments.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Unique identifier |
| code | VARCHAR(10) | Short code (CSE, EEE) |
| name | VARCHAR(100) | Full name |
| description | TEXT | About the department |
| created_at | TIMESTAMP | When it was added |

**Example data:**
```
| id | code | name                          |
|----|------|-------------------------------|
| 1  | CSE  | Computer Science & Engineering|
| 2  | EEE  | Electrical & Electronic Eng.   |
| 3  | BBA  | Business Administration       |
```

---

### 3. `students` Table

Stores student-specific information.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Unique identifier |
| user_id | INT | Links to users table |
| student_id | VARCHAR(20) | Student ID (like CSE2022001) |
| department_id | INT | Their department |
| year | INT | Current year (1, 2, 3, 4) |
| semester | INT | Current semester (1-8) |
| created_at | TIMESTAMP | Enrollment date |

**Example data:**
```
| id | student_id  | department_id | year | semester |
|----|-------------|---------------|------|----------|
| 1  | CSE2022001  | 1             | 2    | 3        |
| 2  | CSE2022002  | 1             | 2    | 3        |
| 3  | EEE2023001  | 2             | 1    | 1        |
```

---

### 4. `faculty` Table

Stores faculty-specific information.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Unique identifier |
| user_id | INT | Links to users table |
| faculty_id | VARCHAR(20) | Faculty ID |
| department_id | INT | Their department |
| designation | VARCHAR(50) | Job title (Professor, Lecturer) |
| qualification | VARCHAR(100) | Degrees (Ph.D., M. Sc.) |
| created_at | TIMESTAMP | When they joined |

**Example data:**
```
| id | faculty_id | department_id | designation       |
|----|------------|---------------|-------------------|
| 1  | FAC001     | 1             | Associate Professor|
| 2  | FAC002     | 1             | Lecturer          |
| 3  | FAC003     | 2             | Professor         |
```

---

### 5. `courses` Table

Stores course information. 

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Unique identifier |
| course_code | VARCHAR(20) | Course code (CSE101) |
| name | VARCHAR(100) | Course name |
| department_id | INT | Which department offers it |
| credit | DECIMAL(3,1) | Credit hours (3.0, 1.5) |
| semester | INT | Which semester (1-8) |
| created_at | TIMESTAMP | When it was added |

**Example data:**
```
| id | course_code | name                    | credit | semester |
|----|-------------|-------------------------|--------|----------|
| 1  | CSE101      | Introduction to CS      | 3.0    | 1        |
| 2  | CSE201      | Data Structures         | 3.0    | 3        |
| 3  | CSE301      | Database Systems        | 3.0    | 5        |
```

---

### 6. `faculty_courses` Table

Links faculty members to the courses they teach. 

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Unique identifier |
| faculty_id | INT | Which faculty member |
| course_id | INT | Which course |
| created_at | TIMESTAMP | When assigned |

**Example data:**
```
| id | faculty_id | course_id |
|----|------------|-----------|
| 1  | 1          | 1         |  (Dr. Smith teaches CSE101)
| 2  | 1          | 2         |  (Dr. Smith also teaches CSE201)
| 3  | 2          | 3         |  (Another faculty teaches CSE301)
```

---

### 7. `results` Table

Stores student marks and grades.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Unique identifier |
| student_id | INT | Which student |
| course_id | INT | Which course |
| faculty_id | INT | Who entered the marks |
| marks | DECIMAL(5,2) | Marks obtained (0-100) |
| grade | VARCHAR(2) | Letter grade (A+, A, B+, etc.) |
| grade_point | DECIMAL(3,2) | Grade point (4.00, 3.75, etc.) |
| semester | INT | Which semester |
| academic_year | VARCHAR(10) | Year (2024, 2024-25) |
| published | BOOLEAN | Is result visible to students?  |
| created_at | TIMESTAMP | When marks were entered |
| updated_at | TIMESTAMP | Last update time |

**Example data:**
```
| student_id | course_id | marks | grade | grade_point | published |
|------------|-----------|-------|-------|-------------|-----------|
| 1          | 1         | 85    | A     | 3.75        | true      |
| 1          | 2         | 78    | B+    | 3.25        | true      |
| 2          | 1         | 92    | A+    | 4.00        | false     |
```

---

## Relationships Between Tables

Tables are connected to each other.  This is called **relationships**.

```
departments ──────┬───────────────────────────────────┐
                  │                                   │
                  ▼                                   ▼
              students                            faculty
                  │                                   │
                  │                                   │
                  │         faculty_courses ◄─────────┤
                  │               │                   │
                  │               ▼                   │
                  │           courses ◄───────────────┘
                  │               │
                  │               │
                  └──────► results ◄──────────────────┘
```

**In simple terms:**
- A **department** has many students and faculty
- A **faculty** can teach many courses
- A **course** can be taught by many faculty
- A **student** has many results
- A **result** belongs to one student and one course

---

## Grade Calculation

Marks are converted to grades using this scale: 

| Marks Range | Grade | Grade Point |
|-------------|-------|-------------|
| 90-100 | A+ | 4.00 |
| 85-89 | A | 3.75 |
| 80-84 | A- | 3.50 |
| 75-79 | B+ | 3.25 |
| 70-74 | B | 3.00 |
| 65-69 | B- | 2.75 |
| 60-64 | C+ | 2.50 |
| 55-59 | C | 2.25 |
| 50-54 | C- | 2.00 |
| 45-49 | D | 1.50 |
| 0-44 | F | 0.00 |

---

## GPA Calculation

**GPA (Grade Point Average)** is calculated per semester:

```
GPA = Sum of (Credit × Grade Point) / Total Credits
```

**Example:**
| Course | Credit | Grade Point | Credit × GP |
|--------|--------|-------------|-------------|
| CSE101 | 3.0 | 3.75 | 11.25 |
| CSE102 | 3.0 | 3.50 | 10.50 |
| CSE103 | 1.5 | 4.00 | 6.00 |

```
GPA = (11.25 + 10.50 + 6.00) / (3.0 + 3.0 + 1.5)
    = 27.75 / 7.5
    = 3.70
```

**CGPA (Cumulative GPA)** is the average of all semesters. 

---

## How to Access the Database

### Using phpMyAdmin (Easy Way)

1. Make sure XAMPP is running (Apache and MySQL)
2. Open browser and go to: `http://localhost/phpmyadmin`
3. Click on `university_db` in the left sidebar
4. You can now view and edit all tables! 

### Using MySQL Command Line

1. Open Command Prompt
2. Navigate to: `D:\xampp\mysql\bin`
3. Type: `mysql -u root -p`
4. Press Enter (no password needed for local)
5. Type: `USE university_db;`

---

## Common Database Operations

### View All Students
```sql
SELECT * FROM students;
```

### View Students with Names (Joining Tables)
```sql
SELECT s.student_id, u.name, u.email, d.name as department
FROM students s
JOIN users u ON s.user_id = u. id
JOIN departments d ON s.department_id = d.id;
```

### View a Student's Results
```sql
SELECT c.course_code, c.name, r.marks, r.grade
FROM results r
JOIN courses c ON r.course_id = c.id
WHERE r.student_id = 1;
```

### Calculate a Student's GPA
```sql
SELECT 
    SUM(c.credit * r.grade_point) / SUM(c.credit) as GPA
FROM results r
JOIN courses c ON r.course_id = c.id
WHERE r.student_id = 1 AND r.semester = 3;
```

---

## Backup and Restore

### Creating a Backup

1. Open phpMyAdmin
2. Select `university_db`
3. Click "Export" tab
4. Click "Go" to download the SQL file

### Restoring from Backup

1. Open phpMyAdmin
2. Create a new database (if needed)
3. Select the database
4. Click "Import" tab
5. Choose your SQL backup file
6. Click "Go"

---

## Tips for Beginners

1. **Always backup** before making changes
2. **Use phpMyAdmin** for easy viewing and editing
3. **Don't delete** the `id` column - it's the unique identifier
4. **Foreign keys** link tables together - don't break these links
5. **Test queries** in phpMyAdmin before using in code