# Frontend Documentation 📱

## What is the Frontend?

The **frontend** is everything you see and interact with in your web browser. Think of it like the "face" of the application - the buttons you click, the forms you fill out, and the data you see on screen.

---

## Folder Structure

```
frontend/
├── index.html          # Login page (the first page users see)
├── assets/             # All the styling and code files
│   ├── css/            # How things look (colors, fonts, spacing)
│   └── js/             # How things work (what happens when you click)
├── components/         # Reusable pieces (like sidebars)
├── admin/              # Pages only admins can see
├── faculty/            # Pages only faculty can see
└── student/            # Pages only students can see
```

---

## Pages Explained

### Login Page (`index.html`)

This is the first page everyone sees.  Users enter their email and password to log in.

- **What happens when you log in?**
  1. You enter your email and password
  2. The system checks if they're correct
  3. If correct, you're sent to your dashboard based on your role (admin, faculty, or student)

---

### Admin Pages (`admin/` folder)

Admins are like the managers of the system. They can do everything! 

| Page | What It Does |
|------|--------------|
| `dashboard.html` | Shows a summary:  total students, faculty, courses, etc. |
| `students.html` | Add, edit, or remove students |
| `faculty.html` | Add, edit, or remove faculty members |
| `courses.html` | Add, edit, or remove courses |
| `departments.html` | Add, edit, or remove departments |
| `assign-courses.html` | Assign which faculty teaches which course |
| `results.html` | View all results and publish/unpublish them |

---

### Faculty Pages (`faculty/` folder)

Faculty members are teachers.  They can manage their courses and enter student marks.

| Page | What It Does |
|------|--------------|
| `dashboard.html` | Shows summary of their assigned courses and recent results |
| `my-courses.html` | View all courses assigned to them |
| `enter-marks.html` | Enter marks for students in their courses |
| `view-results.html` | View all results they've entered |

---

### Student Pages (`student/` folder)

Students can only view their own information.

| Page | What It Does |
|------|--------------|
| `dashboard.html` | Shows their info:  GPA, CGPA, total courses |
| `profile.html` | View their personal details |
| `results.html` | View their results by semester |
| `print-result.html` | Print-friendly version of their results |

---

## CSS Files (How Things Look)

CSS files control the appearance of the website.

| File | What It Controls |
|------|------------------|
| `style.css` | Main styling for the entire website |
| `variables.css` | Colors and sizes used throughout (like a color palette) |
| `components.css` | Styling for buttons, cards, modals, tables |
| `animations.css` | Smooth transitions and animations |

---

## JavaScript Files (How Things Work)

JavaScript makes the website interactive. 

| File | What It Does |
|------|--------------|
| `api.js` | Talks to the backend (sends and receives data) |
| `auth.js` | Handles login, logout, and checking if user is logged in |
| `utils.js` | Helper functions used everywhere (like showing messages) |
| `admin.js` | All the functions for admin pages |
| `faculty.js` | All the functions for faculty pages |
| `student.js` | All the functions for student pages |
| `components.js` | Loads reusable parts like sidebars |
| `router.js` | Helps with navigation between pages |

---

## How Data Flows

Here's what happens when you do something (like view students):

```
1. You open the Students page
        ↓
2. JavaScript (admin.js) runs
        ↓
3. It calls the API (api.js) to get student data
        ↓
4. API talks to the backend (PHP files)
        ↓
5. Backend gets data from database
        ↓
6. Data comes back through the same path
        ↓
7. JavaScript displays the data in a table
```

---

## Common Functions Explained

### `showModal(modalId)`
Opens a popup window (like when you click "Add Student")

### `hideModal(modalId)`
Closes the popup window

### `showToast(title, message, type)`
Shows a small notification message (success, error, etc.)

### `showLoading()` / `hideLoading()`
Shows/hides a loading spinner while data is being fetched

### `escapeHtml(text)`
Makes text safe to display (prevents hacking attempts)

---

## How to Add a New Page

1. **Create the HTML file** in the appropriate folder (admin/faculty/student)
2. **Include the necessary CSS files** in the `<head>` section
3. **Include the necessary JavaScript files** at the bottom
4. **Add initialization code** to load data when page opens
5. **Add a link** in the sidebar component

---

## Tips for Beginners

1. **Don't panic!** If something breaks, check the browser console (press F12)
2. **Console errors** tell you exactly what went wrong and where
3. **Clear cache** (Ctrl+Shift+R) if changes don't appear
4. **Test one thing at a time** when making changes