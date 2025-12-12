<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';

requireRole('admin');

$method = $_SERVER['REQUEST_METHOD'];

// GET - Fetch all courses or a specific course
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("
            SELECT c.*, d.name as department_name
            FROM courses c
            LEFT JOIN departments d ON c.department_id = d.id
            WHERE c.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $course = $result->fetch_assoc();
        $stmt->close();
        
        if ($course) {
            echo json_encode(['success' => true, 'data' => $course]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Course not found']);
        }
    } else {
        $query = "
            SELECT c.*, d.name as department_name
            FROM courses c
            LEFT JOIN departments d ON c.department_id = d.id
            ORDER BY c.semester, c.course_code
        ";
        $result = $conn->query($query);
        $courses = [];
        while ($row = $result->fetch_assoc()) {
            $courses[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $courses]);
    }
}

// POST - Add new course
elseif ($method === 'POST') {
    $input = getJsonInput();
    
    $course_code = $input['course_code'] ?? '';
    $course_name = $input['course_name'] ?? '';
    $credit = $input['credit'] ?? 0;
    $department_id = $input['department_id'] ?? null;
    $semester = $input['semester'] ?? 1;
    
    if (empty($course_code) || empty($course_name) || empty($credit)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit();
    }
    
    try {
        $stmt = $conn->prepare("INSERT INTO courses (course_code, course_name, credit, department_id, semester) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdii", $course_code, $course_name, $credit, $department_id, $semester);
        $stmt->execute();
        $new_course_id = $conn->insert_id;
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'Course added successfully', 'data' => ['id' => $new_course_id]]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error adding course: ' . $e->getMessage()]);
    }
}

// PUT - Update course
elseif ($method === 'PUT') {
    $input = getJsonInput();
    
    $id = $input['id'] ?? 0;
    $course_code = $input['course_code'] ?? '';
    $course_name = $input['course_name'] ?? '';
    $credit = $input['credit'] ?? 0;
    $department_id = $input['department_id'] ?? null;
    $semester = $input['semester'] ?? 1;
    
    if (empty($id) || empty($course_code) || empty($course_name) || empty($credit)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit();
    }
    
    try {
        $stmt = $conn->prepare("UPDATE courses SET course_code = ?, course_name = ?, credit = ?, department_id = ?, semester = ? WHERE id = ?");
        $stmt->bind_param("ssdiii", $course_code, $course_name, $credit, $department_id, $semester, $id);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'Course updated successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error updating course: ' . $e->getMessage()]);
    }
}

// DELETE - Delete course
elseif ($method === 'DELETE') {
    $input = getJsonInput();
    $id = $input['id'] ?? 0;
    
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Course ID is required']);
        exit();
    }
    
    $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $stmt->close();
        echo json_encode(['success' => true, 'message' => 'Course deleted successfully']);
    } else {
        $stmt->close();
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error deleting course']);
    }
}

else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>
