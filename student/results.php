<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');

$page_title = 'My Results';

// Get student info
$stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$student_info = $result->fetch_assoc();
$stmt->close();

$student_id = $student_info['id'];

// Get results grouped by semester
$stmt = $conn->prepare("
    SELECT r.*, c.course_code, c.course_name, c.credit, r.semester
    FROM results r
    JOIN courses c ON r.course_id = c.id
    WHERE r.student_id = ? AND r.published = 1
    ORDER BY r.semester DESC, c.course_code
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$all_results = $stmt->get_result();

// Group results by semester
$results_by_semester = [];
while ($row = $all_results->fetch_assoc()) {
    $sem = $row['semester'];
    if (!isset($results_by_semester[$sem])) {
        $results_by_semester[$sem] = [];
    }
    $results_by_semester[$sem][] = $row;
}

// Calculate overall CGPA
$cgpa = calculateCGPA($conn, $student_id);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-chart-line"></i> My Results</h2>
    <a href="/student/print-result.php" class="btn btn-primary" target="_blank">
        <i class="fas fa-print"></i> Print Result
    </a>
</div>

<div class="content-card">
    <div class="card-header">
        <h3>Overall Performance</h3>
    </div>
    <div class="card-body">
        <div class="cgpa-display">
            <div class="cgpa-box">
                <h4>Cumulative GPA (CGPA)</h4>
                <div class="cgpa-value"><?php echo number_format($cgpa, 2); ?></div>
            </div>
        </div>
    </div>
</div>

<?php foreach ($results_by_semester as $semester => $results): ?>
<div class="content-card">
    <div class="card-header">
        <h3>Semester <?php echo $semester; ?> Results</h3>
        <span class="semester-gpa">
            GPA: <?php echo number_format(calculateGPA($conn, $student_id, $semester), 2); ?>
        </span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Credit</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Grade Point</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_points = 0;
                    $total_credits = 0;
                    foreach ($results as $result): 
                        $total_points += $result['grade_point'] * $result['credit'];
                        $total_credits += $result['credit'];
                    ?>
                    <tr>
                        <td><span class="badge badge-primary"><?php echo clean($result['course_code']); ?></span></td>
                        <td><?php echo clean($result['course_name']); ?></td>
                        <td><?php echo $result['credit']; ?></td>
                        <td><?php echo $result['marks']; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $result['grade'] == 'F' ? 'danger' : 'success'; ?>">
                                <?php echo clean($result['grade']); ?>
                            </span>
                        </td>
                        <td><?php echo $result['grade_point']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="2"><strong>Semester Total</strong></td>
                        <td><strong><?php echo $total_credits; ?></strong></td>
                        <td colspan="2"><strong>Semester GPA:</strong></td>
                        <td><strong><?php echo number_format($total_credits > 0 ? $total_points / $total_credits : 0, 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php if (empty($results_by_semester)): ?>
<div class="content-card">
    <div class="card-body">
        <p style="text-align: center; color: #999;">No published results available yet.</p>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
