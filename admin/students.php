<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');

$page_title = 'Manage Students';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $student_id = $_POST['student_id'];
            $department_id = $_POST['department_id'];
            $year = $_POST['year'];
            $semester = $_POST['semester'];
            
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Insert user
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, 'student', ?)");
                $stmt->bind_param("sssi", $name, $email, $password, $department_id);
                $stmt->execute();
                $user_id = $conn->insert_id;
                $stmt->close();
                
                // Insert student
                $stmt = $conn->prepare("INSERT INTO students (user_id, student_id, name, department_id, year, semester) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issiii", $user_id, $student_id, $name, $department_id, $year, $semester);
                $stmt->execute();
                $stmt->close();
                
                $conn->commit();
                setSuccessMessage('Student added successfully!');
            } catch (Exception $e) {
                $conn->rollback();
                setErrorMessage('Error adding student: ' . $e->getMessage());
            }
            
            header('Location: /admin/students.php');
            exit();
        } elseif ($_POST['action'] === 'edit') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $student_id = $_POST['student_id'];
            $department_id = $_POST['department_id'];
            $year = $_POST['year'];
            $semester = $_POST['semester'];
            
            // Get user_id
            $stmt = $conn->prepare("SELECT user_id FROM students WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $student = $result->fetch_assoc();
            $user_id = $student['user_id'];
            $stmt->close();
            
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Update user
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, department_id = ? WHERE id = ?");
                $stmt->bind_param("ssii", $name, $email, $department_id, $user_id);
                $stmt->execute();
                $stmt->close();
                
                // Update student
                $stmt = $conn->prepare("UPDATE students SET student_id = ?, name = ?, department_id = ?, year = ?, semester = ? WHERE id = ?");
                $stmt->bind_param("ssiiii", $student_id, $name, $department_id, $year, $semester, $id);
                $stmt->execute();
                $stmt->close();
                
                $conn->commit();
                setSuccessMessage('Student updated successfully!');
            } catch (Exception $e) {
                $conn->rollback();
                setErrorMessage('Error updating student: ' . $e->getMessage());
            }
            
            header('Location: /admin/students.php');
            exit();
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Get user_id first
    $stmt = $conn->prepare("SELECT user_id FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
    
    if ($student) {
        // Delete user (cascade will delete student)
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $student['user_id']);
        
        if ($stmt->execute()) {
            setSuccessMessage('Student deleted successfully!');
        } else {
            setErrorMessage('Error deleting student.');
        }
        $stmt->close();
    }
    
    header('Location: /admin/students.php');
    exit();
}

// Get all students with department info
$students = $conn->query("
    SELECT s.*, d.name as dept_name, d.code as dept_code, u.email
    FROM students s
    LEFT JOIN departments d ON s.department_id = d.id
    LEFT JOIN users u ON s.user_id = u.id
    ORDER BY s.name
");

// Get departments for dropdown
$departments = $conn->query("SELECT * FROM departments ORDER BY name");

// Get student for editing
$edit_student = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("
        SELECT s.*, u.email
        FROM students s
        LEFT JOIN users u ON s.user_id = u.id
        WHERE s.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_student = $result->fetch_assoc();
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-user-graduate"></i> Manage Students</h2>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='block'">
        <i class="fas fa-plus"></i> Add Student
    </button>
</div>

<div class="content-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Year</th>
                        <th>Semester</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($student = $students->fetch_assoc()): ?>
                    <tr>
                        <td><span class="badge badge-primary"><?php echo clean($student['student_id']); ?></span></td>
                        <td><?php echo clean($student['name']); ?></td>
                        <td><?php echo clean($student['email']); ?></td>
                        <td><?php echo clean($student['dept_name'] ?? 'N/A'); ?></td>
                        <td><?php echo $student['year']; ?></td>
                        <td><?php echo $student['semester']; ?></td>
                        <td>
                            <a href="?edit=<?php echo $student['id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure? This will delete all associated data.')">
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

<!-- Add Student Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Add Student</h3>
            <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
        </div>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="add">
            <div class="form-row">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" required class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required class="form-control">
                </div>
            </div>
            <div class="form-row">
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
                <div class="form-group">
                    <label>Year</label>
                    <input type="number" name="year" min="1" max="5" required class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Semester</label>
                <input type="number" name="semester" min="1" max="10" required class="form-control">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Student
                </button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addModal').style.display='none'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Student Modal -->
<?php if ($edit_student): ?>
<div id="editModal" class="modal" style="display: block;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Student</h3>
            <span class="close" onclick="window.location.href='/admin/students.php'">&times;</span>
        </div>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $edit_student['id']; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" value="<?php echo clean($edit_student['student_id']); ?>" required class="form-control">
                </div>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo clean($edit_student['name']); ?>" required class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo clean($edit_student['email']); ?>" required class="form-control">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Department</label>
                    <select name="department_id" required class="form-control">
                        <option value="">Select Department</option>
                        <?php 
                        $departments->data_seek(0);
                        while ($dept = $departments->fetch_assoc()): 
                        ?>
                        <option value="<?php echo $dept['id']; ?>" <?php echo $dept['id'] == $edit_student['department_id'] ? 'selected' : ''; ?>>
                            <?php echo clean($dept['name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Year</label>
                    <input type="number" name="year" value="<?php echo $edit_student['year']; ?>" min="1" max="5" required class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label>Semester</label>
                <input type="number" name="semester" value="<?php echo $edit_student['semester']; ?>" min="1" max="10" required class="form-control">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Student
                </button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='/admin/students.php'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
