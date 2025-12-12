<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');

$page_title = 'Admin Dashboard';

// Get statistics
$total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$total_faculty = $conn->query("SELECT COUNT(*) as count FROM faculty")->fetch_assoc()['count'];
$total_courses = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
$total_departments = $conn->query("SELECT COUNT(*) as count FROM departments")->fetch_assoc()['count'];

// Get recent results
$recent_results = $conn->query("
    SELECT r.*, s.name as student_name, s.student_id, c.course_name, c.course_code
    FROM results r
    JOIN students s ON r.student_id = s.id
    JOIN courses c ON r.course_id = c.id
    ORDER BY r.created_at DESC
    LIMIT 10
");

include __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon" style="background: #4CAF50;">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $total_students; ?></h3>
            <p>Total Students</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #2196F3;">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $total_faculty; ?></h3>
            <p>Total Faculty</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #FF9800;">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $total_courses; ?></h3>
            <p>Total Courses</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #9C27B0;">
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $total_departments; ?></h3>
            <p>Departments</p>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-header">
        <h3><i class="fas fa-chart-bar"></i> Recent Results</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $recent_results->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo clean($row['student_id']); ?></td>
                        <td><?php echo clean($row['student_name']); ?></td>
                        <td><?php echo clean($row['course_code'] . ' - ' . $row['course_name']); ?></td>
                        <td><?php echo clean($row['marks']); ?></td>
                        <td><span class="badge badge-<?php echo $row['grade'] == 'F' ? 'danger' : 'success'; ?>"><?php echo clean($row['grade']); ?></span></td>
                        <td>
                            <?php if ($row['published']): ?>
                                <span class="badge badge-success">Published</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Draft</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
