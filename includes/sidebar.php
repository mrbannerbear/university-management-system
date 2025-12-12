<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-university"></i>
        <h2>UMS</h2>
    </div>
    <nav class="sidebar-nav">
        <?php if (hasRole('admin')): ?>
            <a href="/admin/index.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="/admin/departments.php" class="nav-item">
                <i class="fas fa-building"></i>
                <span>Departments</span>
            </a>
            <a href="/admin/students.php" class="nav-item">
                <i class="fas fa-user-graduate"></i>
                <span>Students</span>
            </a>
            <a href="/admin/faculty.php" class="nav-item">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Faculty</span>
            </a>
            <a href="/admin/courses.php" class="nav-item">
                <i class="fas fa-book"></i>
                <span>Courses</span>
            </a>
            <a href="/admin/assign-courses.php" class="nav-item">
                <i class="fas fa-user-tag"></i>
                <span>Assign Courses</span>
            </a>
            <a href="/admin/results.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Results</span>
            </a>
        <?php elseif (hasRole('faculty')): ?>
            <a href="/faculty/index.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="/faculty/my-courses.php" class="nav-item">
                <i class="fas fa-book"></i>
                <span>My Courses</span>
            </a>
            <a href="/faculty/enter-marks.php" class="nav-item">
                <i class="fas fa-edit"></i>
                <span>Enter Marks</span>
            </a>
            <a href="/faculty/view-results.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>View Results</span>
            </a>
        <?php elseif (hasRole('student')): ?>
            <a href="/student/index.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="/student/profile.php" class="nav-item">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </a>
            <a href="/student/results.php" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>My Results</span>
            </a>
        <?php endif; ?>
        <a href="/logout.php" class="nav-item nav-logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </nav>
</aside>
