<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');

$page_title = 'My Profile';

// Get student info
$stmt = $conn->prepare("SELECT s.*, d.name as dept_name, d.code as dept_code, u.email 
                        FROM students s 
                        LEFT JOIN departments d ON s.department_id = d.id
                        LEFT JOIN users u ON s.user_id = u.id
                        WHERE s.user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$student_info = $result->fetch_assoc();
$stmt->close();

$student_id = $student_info['id'];

// Calculate CGPA
$cgpa = calculateCGPA($conn, $student_id);

include __DIR__ . '/../includes/header.php';
?>

<div class="content-card">
    <div class="card-header">
        <h3><i class="fas fa-user"></i> Student Profile</h3>
    </div>
    <div class="card-body">
        <div class="profile-details">
            <div class="profile-section">
                <h4>Personal Information</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Student ID:</label>
                        <span><?php echo clean($student_info['student_id']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Full Name:</label>
                        <span><?php echo clean($student_info['name']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email Address:</label>
                        <span><?php echo clean($student_info['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Department:</label>
                        <span><?php echo clean($student_info['dept_name']); ?> (<?php echo clean($student_info['dept_code']); ?>)</span>
                    </div>
                </div>
            </div>
            
            <div class="profile-section">
                <h4>Academic Information</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Current Year:</label>
                        <span>Year <?php echo $student_info['year']; ?></span>
                    </div>
                    <div class="info-item">
                        <label>Current Semester:</label>
                        <span>Semester <?php echo $student_info['semester']; ?></span>
                    </div>
                    <div class="info-item">
                        <label>CGPA:</label>
                        <span class="cgpa-badge"><?php echo number_format($cgpa, 2); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Enrollment Date:</label>
                        <span><?php echo formatDate($student_info['created_at']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
