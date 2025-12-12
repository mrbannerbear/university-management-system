# University Result Management System

A modern, educational web-based platform for managing academic results in universities. Built with **separated frontend and backend architecture** using PHP REST APIs, MySQL, HTML5, CSS3, and modern JavaScript. This system provides role-based access for administrators, faculty, and students with a beautiful, animated UI.

![University Management System](https://img.shields.io/badge/PHP-7.4+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)
![Architecture](https://img.shields.io/badge/Architecture-REST_API-green.svg)

## 🏗️ Architecture

This project follows a **clean separation of concerns** with frontend and backend completely decoupled:

### Backend (REST API)
- **Location**: `/api/` directory
- **Technology**: Pure PHP with JSON responses
- **Purpose**: Data handling, business logic, authentication
- **Endpoints**: RESTful APIs for all operations (GET, POST, PUT, DELETE)
- **Session Management**: Secure session-based authentication

### Frontend
- **Location**: `/frontend/` directory
- **Technology**: Pure HTML5, CSS3, Modern JavaScript (ES6+)
- **Purpose**: User interface and user experience
- **Communication**: Fetch API for asynchronous data operations
- **Styling**: Modern CSS with animations and responsive design

### Benefits of This Architecture
- ✅ **Separation of Concerns**: Clean separation between UI and business logic
- ✅ **Maintainability**: Easy to update frontend or backend independently
- ✅ **Scalability**: Can easily add mobile apps or other frontends
- ✅ **Educational**: Perfect for learning modern web development practices
- ✅ **Testing**: Backend APIs can be tested independently

## 🚀 Features

### Admin Module
- **Dashboard** with comprehensive statistics
- **Manage Departments** - Add, edit, and delete departments
- **Manage Students** - Complete CRUD operations for student records
- **Manage Faculty** - Add, edit, and delete faculty members
- **Manage Courses** - Course management with credit hours
- **Assign Courses to Faculty** - Link faculty members to courses
- **View All Results** - Filter and manage all student results
- **Publish/Unpublish Results** - Control result visibility

### Faculty Module
- **Dashboard** with assigned courses overview
- **View Assigned Courses** - See all courses taught
- **Enter/Update Marks** - Input student marks with auto-grade calculation
- **View Results** - Monitor all results entered
- **Grade Calculation** - Automatic grade assignment based on marks

### Student Module
- **Dashboard** with GPA/CGPA overview
- **View Profile** - Personal and academic information
- **View Results** - Semester-wise result display
- **Calculate GPA/CGPA** - Automatic grade point calculations
- **Print Result** - Download print-friendly result sheet

### Result Generation
- Automatic grade calculation:
  - A+ (90-100): 4.00 GPA
  - A (85-89): 3.75 GPA
  - A- (80-84): 3.50 GPA
  - B+ (75-79): 3.25 GPA
  - B (70-74): 3.00 GPA
  - B- (65-69): 2.75 GPA
  - C+ (60-64): 2.50 GPA
  - C (55-59): 2.25 GPA
  - D (50-54): 2.00 GPA
  - F (<50): 0.00 GPA
- Semester GPA calculation
- Cumulative GPA (CGPA) calculation

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **XAMPP** / **WAMP** / **MAMP** (Apache, MySQL, PHP)
  - PHP 7.4 or higher
  - MySQL 5.7 or higher
  - Apache Web Server

## 🔧 Installation

### Step 1: Download/Clone the Project

```bash
git clone https://github.com/yourusername/university-management-system.git
```

Or download the ZIP file and extract it.

### Step 2: Move to Web Server Directory

Move the project folder to your web server's document root:

- **XAMPP**: `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/` (Linux)
- **WAMP**: `C:\wamp\www\`
- **MAMP**: `/Applications/MAMP/htdocs/` (Mac)

### Step 3: Database Setup

1. Start your Apache and MySQL servers
2. Open phpMyAdmin (usually `http://localhost/phpmyadmin`)
3. Import the database:
   - Click on "New" to create a new database (optional, the SQL will create it)
   - Click on "Import" tab
   - Click "Choose File" and select `database/schema.sql`
   - Click "Go" to import

The schema will create the `university_db` database with all tables and sample data.

### Step 4: Configure Database Connection

Edit `config/database.php` if your database credentials are different:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your MySQL password
define('DB_NAME', 'university_db');
```

### Step 5: Access the Application

#### New Frontend (Recommended)
Open your web browser and navigate to:

```
http://localhost/university-management-system/frontend/
```

This will load the modern, API-driven frontend.

#### Legacy Frontend (For Reference)
The old PHP-based frontend is still available at:

```
http://localhost/university-management-system/
```

## 🔌 API Documentation

### Authentication Endpoints

#### Login
```http
POST /api/auth/login.php
Content-Type: application/json

{
  "email": "admin@university.com",
  "password": "admin123"
}

Response:
{
  "success": true,
  "data": {
    "user_id": 1,
    "name": "Admin User",
    "email": "admin@university.com",
    "role": "admin",
    "department_id": null
  }
}
```

#### Check Session
```http
GET /api/auth/check-session.php

Response:
{
  "success": true,
  "data": {
    "logged_in": true,
    "user_id": 1,
    "name": "Admin User",
    "email": "admin@university.com",
    "role": "admin"
  }
}
```

#### Logout
```http
POST /api/auth/logout.php

Response:
{
  "success": true,
  "message": "Logged out successfully"
}
```

### Admin Endpoints

#### Get Dashboard Statistics
```http
GET /api/admin/dashboard.php
```

#### Manage Students
```http
GET /api/admin/students.php           # Get all students
GET /api/admin/students.php?id=1      # Get specific student
POST /api/admin/students.php          # Create student
PUT /api/admin/students.php           # Update student
DELETE /api/admin/students.php        # Delete student
```

Similar endpoints exist for:
- `/api/admin/faculty.php` - Faculty management
- `/api/admin/courses.php` - Course management
- `/api/admin/departments.php` - Department management
- `/api/admin/assign-courses.php` - Course assignments
- `/api/admin/results.php` - Results management

### Faculty Endpoints
- `/api/faculty/dashboard.php` - Faculty statistics
- `/api/faculty/my-courses.php` - Get assigned courses
- `/api/faculty/enter-marks.php` - Submit student marks
- `/api/faculty/view-results.php` - View entered results

### Student Endpoints
- `/api/student/dashboard.php` - Student statistics
- `/api/student/profile.php` - Student information
- `/api/student/results.php` - Student results
- `/api/student/gpa.php` - GPA/CGPA calculations

## 📚 For Software Engineering Students

This project demonstrates several important concepts:

### 1. **Separation of Concerns**
The backend handles all data operations and returns JSON, while the frontend handles presentation and user interaction.

### 2. **RESTful API Design**
All backend endpoints follow REST principles with appropriate HTTP methods (GET, POST, PUT, DELETE).

### 3. **Single Page Application (SPA) Principles**
The frontend uses JavaScript to dynamically load and display data without full page reloads.

### 4. **Modern JavaScript Practices**
- Async/await for asynchronous operations
- Fetch API for HTTP requests
- ES6+ features (arrow functions, template literals, destructuring)
- Modular code organization

### 5. **CSS Architecture**
- CSS variables for theming
- Animation keyframes for smooth transitions
- Responsive design with media queries
- Component-based styling

### 6. **Security Best Practices**
- Password hashing with PHP's `password_hash()`
- Prepared statements for SQL injection prevention
- Session-based authentication
- Input sanitization with `htmlspecialchars()`
- CORS headers for API security

### 7. **Code Organization**
- Modular file structure
- Reusable components
- Helper functions for common operations
- Consistent naming conventions

## 🔐 Default Login Credentials

### Admin Access
- **Email**: admin@university.com
- **Password**: admin123

### Faculty Access
- **Email**: faculty1@university.com
- **Password**: faculty123
- **Email**: faculty2@university.com
- **Password**: faculty123
- **Email**: faculty3@university.com
- **Password**: faculty123

### Student Access
- **Email**: student1@university.com
- **Password**: student123
- **Email**: student2@university.com
- **Password**: student123
- (student3-10@university.com / student123)

## 📁 Project Structure

```
university-management-system/
├── api/                        # Backend REST API
│   ├── config/
│   │   └── database.php       # Database connection
│   ├── auth/
│   │   ├── login.php          # POST: User authentication
│   │   ├── logout.php         # POST: Destroy session
│   │   └── check-session.php  # GET: Verify login status
│   ├── admin/
│   │   ├── dashboard.php      # GET: Admin statistics
│   │   ├── students.php       # CRUD: Student management
│   │   ├── faculty.php        # CRUD: Faculty management
│   │   ├── courses.php        # CRUD: Course management
│   │   ├── departments.php    # CRUD: Department management
│   │   ├── assign-courses.php # Assign courses to faculty
│   │   └── results.php        # Manage all results
│   ├── faculty/
│   │   ├── dashboard.php      # GET: Faculty statistics
│   │   ├── my-courses.php     # GET: Assigned courses
│   │   ├── enter-marks.php    # POST: Submit student marks
│   │   └── view-results.php   # GET: View entered results
│   ├── student/
│   │   ├── dashboard.php      # GET: Student statistics
│   │   ├── profile.php        # GET: Student information
│   │   ├── results.php        # GET: Student results
│   │   └── gpa.php           # GET: GPA/CGPA calculation
│   └── helpers/
│       └── functions.php      # Helper functions
├── frontend/                   # Frontend Application
│   ├── index.html             # Login page
│   ├── admin/
│   │   ├── dashboard.html     # Admin dashboard
│   │   ├── students.html      # Manage students
│   │   ├── faculty.html       # Manage faculty
│   │   ├── courses.html       # Manage courses
│   │   ├── departments.html   # Manage departments
│   │   ├── assign-courses.html # Assign courses
│   │   └── results.html       # View results
│   ├── faculty/
│   │   ├── dashboard.html     # Faculty dashboard
│   │   ├── my-courses.html    # View assigned courses
│   │   ├── enter-marks.html   # Enter student marks
│   │   └── view-results.html  # View results
│   ├── student/
│   │   ├── dashboard.html     # Student dashboard
│   │   ├── profile.html       # View profile
│   │   ├── results.html       # View results
│   │   └── print-result.html  # Print results
│   ├── assets/
│   │   ├── css/
│   │   │   ├── style.css      # Main stylesheet
│   │   │   └── animations.css # CSS animations
│   │   └── js/
│   │       ├── api.js         # API helper functions
│   │       ├── auth.js        # Authentication logic
│   │       ├── admin.js       # Admin functionality
│   │       ├── faculty.js     # Faculty functionality
│   │       ├── student.js     # Student functionality
│   │       └── utils.js       # Utility functions
│   └── components/
│       ├── sidebar-admin.html # Admin sidebar
│       ├── sidebar-faculty.html # Faculty sidebar
│       └── sidebar-student.html # Student sidebar
├── database/
│   └── schema.sql             # Database schema + sample data
├── config/                     # Legacy config (kept for compatibility)
│   └── database.php
├── admin/                      # Legacy admin pages (kept for reference)
├── faculty/                    # Legacy faculty pages (kept for reference)
├── student/                    # Legacy student pages (kept for reference)
├── includes/                   # Legacy includes (kept for reference)
└── README.md
```

## 🎨 UI Features

- **Modern, Clean Design** - Beautiful indigo color scheme with professional aesthetics
- **Fully Responsive** - Works seamlessly on desktop, tablet, and mobile devices
- **CSS Animations** - Smooth fade-in, slide-in, and scale animations throughout
  - Page load animations with staggered delays
  - Hover effects with subtle scaling
  - Modal transitions with scale and fade
  - Toast notifications sliding from right
  - Skeleton loading states with shimmer effect
- **Card-Based Layout** - Clean, organized dashboard with shadow effects
- **Font Awesome Icons** - Beautiful iconography throughout the interface
- **Interactive Components**
  - Animated stat cards with gradient icons
  - Smooth hover effects on tables and buttons
  - Modal dialogs with backdrop blur
  - Toast notifications with auto-dismiss
  - Loading spinners and overlays
- **Modern Typography** - Inter font family for clean, readable text
- **Color-Coded Elements**
  - Success states in emerald green
  - Errors in red
  - Warnings in amber
  - Info in blue
  - Primary actions in indigo

## 🔒 Security Features

- **Password Hashing** - Using PHP's `password_hash()` function
- **Prepared Statements** - SQL injection prevention
- **Session Management** - Secure session handling
- **Role-Based Access Control** - Proper authorization checks
- **Input Sanitization** - Using `htmlspecialchars()` for output
- **CSRF Protection** - Form token validation (can be enhanced)

## 🛠️ Customization

### Changing the Color Scheme

Edit `/frontend/assets/css/style.css` and modify the CSS variables:

```css
:root {
    --primary: #4F46E5;           /* Change to your brand color */
    --primary-hover: #4338CA;
    --secondary: #10B981;
    /* ... other colors ... */
}
```

### Changing the Grading Scale

Edit `/api/helpers/functions.php`:

```php
function calculateGrade($marks) {
    if ($marks >= 90) {
        return ['grade' => 'A+', 'grade_point' => 4.00];
    }
    // Modify as needed
}
```

### Adding New API Endpoints

1. Create a new PHP file in the appropriate `/api/` subdirectory
2. Include required files:
```php
<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';

requireRole('admin'); // or 'faculty' or 'student'

// Handle different HTTP methods
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request
}
```
3. Return JSON responses:
```php
echo json_encode(['success' => true, 'data' => $data]);
```

### Adding New Frontend Pages

1. Create HTML file in `/frontend/admin/`, `/frontend/faculty/`, or `/frontend/student/`
2. Include CSS and JS files:
```html
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/animations.css">
<script src="../assets/js/api.js"></script>
<script src="../assets/js/auth.js"></script>
```
3. Load sidebar component:
```javascript
loadComponent('sidebar-container', '../components/sidebar-admin.html');
```
4. Protect page with authentication:
```javascript
protectPage('admin').then(user => {
    // Page logic here
});
```

## 📊 Database Schema

### Key Tables

1. **users** - Stores all user accounts (admin, faculty, students)
2. **departments** - Academic departments
3. **students** - Student records linked to users
4. **faculty** - Faculty records linked to users
5. **courses** - Course information
6. **faculty_courses** - Course assignments to faculty
7. **results** - Student marks and grades

## 🐛 Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in `config/database.php`
- Ensure database exists and is imported correctly

### Login Not Working
- Clear browser cache and cookies
- Verify user exists in database
- Check session configuration in PHP

### Pages Not Loading
- Check file permissions (755 for directories, 644 for files)
- Verify Apache mod_rewrite is enabled
- Check Apache error logs

### Styling Issues
- Clear browser cache
- Check if CSS file path is correct
- Verify CSS file is accessible

## 📝 Future Enhancements

### Backend API
- JWT-based authentication as alternative to sessions
- Rate limiting for API endpoints
- API versioning (v1, v2, etc.)
- Pagination for large datasets
- Advanced filtering and sorting
- File upload for profile pictures
- Export data as PDF/Excel

### Frontend
- Progressive Web App (PWA) support
- Offline mode with service workers
- Real-time updates with WebSockets
- Advanced data visualization with Chart.js
- Dark mode toggle
- Multi-language support (i18n)
- Advanced search and filtering UI

### Features
- Email notifications for result publication
- SMS notifications
- Attendance management module
- Fee management system
- Timetable/schedule management
- Online examination system
- Discussion forum
- Assignment submission
- Library management
- Hostel management

### Mobile
- React Native mobile app
- Flutter mobile app
- API remains the same (benefit of separated architecture!)

## 🎓 Learning Outcomes

By studying this project, students will learn:

1. **Backend Development**
   - RESTful API design and implementation
   - Database operations with MySQLi
   - Session management and authentication
   - Security best practices

2. **Frontend Development**
   - Modern JavaScript (ES6+)
   - Asynchronous programming with Promises and Async/Await
   - DOM manipulation
   - Fetch API for HTTP requests
   - CSS animations and transitions
   - Responsive design principles

3. **Architecture & Design**
   - Separation of concerns
   - MVC-like pattern
   - Component-based design
   - Code organization and modularity

4. **Best Practices**
   - Code documentation
   - Error handling
   - User experience considerations
   - Security considerations

## 👥 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgments

- Font Awesome for icons
- PHP community for excellent documentation
- All contributors and testers

## 📧 Support

For support, email support@example.com or open an issue in the GitHub repository.

## 📸 Screenshots

### Login Page
![Login Page](screenshots/login.png)

### Admin Dashboard
![Admin Dashboard](screenshots/admin-dashboard.png)

### Faculty Dashboard
![Faculty Dashboard](screenshots/faculty-dashboard.png)

### Student Dashboard
![Student Dashboard](screenshots/student-dashboard.png)

### Result View
![Result View](screenshots/results.png)

---

**Note**: This is an educational project designed to teach modern web development practices, including frontend-backend separation, REST API design, and modern UI/UX principles. For production use, please implement additional security measures, thorough testing, and follow industry best practices.

**Made with ❤️ for educational purposes**

## 🆕 What's New in This Version

### Architecture Improvements
- ✅ Complete separation of frontend and backend
- ✅ RESTful API architecture with JSON responses
- ✅ Modular, maintainable code structure
- ✅ Modern JavaScript (no jQuery dependency)

### UI/UX Overhaul
- ✅ Modern indigo color scheme
- ✅ Smooth CSS animations throughout
- ✅ Responsive design with mobile support
- ✅ Professional gradient stat cards
- ✅ Toast notifications system
- ✅ Loading states and skeleton screens
- ✅ Modal dialogs with backdrop blur
- ✅ Inter font for better typography

### Developer Experience
- ✅ Clear project structure
- ✅ Reusable components
- ✅ Helper functions for common tasks
- ✅ Comprehensive API documentation
- ✅ Educational comments and documentation

### Legacy Support
- ✅ Original PHP files kept for reference
- ✅ Database schema unchanged
- ✅ Sample data preserved
- ✅ Backward compatible with existing database