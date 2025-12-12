<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('admin');

$page_title = 'View All Results';

// Handle publish/unpublish
if (isset($_GET['toggle_publish'])) {
    $id = $_GET['toggle_publish'];
    $stmt = $conn->prepare("UPDATE results SET published = NOT published WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        setSuccessMessage('Result status updated!');
    } else {
        setErrorMessage('Error updating result.');
    }
    $stmt->close();
    header('Location: /admin/results.php');
    exit();
}

// Get all results with filters
$where = [];
$params = [];
$types = "";

if (isset($_GET['student_id']) && !empty($_GET['student_id'])) {
    $where[] = "s.student_id LIKE ?";
    $params[] = "%" . $_GET['student_id'] . "%";
    $types .= "s";
}

if (isset($_GET['semester']) && !empty($_GET['semester'])) {
    $where[] = "r.semester = ?";
    $params[] = $_GET['semester'];
    $types .= "i";
}

if (isset($_GET['published']) && $_GET['published'] !== '') {
    $where[] = "r.published = ?";
    $params[] = $_GET['published'];
    $types .= "i";
}

$where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$query = "
    SELECT r.*, s.name as student_name, s.student_id, 
           c.course_code, c.course_name, c.credit,
           f.name as faculty_name
    FROM results r
    JOIN students s ON r.student_id = s.id
    JOIN courses c ON r.course_id = c.id
    LEFT JOIN faculty f ON r.faculty_id = f.id
    $where_clause
    ORDER BY r.created_at DESC
";

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $results = $stmt->get_result();
} else {
    $results = $conn->query($query);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h2><i class="fas fa-chart-bar"></i> All Results</h2>
</div>

<div class="content-card">
    <div class="card-header">
        <h3>Filter Results</h3>
    </div>
    <div class="card-body">
        <form method="GET" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Student ID</label>
                    <input type="text" name="student_id" value="<?php echo $_GET['student_id'] ?? ''; ?>" class="form-control" placeholder="Search by Student ID">
                </div>
                <div class="form-group">
                    <label>Semester</label>
                    <input type="number" name="semester" value="<?php echo $_GET['semester'] ?? ''; ?>" class="form-control" placeholder="Filter by Semester">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="published" class="form-control">
                        <option value="">All</option>
                        <option value="1" <?php echo (isset($_GET['published']) && $_GET['published'] == '1') ? 'selected' : ''; ?>>Published</option>
                        <option value="0" <?php echo (isset($_GET['published']) && $_GET['published'] == '0') ? 'selected' : ''; ?>>Draft</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="/admin/results.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
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
                        <th>GPA</th>
                        <th>Semester</th>
                        <th>Year</th>
                        <th>Faculty</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $results->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo clean($row['student_id']); ?></td>
                        <td><?php echo clean($row['student_name']); ?></td>
                        <td><?php echo clean($row['course_code'] . ' - ' . $row['course_name']); ?></td>
                        <td><?php echo clean($row['marks']); ?></td>
                        <td><span class="badge badge-<?php echo $row['grade'] == 'F' ? 'danger' : 'success'; ?>"><?php echo clean($row['grade']); ?></span></td>
                        <td><?php echo $row['grade_point']; ?></td>
                        <td><?php echo $row['semester']; ?></td>
                        <td><?php echo clean($row['academic_year']); ?></td>
                        <td><?php echo clean($row['faculty_name'] ?? 'N/A'); ?></td>
                        <td>
                            <?php if ($row['published']): ?>
                                <span class="badge badge-success">Published</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Draft</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?toggle_publish=<?php echo $row['id']; ?>" class="btn btn-sm btn-<?php echo $row['published'] ? 'warning' : 'success'; ?>">
                                <i class="fas fa-<?php echo $row['published'] ? 'eye-slash' : 'eye'; ?>"></i>
                                <?php echo $row['published'] ? 'Unpublish' : 'Publish'; ?>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
