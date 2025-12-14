<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';

requireRole('admin');

$method = $_SERVER['REQUEST_METHOD'];

// GET - Fetch all faculty or a specific faculty member
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("
            SELECT f.*, d.name as department_name, u.email
            FROM faculty f
            LEFT JOIN departments d ON f.department_id = d.id
            LEFT JOIN users u ON f.user_id = u.id
            WHERE f.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $faculty = $result->fetch_assoc();
        $stmt->close();
        
        if ($faculty) {
            echo json_encode(['success' => true, 'data' => $faculty]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Faculty not found']);
        }
    } else {
        $query = "
            SELECT f.*, d.name as department_name, u.email
            FROM faculty f
            LEFT JOIN departments d ON f.department_id = d.id
            LEFT JOIN users u ON f.user_id = u.id
            ORDER BY f.created_at DESC
        ";
        $result = $conn->query($query);
        $faculty_list = [];
        while ($row = $result->fetch_assoc()) {
            $faculty_list[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $faculty_list]);
    }
}

// POST - Add new faculty
elseif ($method === 'POST') {
    $input = getJsonInput();
    
    $name = $input['name'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $department_id = $input['department_id'] ?? null;
    
    if (empty($name) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit();
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, 'faculty', ?)");
        $stmt->bind_param("sssi", $name, $email, $hashed_password, $department_id);
        $stmt->execute();
        $user_id = $conn->insert_id;
        $stmt->close();
        
        $stmt = $conn->prepare("INSERT INTO faculty (user_id, name, department_id) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $user_id, $name, $department_id);
        $stmt->execute();
        $new_faculty_id = $conn->insert_id;
        $stmt->close();
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Faculty added successfully', 'data' => ['id' => $new_faculty_id]]);
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error adding faculty: ' . $e->getMessage()]);
    }
}

// PUT - Update faculty
elseif ($method === 'PUT') {
    $input = getJsonInput();
    
    $id = $input['id'] ?? 0;
    $name = $input['name'] ?? '';
    $email = $input['email'] ?? '';
    $department_id = $input['department_id'] ?? null;
    
    if (empty($id) || empty($name) || empty($email)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT user_id FROM faculty WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $faculty = $result->fetch_assoc();
    $stmt->close();
    
    if (!$faculty) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Faculty not found']);
        exit();
    }
    
    $user_id = $faculty['user_id'];
    
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
        echo json_encode(['success' => true, 'message' => 'Faculty updated successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error updating faculty: ' . $e->getMessage()]);
    }
}

// DELETE - Delete faculty
elseif ($method === 'DELETE') {
    $input = getJsonInput();
    $id = $input['id'] ?? 0;
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Faculty ID is required']);
        exit();
    }
    
    $stmt = $conn->prepare("SELECT user_id FROM faculty WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $faculty = $result->fetch_assoc();
    $stmt->close();
    
    if (!$faculty) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Faculty not found']);
        exit();
    }
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $faculty['user_id']);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Faculty deleted successfully']);
    } else {
        $stmt->close();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error deleting faculty']);
    }
}

else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>
