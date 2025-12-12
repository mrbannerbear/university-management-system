<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('faculty');

$page_title = 'My Courses';

// Get faculty info
$stmt = $conn->prepare("SELECT * FROM faculty WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$faculty_info = $result->fetch_assoc();
$stmt->close();

$faculty_id = $faculty_info['id'];

// Get assigned courses
$stmt = $conn->prepare("
    SELECT c.*, d.name as dept_name, 
           (SELECT COUNT(*) FROM results WHERE course_id = c.id AND faculty_id = ?) as students_count
    FROM courses c
    JOIN faculty_courses fc ON c.id = fc.course_id
    LEFT JOIN departments d ON c.department_id = d.id
    WHERE fc.faculty_id = ?
    ORDER BY c.course_code
");
$stmt->bind_param("ii", $faculty_id, $faculty_id);
$stmt->execute();
$courses = $stmt->get_result();

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-book"></i> My Assigned Courses</h2>
</div>

<div class="content-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Credit Hours</th>
                        <th>Department</th>
                        <th>Semester</th>
                        <th>Students</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($courses->num_rows > 0): ?>
                        <?php while ($course = $courses->fetch_assoc()): ?>
                        <tr>
                            <td><span class="badge badge-primary"><?php echo clean($course['course_code']); ?></span></td>
                            <td><?php echo clean($course['course_name']); ?></td>
                            <td><?php echo $course['credit']; ?></td>
                            <td><?php echo clean($course['dept_name'] ?? 'N/A'); ?></td>
                            <td><?php echo $course['semester']; ?></td>
                            <td><?php echo $course['students_count']; ?></td>
                            <td>
                                <a href="/faculty/enter-marks.php?course_id=<?php echo $course['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Enter Marks
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No courses assigned yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
