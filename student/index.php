<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');

$page_title = 'Student Dashboard';

// Get student info
$stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$student_info = $result->fetch_assoc();
$stmt->close();

$student_id = $student_info['id'];

// Calculate CGPA
$cgpa = calculateCGPA($conn, $student_id);

// Calculate current semester GPA
$current_gpa = calculateGPA($conn, $student_id, $student_info['semester']);

// Get total courses with results
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM results WHERE student_id = ? AND published = 1");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$total_courses = $result->fetch_assoc()['count'];
$stmt->close();

// Get department name
$dept_name = getDepartmentName($conn, $student_info['department_id']);

include __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon" style="background: #4CAF50;">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo number_format($cgpa, 2); ?></h3>
            <p>CGPA</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #2196F3;">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo number_format($current_gpa, 2); ?></h3>
            <p>Current Semester GPA</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #FF9800;">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $total_courses; ?></h3>
            <p>Courses Completed</p>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-header">
        <h3><i class="fas fa-user"></i> Profile Information</h3>
    </div>
    <div class="card-body">
        <div class="profile-info">
            <div class="info-row">
                <div class="info-label">Student ID:</div>
                <div class="info-value"><?php echo clean($student_info['student_id']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div class="info-value"><?php echo clean($student_info['name']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value"><?php echo clean($_SESSION['email']); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Department:</div>
                <div class="info-value"><?php echo clean($dept_name); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Current Year:</div>
                <div class="info-value">Year <?php echo $student_info['year']; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Current Semester:</div>
                <div class="info-value">Semester <?php echo $student_info['semester']; ?></div>
            </div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-header">
        <h3><i class="fas fa-chart-bar"></i> Quick Links</h3>
    </div>
    <div class="card-body">
        <div class="quick-links">
            <a href="/student/profile.php" class="quick-link-card">
                <i class="fas fa-user"></i>
                <span>View Profile</span>
            </a>
            <a href="/student/results.php" class="quick-link-card">
                <i class="fas fa-chart-line"></i>
                <span>View Results</span>
            </a>
            <a href="/student/print-result.php" class="quick-link-card">
                <i class="fas fa-print"></i>
                <span>Print Result</span>
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
