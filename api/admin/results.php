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

// GET - Fetch all results
if ($method === 'GET') {
    $query = "
        SELECT r.*, s.name as student_name, s.student_id, c.course_code, c.course_name, 
               f.name as faculty_name, d.name as department_name
        FROM results r
        JOIN students s ON r.student_id = s.id
        JOIN courses c ON r.course_id = c.id
        LEFT JOIN faculty f ON r.faculty_id = f.id
        LEFT JOIN departments d ON s.department_id = d.id
        ORDER BY r.created_at DESC
    ";
    
    if (isset($_GET['semester'])) {
        $semester = intval($_GET['semester']);
        $query = "
            SELECT r.*, s.name as student_name, s.student_id, c.course_code, c.course_name, 
                   f.name as faculty_name, d.name as department_name
            FROM results r
            JOIN students s ON r.student_id = s.id
            JOIN courses c ON r.course_id = c.id
            LEFT JOIN faculty f ON r.faculty_id = f.id
            LEFT JOIN departments d ON s.department_id = d.id
            WHERE r.semester = $semester
            ORDER BY r.created_at DESC
        ";
    }
    
    $result = $conn->query($query);
    $results = [];
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $results]);
}

// PUT - Publish/unpublish result
elseif ($method === 'PUT') {
    $input = getJsonInput();
    
    $id = $input['id'] ?? 0;
    $published = $input['published'] ?? 0;
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Result ID is required']);
        exit();
    }
    
    try {
        $stmt = $conn->prepare("UPDATE results SET published = ? WHERE id = ?");
        $stmt->bind_param("ii", $published, $id);
        $stmt->execute();
        $stmt->close();
        
        $message = $published ? 'Result published successfully' : 'Result unpublished successfully';
        echo json_encode(['success' => true, 'message' => $message]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error updating result: ' . $e->getMessage()]);
    }
}

// DELETE - Delete result
elseif ($method === 'DELETE') {
    $input = getJsonInput();
    $id = $input['id'] ?? 0;
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Result ID is required']);
        exit();
    }
    
    $stmt = $conn->prepare("DELETE FROM results WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Result deleted successfully']);
    } else {
        $stmt->close();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error deleting result']);
    }
}

else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>
