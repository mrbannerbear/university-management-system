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

requireRole('student');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

// Get student ID from session
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student_data = $result->fetch_assoc();
$stmt->close();

if (!$student_data) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Student profile not found']);
    exit();
}

$student_id = $student_data['id'];

// Get student info
$stmt = $conn->prepare("
    SELECT s.*, d.name as department_name
    FROM students s
    LEFT JOIN departments d ON s.department_id = d.id
    WHERE s.id = ?
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student_info = $result->fetch_assoc();
$stmt->close();

// Calculate GPA and CGPA
$current_semester = $student_info['semester'];
$gpa = calculateGPA($conn, $student_id, $current_semester);
$cgpa = calculateCGPA($conn, $student_id);

// Get total courses enrolled
$total_courses = $conn->query("
    SELECT COUNT(DISTINCT course_id) as count 
    FROM results 
    WHERE student_id = $student_id
")->fetch_assoc()['count'];

// Get published results count
$published_results = $conn->query("
    SELECT COUNT(*) as count 
    FROM results 
    WHERE student_id = $student_id AND published = 1
")->fetch_assoc()['count'];

echo json_encode([
    'success' => true,
    'data' => [
        'student_info' => $student_info,
        'statistics' => [
            'current_gpa' => (float)$gpa,
            'cgpa' => (float)$cgpa,
            'total_courses' => (int)$total_courses,
            'published_results' => (int)$published_results
        ]
    ]
]);
?>
