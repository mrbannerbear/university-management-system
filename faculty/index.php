<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('faculty');

$page_title = 'Faculty Dashboard';

// Get faculty info
$stmt = $conn->prepare("SELECT * FROM faculty WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$faculty_info = $result->fetch_assoc();
$stmt->close();

$faculty_id = $faculty_info['id'];

// Get total assigned courses
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM faculty_courses WHERE faculty_id = ?");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$total_courses = $result->fetch_assoc()['count'];
$stmt->close();

// Get total students in assigned courses
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT r.student_id) as count
    FROM results r
    JOIN faculty_courses fc ON r.course_id = fc.course_id
    WHERE fc.faculty_id = ?
");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$total_students = $result->fetch_assoc()['count'];
$stmt->close();

// Get total results entered
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM results WHERE faculty_id = ?");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
$total_results = $result->fetch_assoc()['count'];
$stmt->close();

// Get recent results
$stmt = $conn->prepare("
    SELECT r.*, s.name as student_name, s.student_id, c.course_name, c.course_code
    FROM results r
    JOIN students s ON r.student_id = s.id
    JOIN courses c ON r.course_id = c.id
    WHERE r.faculty_id = ?
    ORDER BY r.created_at DESC
    LIMIT 10
");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$recent_results = $stmt->get_result();

include __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-icon" style="background: #4CAF50;">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $total_courses; ?></h3>
            <p>Assigned Courses</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #2196F3;">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $total_students; ?></h3>
            <p>Total Students</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #FF9800;">
            <i class="fas fa-chart-bar"></i>
        </div>
        <div class="stat-details">
            <h3><?php echo $total_results; ?></h3>
            <p>Results Entered</p>
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
                    <?php if ($recent_results->num_rows > 0): ?>
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
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No results entered yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
