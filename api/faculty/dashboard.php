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

requireRole('faculty');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

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

// Get assigned courses count
$assigned_courses = $conn->query("SELECT COUNT(*) as count FROM faculty_courses WHERE faculty_id = $faculty_id")->fetch_assoc()['count'];

// Get total students in assigned courses
$total_students_query = $conn->query("
    SELECT COUNT(DISTINCT s.id) as count
    FROM students s
    JOIN results r ON s.id = r.student_id
    WHERE r.faculty_id = $faculty_id
");
$total_students = $total_students_query->fetch_assoc()['count'];

// Get results entered count
$results_entered = $conn->query("SELECT COUNT(*) as count FROM results WHERE faculty_id = $faculty_id")->fetch_assoc()['count'];

// Get recent results
$recent_results_query = $conn->query("
    SELECT r.*, s.name as student_name, s.student_id, c.course_name, c.course_code
    FROM results r
    JOIN students s ON r.student_id = s.id
    JOIN courses c ON r.course_id = c.id
    WHERE r.faculty_id = $faculty_id
    ORDER BY r.created_at DESC
    LIMIT 10
");

$recent_results = [];
while ($row = $recent_results_query->fetch_assoc()) {
    $recent_results[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => [
        'statistics' => [
            'assigned_courses' => (int)$assigned_courses,
            'total_students' => (int)$total_students,
            'results_entered' => (int)$results_entered
        ],
        'recent_results' => $recent_results
    ]
]);
?>
