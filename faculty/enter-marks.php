<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('faculty');

$page_title = 'Enter/Update Marks';

// Get faculty info
$stmt = $conn->prepare("SELECT * FROM faculty WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$faculty_info = $result->fetch_assoc();
$stmt->close();

$faculty_id = $faculty_info['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $marks = $_POST['marks'];
    $semester = $_POST['semester'];
    $academic_year = $_POST['academic_year'];
    
    // Calculate grade
    $gradeData = calculateGrade($marks);
    $grade = $gradeData['grade'];
    $grade_point = $gradeData['grade_point'];
    
    // Check if result already exists
    $stmt = $conn->prepare("SELECT id FROM results WHERE student_id = ? AND course_id = ?");
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($existing) {
        // Update existing result
        $stmt = $conn->prepare("UPDATE results SET marks = ?, grade = ?, grade_point = ?, semester = ?, academic_year = ? WHERE id = ?");
        $stmt->bind_param("dssdsi", $marks, $grade, $grade_point, $semester, $academic_year, $existing['id']);
        $message = 'Marks updated successfully!';
    } else {
        // Insert new result
        $stmt = $conn->prepare("INSERT INTO results (student_id, course_id, marks, grade, grade_point, faculty_id, semester, academic_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iidsdiis", $student_id, $course_id, $marks, $grade, $grade_point, $faculty_id, $semester, $academic_year);
        $message = 'Marks entered successfully!';
    }
    
    if ($stmt->execute()) {
        setSuccessMessage($message);
    } else {
        setErrorMessage('Error saving marks.');
    }
    $stmt->close();
    
    header('Location: /faculty/enter-marks.php?course_id=' . $course_id);
    exit();
}

// Get course info if selected
$selected_course = null;
$students = null;

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    
    // Verify faculty has access to this course
    $stmt = $conn->prepare("
        SELECT c.*, d.name as dept_name
        FROM courses c
        JOIN faculty_courses fc ON c.id = fc.course_id
        LEFT JOIN departments d ON c.department_id = d.id
        WHERE c.id = ? AND fc.faculty_id = ?
    ");
    $stmt->bind_param("ii", $course_id, $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $selected_course = $result->fetch_assoc();
    $stmt->close();
    
    if ($selected_course) {
        // Get students for the same semester and department
        $stmt = $conn->prepare("
            SELECT s.*, 
                   r.marks, r.grade, r.grade_point, r.id as result_id, r.semester as result_semester, r.academic_year
            FROM students s
            LEFT JOIN results r ON s.id = r.student_id AND r.course_id = ?
            WHERE s.semester = ? AND s.department_id = ?
            ORDER BY s.student_id
        ");
        $stmt->bind_param("iii", $course_id, $selected_course['semester'], $selected_course['department_id']);
        $stmt->execute();
        $students = $stmt->get_result();
        $stmt->close();
    }
}

// Get assigned courses for dropdown
$stmt = $conn->prepare("
    SELECT c.*, d.name as dept_name
    FROM courses c
    JOIN faculty_courses fc ON c.id = fc.course_id
    LEFT JOIN departments d ON c.department_id = d.id
    WHERE fc.faculty_id = ?
    ORDER BY c.course_code
");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$courses = $stmt->get_result();

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-edit"></i> Enter/Update Marks</h2>
</div>

<div class="content-card">
    <div class="card-header">
        <h3>Select Course</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="form-inline">
            <div class="form-group">
                <label>Course:</label>
                <select name="course_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Select a Course --</option>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                    <option value="<?php echo $course['id']; ?>" <?php echo (isset($_GET['course_id']) && $_GET['course_id'] == $course['id']) ? 'selected' : ''; ?>>
                        <?php echo clean($course['course_code'] . ' - ' . $course['course_name']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<?php if ($selected_course): ?>
<div class="content-card">
    <div class="card-header">
        <h3><?php echo clean($selected_course['course_code'] . ' - ' . $selected_course['course_name']); ?></h3>
        <p>Department: <?php echo clean($selected_course['dept_name']); ?> | Credits: <?php echo $selected_course['credit']; ?> | Semester: <?php echo $selected_course['semester']; ?></p>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Year</th>
                        <th>Current Marks</th>
                        <th>Current Grade</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students && $students->num_rows > 0): ?>
                        <?php while ($student = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo clean($student['student_id']); ?></td>
                            <td><?php echo clean($student['name']); ?></td>
                            <td><?php echo $student['year']; ?></td>
                            <td>
                                <?php if ($student['marks'] !== null): ?>
                                    <?php echo $student['marks']; ?>
                                <?php else: ?>
                                    <span class="text-muted">Not entered</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($student['grade']): ?>
                                    <span class="badge badge-<?php echo $student['grade'] == 'F' ? 'danger' : 'success'; ?>">
                                        <?php echo clean($student['grade']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button onclick="openMarksModal(<?php echo $student['id']; ?>, '<?php echo clean($student['student_id']); ?>', '<?php echo clean($student['name']); ?>', <?php echo $student['marks'] ?? 0; ?>, <?php echo $student['semester']; ?>, '<?php echo $student['academic_year'] ?? date('Y') . '-' . (date('Y')+1); ?>')" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> <?php echo $student['marks'] !== null ? 'Update' : 'Enter'; ?> Marks
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No students found for this course.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Marks Entry Modal -->
<div id="marksModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Enter/Update Marks</h3>
            <span class="close" onclick="document.getElementById('marksModal').style.display='none'">&times;</span>
        </div>
        <form method="POST" class="form" id="marksForm">
            <input type="hidden" name="student_id" id="modal_student_id">
            <input type="hidden" name="course_id" value="<?php echo $_GET['course_id'] ?? ''; ?>">
            <input type="hidden" name="semester" id="modal_semester">
            <input type="hidden" name="academic_year" id="modal_academic_year">
            
            <div class="form-group">
                <label>Student ID:</label>
                <p id="modal_student_display" style="font-weight: bold;"></p>
            </div>
            
            <div class="form-group">
                <label>Marks (0-100)</label>
                <input type="number" name="marks" id="modal_marks" min="0" max="100" step="0.01" required class="form-control" onchange="calculatePreviewGrade()">
            </div>
            
            <div class="form-group">
                <label>Calculated Grade:</label>
                <p id="preview_grade" style="font-size: 24px; font-weight: bold; color: #2196F3;">-</p>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Marks
                </button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('marksModal').style.display='none'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openMarksModal(studentId, studentIdText, studentName, marks, semester, academicYear) {
    document.getElementById('modal_student_id').value = studentId;
    document.getElementById('modal_student_display').textContent = studentIdText + ' - ' + studentName;
    document.getElementById('modal_marks').value = marks > 0 ? marks : '';
    document.getElementById('modal_semester').value = semester;
    document.getElementById('modal_academic_year').value = academicYear;
    calculatePreviewGrade();
    document.getElementById('marksModal').style.display = 'block';
}

function calculatePreviewGrade() {
    const marks = parseFloat(document.getElementById('modal_marks').value);
    let grade = '-';
    
    if (!isNaN(marks)) {
        if (marks >= 90) grade = 'A+ (4.00)';
        else if (marks >= 85) grade = 'A (3.75)';
        else if (marks >= 80) grade = 'A- (3.50)';
        else if (marks >= 75) grade = 'B+ (3.25)';
        else if (marks >= 70) grade = 'B (3.00)';
        else if (marks >= 65) grade = 'B- (2.75)';
        else if (marks >= 60) grade = 'C+ (2.50)';
        else if (marks >= 55) grade = 'C (2.25)';
        else if (marks >= 50) grade = 'D (2.00)';
        else grade = 'F (0.00)';
    }
    
    document.getElementById('preview_grade').textContent = grade;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
