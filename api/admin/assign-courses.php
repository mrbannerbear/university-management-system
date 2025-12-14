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

// GET - Fetch course assignments
if ($method === 'GET') {
    $query = "
        SELECT fc.*, f.name as faculty_name, c.course_code, c.course_name, c.credit, d.name as department_name
        FROM faculty_courses fc
        JOIN faculty f ON fc.faculty_id = f.id
        JOIN courses c ON fc.course_id = c.id
        LEFT JOIN departments d ON f.department_id = d.id
        ORDER BY f.name, c.course_code
    ";
    $result = $conn->query($query);
    $assignments = [];
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $assignments]);
}

// POST - Assign course to faculty
elseif ($method === 'POST') {
    $input = getJsonInput();
    
    $faculty_id = $input['faculty_id'] ?? 0;
    $course_id = $input['course_id'] ?? 0;
    
    if (empty($faculty_id) || empty($course_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Faculty and course are required']);
        exit();
    }
    
    try {
        $stmt = $conn->prepare("INSERT INTO faculty_courses (faculty_id, course_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $faculty_id, $course_id);
        $stmt->execute();
        $new_assignment_id = $conn->insert_id;
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'Course assigned successfully', 'data' => ['id' => $new_assignment_id]]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error assigning course: ' . $e->getMessage()]);
    }
}

// DELETE - Remove course assignment
elseif ($method === 'DELETE') {
    $input = getJsonInput();
    $id = $input['id'] ?? 0;
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Assignment ID is required']);
        exit();
    }
    
    $stmt = $conn->prepare("DELETE FROM faculty_courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Assignment removed successfully']);
    } else {
        $stmt->close();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error removing assignment']);
    }
}

else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>
