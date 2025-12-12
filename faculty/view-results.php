<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('faculty');

$page_title = 'View Results';

// Get faculty info
$stmt = $conn->prepare("SELECT * FROM faculty WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$faculty_info = $result->fetch_assoc();
$stmt->close();

$faculty_id = $faculty_info['id'];

// Get results for courses taught by this faculty
$stmt = $conn->prepare("
    SELECT r.*, s.name as student_name, s.student_id,
           c.course_code, c.course_name, c.credit
    FROM results r
    JOIN students s ON r.student_id = s.id
    JOIN courses c ON r.course_id = c.id
    WHERE r.faculty_id = ?
    ORDER BY r.created_at DESC
");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$results = $stmt->get_result();

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-chart-bar"></i> View Results</h2>
</div>

<div class="content-card">
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
                        <th>Grade Point</th>
                        <th>Semester</th>
                        <th>Academic Year</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($results->num_rows > 0): ?>
                        <?php while ($row = $results->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo clean($row['student_id']); ?></td>
                            <td><?php echo clean($row['student_name']); ?></td>
                            <td><?php echo clean($row['course_code'] . ' - ' . $row['course_name']); ?></td>
                            <td><?php echo $row['marks']; ?></td>
                            <td><span class="badge badge-<?php echo $row['grade'] == 'F' ? 'danger' : 'success'; ?>"><?php echo clean($row['grade']); ?></span></td>
                            <td><?php echo $row['grade_point']; ?></td>
                            <td><?php echo $row['semester']; ?></td>
                            <td><?php echo clean($row['academic_year']); ?></td>
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
                            <td colspan="9" style="text-align: center;">No results found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
