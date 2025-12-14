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

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

// Get statistics
$total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$total_faculty = $conn->query("SELECT COUNT(*) as count FROM faculty")->fetch_assoc()['count'];
$total_courses = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];
$total_departments = $conn->query("SELECT COUNT(*) as count FROM departments")->fetch_assoc()['count'];

// Get recent results
$recent_results_query = $conn->query("
    SELECT r.*, s.name as student_name, s.student_id, c.course_name, c.course_code
    FROM results r
    JOIN students s ON r.student_id = s.id
    JOIN courses c ON r.course_id = c.id
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
            'total_students' => (int)$total_students,
            'total_faculty' => (int)$total_faculty,
            'total_courses' => (int)$total_courses,
            'total_departments' => (int)$total_departments
        ],
        'recent_results' => $recent_results
    ]
]);
?>
