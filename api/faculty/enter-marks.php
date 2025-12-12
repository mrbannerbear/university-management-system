<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';

requireRole('faculty');

// Get faculty ID from session
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM faculty WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$faculty_data = $result->fetch_assoc();
$stmt->close();

if (!$faculty_data) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Faculty profile not found']);
    exit();
}

$faculty_id = $faculty_data['id'];

$method = $_SERVER['REQUEST_METHOD'];

// GET - Get students for a course to enter marks
if ($method === 'GET') {
    if (!isset($_GET['course_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Course ID is required']);
        exit();
    }
    
    $course_id = intval($_GET['course_id']);
    
    // Get all students in the same semester as the course
    $query = "
        SELECT s.*, r.id as result_id, r.marks, r.grade, r.grade_point, r.published
        FROM students s
        LEFT JOIN results r ON s.id = r.student_id AND r.course_id = ? AND r.faculty_id = ?
        JOIN courses c ON c.id = ?
        WHERE s.semester = c.semester
        ORDER BY s.student_id
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $course_id, $faculty_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();
    
    echo json_encode(['success' => true, 'data' => $students]);
}

// POST - Submit marks for students
elseif ($method === 'POST') {
    $input = getJsonInput();
    
    $course_id = $input['course_id'] ?? 0;
    $student_id = $input['student_id'] ?? 0;
    $marks = $input['marks'] ?? 0;
    $semester = $input['semester'] ?? 1;
    $academic_year = $input['academic_year'] ?? date('Y');
    
    if (empty($course_id) || empty($student_id) || $marks === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        exit();
    }
    
    // Calculate grade
    $gradeInfo = calculateGrade($marks);
    $grade = $gradeInfo['grade'];
    $grade_point = $gradeInfo['grade_point'];
    
    // Check if result already exists
    $stmt = $conn->prepare("SELECT id FROM results WHERE student_id = ? AND course_id = ? AND faculty_id = ?");
    $stmt->bind_param("iii", $student_id, $course_id, $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing = $result->fetch_assoc();
    $stmt->close();
    
    try {
        if ($existing) {
            // Update existing result
            $stmt = $conn->prepare("UPDATE results SET marks = ?, grade = ?, grade_point = ? WHERE id = ?");
            $stmt->bind_param("dsdi", $marks, $grade, $grade_point, $existing['id']);
            $stmt->execute();
            $stmt->close();
            echo json_encode(['success' => true, 'message' => 'Marks updated successfully']);
        } else {
            // Insert new result
            $stmt = $conn->prepare("INSERT INTO results (student_id, course_id, marks, grade, grade_point, faculty_id, semester, academic_year) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iidsdiss", $student_id, $course_id, $marks, $grade, $grade_point, $faculty_id, $semester, $academic_year);
            $stmt->execute();
            $stmt->close();
            echo json_encode(['success' => true, 'message' => 'Marks entered successfully']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error saving marks: ' . $e->getMessage()]);
    }
}

else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
?>
