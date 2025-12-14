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

// Get all results entered by this faculty
$query = "
    SELECT r.*, s.name as student_name, s.student_id, c.course_code, c.course_name
    FROM results r
    JOIN students s ON r.student_id = s.id
    JOIN courses c ON r.course_id = c.id
    WHERE r.faculty_id = ?
    ORDER BY r.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}
$stmt->close();

echo json_encode(['success' => true, 'data' => $results]);
?>
