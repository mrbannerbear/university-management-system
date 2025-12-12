# University Result Management System

A comprehensive web-based platform for managing academic results in universities. Built with PHP, MySQL, HTML, CSS, and JavaScript, this system provides role-based access for administrators, faculty, and students.

![University Management System](https://img.shields.io/badge/PHP-7.4+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

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

Open your web browser and navigate to:

```
http://localhost/university-management-system/
```

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
├── admin/                      # Admin module
│   ├── index.php              # Admin dashboard
│   ├── students.php           # Manage students
│   ├── faculty.php            # Manage faculty
│   ├── courses.php            # Manage courses
│   ├── departments.php        # Manage departments
│   ├── assign-courses.php     # Assign courses to faculty
│   └── results.php            # View all results
├── faculty/                    # Faculty module
│   ├── index.php              # Faculty dashboard
│   ├── my-courses.php         # View assigned courses
│   ├── enter-marks.php        # Enter/update marks
│   └── view-results.php       # View results
├── student/                    # Student module
│   ├── index.php              # Student dashboard
│   ├── profile.php            # View profile
│   ├── results.php            # View results
│   └── print-result.php       # Print result
├── assets/                     # Frontend assets
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   └── js/
│       └── main.js            # JavaScript functionality
├── config/                     # Configuration files
│   └── database.php           # Database connection
├── database/                   # Database files
│   └── schema.sql             # Database schema with sample data
├── includes/                   # Common includes
│   ├── functions.php          # Helper functions
│   ├── header.php             # Common header
│   ├── footer.php             # Common footer
│   └── sidebar.php            # Navigation sidebar
├── index.php                   # Login page
├── login.php                   # Login handler
├── logout.php                  # Logout handler
└── README.md                   # This file
```

## 🎨 UI Features

- **Modern, Clean Design** - Professional color scheme with intuitive layout
- **Responsive Design** - Works seamlessly on desktop, tablet, and mobile
- **Card-Based Layout** - Clean, organized dashboard widgets
- **Font Awesome Icons** - Visual elements throughout the interface
- **Modal Dialogs** - For add/edit operations
- **Data Tables** - Sortable, searchable tables
- **Form Validation** - Client and server-side validation
- **Toast Notifications** - Success/error messages
- **Print-Friendly Views** - Optimized result printing

## 🔒 Security Features

- **Password Hashing** - Using PHP's `password_hash()` function
- **Prepared Statements** - SQL injection prevention
- **Session Management** - Secure session handling
- **Role-Based Access Control** - Proper authorization checks
- **Input Sanitization** - Using `htmlspecialchars()` for output
- **CSRF Protection** - Form token validation (can be enhanced)

## 🛠️ Customization

### Changing the Grading Scale

Edit the `calculateGrade()` function in `includes/functions.php`:

```php
function calculateGrade($marks) {
    if ($marks >= 90) {
        return ['grade' => 'A+', 'grade_point' => 4.00];
    }
    // Modify as needed
}
```

### Adding New Fields

1. Modify the database schema in `database/schema.sql`
2. Update the corresponding PHP files in admin/faculty/student modules
3. Update the forms to include new fields

### Changing the Theme

Modify colors in `assets/css/style.css`:

```css
:root {
    --primary-color: #2196F3;  /* Change to your preferred color */
    --secondary-color: #1976D2;
    /* ... other colors ... */
}
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

- Email notifications for result publication
- PDF export for results
- Attendance management
- Fee management
- Timetable management
- Online exam system
- Student portal enhancements
- Mobile app

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

**Note**: This is a demonstration/educational project. For production use, please implement additional security measures, conduct thorough testing, and follow best practices for web application security.

**Made with ❤️ for educational purposes**