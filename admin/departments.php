<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');

$page_title = 'Manage Departments';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $name = $_POST['name'];
            $code = $_POST['code'];
            
            $stmt = $conn->prepare("INSERT INTO departments (name, code) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $code);
            
            if ($stmt->execute()) {
                setSuccessMessage('Department added successfully!');
            } else {
                setErrorMessage('Error adding department: ' . $stmt->error);
            }
            $stmt->close();
            header('Location: /admin/departments.php');
            exit();
        } elseif ($_POST['action'] === 'edit') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $code = $_POST['code'];
            
            $stmt = $conn->prepare("UPDATE departments SET name = ?, code = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $code, $id);
            
            if ($stmt->execute()) {
                setSuccessMessage('Department updated successfully!');
            } else {
                setErrorMessage('Error updating department: ' . $stmt->error);
            }
            $stmt->close();
            header('Location: /admin/departments.php');
            exit();
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        setSuccessMessage('Department deleted successfully!');
    } else {
        setErrorMessage('Error deleting department: ' . $stmt->error);
    }
    $stmt->close();
    header('Location: /admin/departments.php');
    exit();
}

// Get all departments
$departments = $conn->query("SELECT * FROM departments ORDER BY name");

// Get department for editing
$edit_dept = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM departments WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_dept = $result->fetch_assoc();
    $stmt->close();
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-building"></i> Manage Departments</h2>
    <button class="btn btn-primary" onclick="document.getElementById('addModal').style.display='block'">
        <i class="fas fa-plus"></i> Add Department
    </button>
</div>

<div class="content-card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Department Name</th>
                        <th>Code</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($dept = $departments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $dept['id']; ?></td>
                        <td><?php echo clean($dept['name']); ?></td>
                        <td><span class="badge badge-info"><?php echo clean($dept['code']); ?></span></td>
                        <td><?php echo formatDate($dept['created_at']); ?></td>
                        <td>
                            <a href="?edit=<?php echo $dept['id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="?delete=<?php echo $dept['id']; ?>" class="btn btn-sm btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this department?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Department Modal -->
<div id="addModal" class="modal" style="display: <?php echo $edit_dept ? 'none' : 'none'; ?>;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-plus"></i> Add Department</h3>
            <span class="close" onclick="document.getElementById('addModal').style.display='none'">&times;</span>
        </div>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label>Department Name</label>
                <input type="text" name="name" required class="form-control">
            </div>
            <div class="form-group">
                <label>Department Code</label>
                <input type="text" name="code" required class="form-control">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Add Department
                </button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addModal').style.display='none'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Department Modal -->
<?php if ($edit_dept): ?>
<div id="editModal" class="modal" style="display: block;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Department</h3>
            <span class="close" onclick="window.location.href='/admin/departments.php'">&times;</span>
        </div>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?php echo $edit_dept['id']; ?>">
            <div class="form-group">
                <label>Department Name</label>
                <input type="text" name="name" value="<?php echo clean($edit_dept['name']); ?>" required class="form-control">
            </div>
            <div class="form-group">
                <label>Department Code</label>
                <input type="text" name="code" value="<?php echo clean($edit_dept['code']); ?>" required class="form-control">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Department
                </button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='/admin/departments.php'">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
