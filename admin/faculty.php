<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');

$page_title = 'Manage Faculty';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $department_id = $_POST['department_id'];
            
            $conn->begin_transaction();
            
            try {
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, 'faculty', ?)");
                $stmt->bind_param("sssi", $name, $email, $password, $department_id);
                $stmt->execute();
                $user_id = $conn->insert_id;
                $stmt->close();
                
                $stmt = $conn->prepare("INSERT INTO faculty (user_id, name, department_id) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $user_id, $name, $department_id);
                $stmt->execute();
                $stmt->close();
                
                $conn->commit();
                setSuccessMessage('Faculty added successfully!');
            } catch (Exception $e) {
                $conn->rollback();
                setErrorMessage('Error adding faculty: ' . $e->getMessage());
            }
            
            header('Location: /admin/faculty.php');
            exit();
        } elseif ($_POST['action'] === 'edit') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $email = $_POST['email'];
            $department_id = $_POST['department_id'];
            
            $stmt = $conn->prepare("SELECT user_id FROM faculty WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $faculty = $result->fetch_assoc();
            $user_id = $faculty['user_id'];
            $stmt->close();
            
            $conn->begin_transaction();
            
            try {
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, department_id = ? WHERE id = ?");
                $stmt->bind_param("ssii", $name, $email, $department_id, $user_id);
                $stmt->execute();
                $stmt->close();
                
                $stmt = $conn->prepare("UPDATE faculty SET name = ?, department_id = ? WHERE id = ?");
                $stmt->bind_param("sii", $name, $department_id, $id);
                $stmt->execute();
                $stmt->close();
                
                $conn->commit();
                setSuccessMessage('Faculty updated successfully!');
            } catch (Exception $e) {
                $conn->rollback();
                setErrorMessage('Error updating faculty: ' . $e->getMessage());
            }
            
            header('Location: /admin/faculty.php');
            exit();
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $stmt = $conn->prepare("SELECT user_id FROM faculty WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $faculty = $result->fetch_assoc();
    $stmt->close();
    
    if ($faculty) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $faculty['user_id']);
        
        if ($stmt->execute()) {
            setSuccessMessage('Faculty deleted successfully!');
        } else {
            setErrorMessage('Error deleting faculty.');
        }
        $stmt->close();
    }
    
    header('Location: /admin/faculty.php');
    exit();
}

$faculty_list = $conn->query("
    SELECT f.*, d.name as dept_name, u.email
    FROM faculty f
    LEFT JOIN departments d ON f.department_id = d.id
    LEFT JOIN users u ON f.user_id = u.id
    ORDER BY f.name
");

$departments = $conn->query("SELECT * FROM departments ORDER BY name");

$edit_faculty = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("
        SELECT f.*, u.email
        FROM faculty f
        LEFT JOIN users u ON f.user_id = u.id
        WHERE f.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_faculty = $result->fetch_assoc();
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-chalkboard-teacher"></i> Manage Faculty</h2>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='block'">
        <i class="fas fa-plus"></i> Add Faculty
    </button>
</div>

<div class="content-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fac = $faculty_list->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $fac['id']; ?></td>
                        <td><?php echo clean($fac['name']); ?></td>
                        <td><?php echo clean($fac['email']); ?></td>
                        <td><?php echo clean($fac['dept_name'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="?edit=<?php echo $fac['id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $fac['id']; ?>" class="btn btn-sm btn-danger" 
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
            <h3><i class="fas fa-plus"></i> Add Faculty</h3>
            <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
        </div>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required class="form-control">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required class="form-control">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required class="form-control">
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
                    <i class="fas fa-save"></i> Add Faculty
                </button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addModal').style.display='none'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($edit_faculty): ?>
<div id="editModal" class="modal" style="display: block;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Faculty</h3>
            <span class="close" onclick="window.location.href='/admin/faculty.php'">&times;</span>
        </div>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $edit_faculty['id']; ?>">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?php echo clean($edit_faculty['name']); ?>" required class="form-control">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo clean($edit_faculty['email']); ?>" required class="form-control">
            </div>
            <div class="form-group">
                <label>Department</label>
                <select name="department_id" required class="form-control">
                    <option value="">Select Department</option>
                    <?php 
                    $departments->data_seek(0);
                    while ($dept = $departments->fetch_assoc()): 
                    ?>
                    <option value="<?php echo $dept['id']; ?>" <?php echo $dept['id'] == $edit_faculty['department_id'] ? 'selected' : ''; ?>>
                        <?php echo clean($dept['name']); ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Faculty
                </button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='/admin/faculty.php'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
