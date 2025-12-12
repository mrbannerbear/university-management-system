<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');

$page_title = 'Assign Courses to Faculty';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'assign') {
            $faculty_id = $_POST['faculty_id'];
            $course_id = $_POST['course_id'];
            
            $stmt = $conn->prepare("INSERT INTO faculty_courses (faculty_id, course_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $faculty_id, $course_id);
            
            if ($stmt->execute()) {
                setSuccessMessage('Course assigned successfully!');
            } else {
                setErrorMessage('Error assigning course. It may already be assigned.');
            }
            $stmt->close();
            header('Location: /admin/assign-courses.php');
            exit();
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM faculty_courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        setSuccessMessage('Assignment removed successfully!');
    } else {
        setErrorMessage('Error removing assignment.');
    }
    $stmt->close();
    header('Location: /admin/assign-courses.php');
    exit();
}

$assignments = $conn->query("
    SELECT fc.*, f.name as faculty_name, c.course_code, c.course_name, d.name as dept_name
    FROM faculty_courses fc
    JOIN faculty f ON fc.faculty_id = f.id
    JOIN courses c ON fc.course_id = c.id
    LEFT JOIN departments d ON c.department_id = d.id
    ORDER BY f.name, c.course_code
");

$faculty_list = $conn->query("SELECT * FROM faculty ORDER BY name");
$courses = $conn->query("SELECT c.*, d.name as dept_name FROM courses c LEFT JOIN departments d ON c.department_id = d.id ORDER BY c.course_code");

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-user-tag"></i> Assign Courses to Faculty</h2>
    <button class="btn btn-primary" onclick="document.getElementById('assignModal').style.display='block'">
        <i class="fas fa-plus"></i> New Assignment
    </button>
</div>

<div class="content-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Faculty Name</th>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($assign = $assignments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo clean($assign['faculty_name']); ?></td>
                        <td><span class="badge badge-primary"><?php echo clean($assign['course_code']); ?></span></td>
                        <td><?php echo clean($assign['course_name']); ?></td>
                        <td><?php echo clean($assign['dept_name'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="?delete=<?php echo $assign['id']; ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Remove this assignment?')">
                                <i class="fas fa-trash"></i> Remove
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="assignModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Assign Course to Faculty</h3>
            <span class="close" onclick="document.getElementById('assignModal').style.display='none'">&times;</span>
        </div>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="assign">
            <div class="form-group">
                <label>Select Faculty</label>
                <select name="faculty_id" required class="form-control">
                    <option value="">Choose Faculty</option>
                    <?php while ($fac = $faculty_list->fetch_assoc()): ?>
                    <option value="<?php echo $fac['id']; ?>"><?php echo clean($fac['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Select Course</label>
                <select name="course_id" required class="form-control">
                    <option value="">Choose Course</option>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                    <option value="<?php echo $course['id']; ?>">
                        <?php echo clean($course['course_code'] . ' - ' . $course['course_name'] . ' (' . ($course['dept_name'] ?? 'N/A') . ')'); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Assign Course
                </button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('assignModal').style.display='none'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
