# Backend Documentation ⚙️

## What is the Backend?

The **backend** is the "behind the scenes" part of the application. It's like the kitchen in a restaurant - customers (users) don't see it, but that's where all the real work happens! 

The backend: 
- Receives requests from the frontend
- Processes the requests (like checking passwords, calculating grades)
- Talks to the database to get or save data
- Sends responses back to the frontend

---

## Technology Used

- **PHP** - The programming language used for backend
- **MySQL** - The database where all data is stored

---

## Folder Structure

```
api/
├── config/
│   └── database.php    # Connects to the database
├── helpers/
│   └── functions.php   # Helper functions used everywhere
├── auth/               # Login/logout related
│   ├── login.php
│   ├── logout.php
│   └── check-session.php
├── admin/              # Admin-only operations
│   ├── dashboard.php
│   ├── students.php
│   ├── faculty.php
│   ├── courses.php
│   ├── departments.php
│   ├── assign-courses.php
│   └── results.php
├── faculty/            # Faculty operations
│   ├── dashboard.php
│   ├── my-courses.php
│   ├── enter-marks. php
│   └── view-results. php
└── student/            # Student operations
    ├── dashboard.php
    ├── profile.php
    ├── results. php
    └── gpa. php
```

---

## How the Backend Works

### Step 1: Frontend Sends a Request

When you click a button (like "Save Student"), the frontend sends a request to the backend. 

Example request:
```
POST /api/admin/students. php
Data: {name: "John", email: "john@example.com", ... }
```

### Step 2: Backend Receives the Request

The PHP file receives this request and processes it. 

### Step 3: Backend Talks to Database

The PHP code runs a database query to save or fetch data.

### Step 4: Backend Sends Response

The backend sends back a response (usually in JSON format):

```json
{
    "success": true,
    "message": "Student added successfully",
    "data": {... }
}
```

---

## Key Files Explained

### `config/database.php`

This file connects to the database.  Think of it as the "key" that opens the database door.

```php
// What it contains: 
- Database host (where the database is located)
- Database username
- Database password
- Database name
```

**Important settings:**
| Setting | Default Value | What It Means |
|---------|---------------|---------------|
| DB_HOST | localhost | Database is on the same computer |
| DB_USER | root | Username to access database |
| DB_PASS | (empty) | Password (empty for local development) |
| DB_NAME | university_db | Name of our database |

---

### `helpers/functions.php`

Contains useful functions used throughout the backend. 

| Function | What It Does |
|----------|--------------|
| `getJsonInput()` | Gets data sent from the frontend |
| `requireRole($role)` | Checks if user has permission (admin/faculty/student) |
| `calculateGrade($marks)` | Converts marks to grade (A+, A, B+, etc.) |

---

## API Endpoints Explained

### What is an API Endpoint?

An **endpoint** is like a specific address the frontend can send requests to.  Each endpoint does a specific job.

---

### Authentication Endpoints (`auth/`)

| Endpoint | Method | What It Does |
|----------|--------|--------------|
| `login.php` | POST | Checks email/password and logs user in |
| `logout.php` | POST | Logs the user out |
| `check-session.php` | GET | Checks if user is currently logged in |

---

### Admin Endpoints (`admin/`)

#### `students.php`

| Method | What It Does |
|--------|--------------|
| GET | Get all students (or one specific student) |
| POST | Add a new student |
| PUT | Update an existing student |
| DELETE | Remove a student |

#### `faculty.php`

| Method | What It Does |
|--------|--------------|
| GET | Get all faculty members |
| POST | Add a new faculty member |
| PUT | Update faculty details |
| DELETE | Remove a faculty member |

#### `courses.php`

| Method | What It Does |
|--------|--------------|
| GET | Get all courses |
| POST | Add a new course |
| PUT | Update course details |
| DELETE | Remove a course |

#### `departments.php`

| Method | What It Does |
|--------|--------------|
| GET | Get all departments |
| POST | Add a new department |
| PUT | Update department details |
| DELETE | Remove a department |

#### `assign-courses.php`

| Method | What It Does |
|--------|--------------|
| GET | Get all faculty-course assignments |
| POST | Assign a course to a faculty member |
| DELETE | Remove an assignment |

#### `results.php`

| Method | What It Does |
|--------|--------------|
| GET | Get all results |
| PUT | Publish or unpublish a result |

---

### Faculty Endpoints (`faculty/`)

| Endpoint | Method | What It Does |
|----------|--------|--------------|
| `dashboard.php` | GET | Get faculty's statistics |
| `my-courses.php` | GET | Get courses assigned to this faculty |
| `enter-marks.php` | GET | Get students for a course |
| `enter-marks.php` | POST | Save marks for a student |
| `view-results.php` | GET | Get all results entered by this faculty |

---

### Student Endpoints (`student/`)

| Endpoint | Method | What It Does |
|----------|--------|--------------|
| `dashboard.php` | GET | Get student's info and statistics |
| `profile.php` | GET | Get student's profile details |
| `results.php` | GET | Get student's results by semester |
| `gpa.php` | GET | Get student's GPA and CGPA |

---

## HTTP Methods Explained

| Method | Used For | Example |
|--------|----------|---------|
| GET | Getting/reading data | View list of students |
| POST | Creating new data | Add a new student |
| PUT | Updating existing data | Edit a student's details |
| DELETE | Removing data | Delete a student |

---

## Response Format

All API responses follow this format:

### Success Response
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": { ...  }
}
```

### Error Response
```json
{
    "success": false,
    "error": "Something went wrong"
}
```

---

## Sessions (Remembering Who's Logged In)

When a user logs in, the backend creates a **session**. Think of it like a wristband at an event - it proves you're allowed to be there.

The session stores:
- `user_id` - Who this user is
- `name` - User's name
- `email` - User's email
- `role` - What they can do (admin/faculty/student)
- `department_id` - Which department they belong to

---

## Security Features

### Password Hashing
Passwords are never stored as plain text. They're scrambled using a secure method so even if someone sees the database, they can't read the passwords.

### Role Checking
Every API endpoint checks if the user has permission.  A student can't access admin pages! 

### Session Validation
Every request checks if the user is logged in and their session is valid.

---

## Common Errors and Solutions

| Error | Meaning | Solution |
|-------|---------|----------|
| 401 Unauthorized | Not logged in | Log in first |
| 403 Forbidden | Don't have permission | Use correct account type |
| 404 Not Found | Resource doesn't exist | Check the ID |
| 500 Server Error | Something broke in PHP | Check error logs |

---

## Tips for Beginners

1. **Check the error logs** at `D:\xampp\apache\logs\error.log`
2. **Test APIs** using your browser for GET requests
3. **Use browser dev tools** (F12 > Network tab) to see requests/responses
4. **Always validate data** before saving to database
5. **Never trust user input** - always check and clean it