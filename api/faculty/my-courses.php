<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

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

// Get assigned courses
$query = "
    SELECT c.*, d.name as department_name, fc.id as assignment_id
    FROM faculty_courses fc
    JOIN courses c ON fc.course_id = c.id
    LEFT JOIN departments d ON c.department_id = d.id
    WHERE fc.faculty_id = ?
    ORDER BY c.semester, c.course_code
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}
$stmt->close();

echo json_encode(['success' => true, 'data' => $courses]);
?>
