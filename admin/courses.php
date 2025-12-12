<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');

$page_title = 'Manage Courses';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $course_code = $_POST['course_code'];
            $course_name = $_POST['course_name'];
            $credit = $_POST['credit'];
            $department_id = $_POST['department_id'];
            $semester = $_POST['semester'];
            
            $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, credit, department_id, semester) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdii", $course_code, $course_name, $credit, $department_id, $semester);
            
            if ($stmt->execute()) {
                setSuccessMessage('Course added successfully!');
            } else {
                setErrorMessage('Error adding course.');
            }
            $stmt->close();
            header('Location: /admin/courses.php');
            exit();
        } elseif ($_POST['action'] === 'edit') {
            $id = $_POST['id'];
            $course_code = $_POST['course_code'];
            $course_name = $_POST['course_name'];
            $credit = $_POST['credit'];
            $department_id = $_POST['department_id'];
            $semester = $_POST['semester'];
            
            $stmt = $conn->prepare("UPDATE courses SET course_code = ?, course_name = ?, credit = ?, department_id = ?, semester = ? WHERE id = ?");
            $stmt->bind_param("ssdiii", $course_code, $course_name, $credit, $department_id, $semester, $id);
            
            if ($stmt->execute()) {
                setSuccessMessage('Course updated successfully!');
            } else {
                setErrorMessage('Error updating course.');
            }
            $stmt->close();
            header('Location: /admin/courses.php');
            exit();
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        setSuccessMessage('Course deleted successfully!');
    } else {
        setErrorMessage('Error deleting course.');
    }
    $stmt->close();
    header('Location: /admin/courses.php');
    exit();
}

$courses = $conn->query("
    SELECT c.*, d.name as dept_name
    FROM courses c
    LEFT JOIN departments d ON c.department_id = d.id
    ORDER BY c.course_code
");

$departments = $conn->query("SELECT * FROM departments ORDER BY name");

$edit_course = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_course = $result->fetch_assoc();
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-book"></i> Manage Courses</h2>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='block'">
        <i class="fas fa-plus"></i> Add Course
    </button>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                    <tr>
                        <td><span class="badge badge-primary"><?php echo clean($course['course_code']); ?></span></td>
                        <td><?php echo clean($course['course_name']); ?></td>
                        <td><?php echo $course['credit']; ?></td>
                        <td><?php echo clean($course['dept_name'] ?? 'N/A'); ?></td>
                        <td><?php echo $course['semester']; ?></td>
                        <td>
                            <a href="?edit=<?php echo $course['id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $course['id']; ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Add Course</h3>
            <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
        </div>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Course Code</label>
                <input type="text" name="course_code" required class="form-control">
            </div>
            <div class="form-group">
                <label>Course Name</label>
                <input type="text" name="course_name" required class="form-control">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Credit Hours</label>
                    <input type="number" step="0.5" name="credit" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Semester</label>
                    <input type="number" min="1" max="10" name="semester" required class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Department</label>
                <select name="department_id" required class="form-control">
                    <option value="">Select Department</option>
                    <?php 
                    $departments->data_seek(0);
                    while ($dept = $departments->fetch_assoc()): 
                    ?>
                    <option value="<?php echo $dept['id']; ?>"><?php echo clean($dept['name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Course
                </button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addModal').style.display='none'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($edit_course): ?>
<div id="editModal" class="modal" style="display: block;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Course</h3>
            <span class="close" onclick="window.location.href='/admin/courses.php'">&times;</span>
        </div>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $edit_course['id']; ?>">
            <div class="form-group">
                <label>Course Code</label>
                <input type="text" name="course_code" value="<?php echo clean($edit_course['course_code']); ?>" required class="form-control">
            </div>
            <div class="form-group">
                <label>Course Name</label>
                <input type="text" name="course_name" value="<?php echo clean($edit_course['course_name']); ?>" required class="form-control">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Credit Hours</label>
                    <input type="number" step="0.5" name="credit" value="<?php echo $edit_course['credit']; ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Semester</label>
                    <input type="number" min="1" max="10" name="semester" value="<?php echo $edit_course['semester']; ?>" required class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Department</label>
                <select name="department_id" required class="form-control">
                    <option value="">Select Department</option>
                    <?php 
                    $departments->data_seek(0);
                    while ($dept = $departments->fetch_assoc()): 
                    ?>
                    <option value="<?php echo $dept['id']; ?>" <?php echo $dept['id'] == $edit_course['department_id'] ? 'selected' : ''; ?>>
                        <?php echo clean($dept['name']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Course
                </button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='/admin/courses.php'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
