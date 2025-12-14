<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
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

// GET - Fetch all departments
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $department = $result->fetch_assoc();
        $stmt->close();
        
        if ($department) {
            echo json_encode(['success' => true, 'data' => $department]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Department not found']);
        }
    } else {
        $result = $conn->query("SELECT * FROM departments ORDER BY name");
        $departments = [];
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $departments]);
    }
}

// POST - Add new department
elseif ($method === 'POST') {
    $input = getJsonInput();
    
    $name = $input['name'] ?? '';
    $code = $input['code'] ?? '';
    
    if (empty($name) || empty($code)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Name and code are required']);
        exit();
    }
    
    try {
        $stmt = $conn->prepare("INSERT INTO departments (name, code) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $code);
        $stmt->execute();
        $new_dept_id = $conn->insert_id;
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'Department added successfully', 'data' => ['id' => $new_dept_id]]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error adding department: ' . $e->getMessage()]);
    }
}

// PUT - Update department
elseif ($method === 'PUT') {
    $input = getJsonInput();
    
    $id = $input['id'] ?? 0;
    $name = $input['name'] ?? '';
    $code = $input['code'] ?? '';
    
    if (empty($id) || empty($name) || empty($code)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit();
    }
    
    try {
        $stmt = $conn->prepare("UPDATE departments SET name = ?, code = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $code, $id);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'Department updated successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error updating department: ' . $e->getMessage()]);
    }
}

// DELETE - Delete department
elseif ($method === 'DELETE') {
    $input = getJsonInput();
    $id = $input['id'] ?? 0;
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Department ID is required']);
        exit();
    }
    
    $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Department deleted successfully']);
    } else {
        $stmt->close();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error deleting department']);
    }
}

else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>
