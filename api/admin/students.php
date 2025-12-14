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

// GET - Fetch all students or a specific student
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        // Fetch specific student
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("
            SELECT s.*, d.name as department_name, u.email
            FROM students s
            LEFT JOIN departments d ON s.department_id = d.id
            LEFT JOIN users u ON s.user_id = u.id
            WHERE s.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $student = $result->fetch_assoc();
        $stmt->close();
        
        if ($student) {
            echo json_encode(['success' => true, 'data' => $student]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Student not found']);
        }
    } else {
        // Fetch all students
        $query = "
            SELECT s.*, d.name as department_name, u.email
            FROM students s
            LEFT JOIN departments d ON s.department_id = d.id
            LEFT JOIN users u ON s.user_id = u.id
            ORDER BY s.created_at DESC
        ";
        $result = $conn->query($query);
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $students]);
    }
}

// POST - Add new student
elseif ($method === 'POST') {
    $input = getJsonInput();
    
    $name = $input['name'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $student_id = $input['student_id'] ?? '';
    $department_id = $input['department_id'] ?? null;
    $year = $input['year'] ?? 1;
    $semester = $input['semester'] ?? 1;
    
    if (empty($name) || empty($email) || empty($password) || empty($student_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit();
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department_id) VALUES (?, ?, ?, 'student', ?)");
        $stmt->bind_param("sssi", $name, $email, $hashed_password, $department_id);
        $stmt->execute();
        $user_id = $conn->insert_id;
        $stmt->close();
        
        // Insert student
        $stmt = $conn->prepare("INSERT INTO students (user_id, student_id, name, department_id, year, semester) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issiii", $user_id, $student_id, $name, $department_id, $year, $semester);
        $stmt->execute();
        $new_student_id = $conn->insert_id;
        $stmt->close();
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Student added successfully', 'data' => ['id' => $new_student_id]]);
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error adding student: ' . $e->getMessage()]);
    }
}

// PUT - Update student
elseif ($method === 'PUT') {
    $input = getJsonInput();
    
    $id = $input['id'] ?? 0;
    $name = $input['name'] ?? '';
    $email = $input['email'] ?? '';
    $student_id = $input['student_id'] ?? '';
    $department_id = $input['department_id'] ?? null;
    $year = $input['year'] ?? 1;
    $semester = $input['semester'] ?? 1;
    
    if (empty($id) || empty($name) || empty($email) || empty($student_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit();
    }
    
    // Get user_id
    $stmt = $conn->prepare("SELECT user_id FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
    
    if (!$student) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Student not found']);
        exit();
    }
    
    $user_id = $student['user_id'];
    
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
        echo json_encode(['success' => true, 'message' => 'Student updated successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error updating student: ' . $e->getMessage()]);
    }
}

// DELETE - Delete student
elseif ($method === 'DELETE') {
    $input = getJsonInput();
    $id = $input['id'] ?? 0;
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Student ID is required']);
        exit();
    }
    
    // Get user_id
    $stmt = $conn->prepare("SELECT user_id FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
    
    if (!$student) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Student not found']);
        exit();
    }
    
    // Delete user (will cascade delete student)
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $student['user_id']);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
    } else {
        $stmt->close();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error deleting student']);
    }
}

else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>
