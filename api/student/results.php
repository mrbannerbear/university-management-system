<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

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

// Get all published results
$query = "
    SELECT r.*, c.course_code, c.course_name, c.credit, c.semester
    FROM results r
    JOIN courses c ON r.course_id = c.id
    WHERE r.student_id = ? AND r.published = 1
    ORDER BY r.semester, c.course_code
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}
$stmt->close();

// Group results by semester
$results_by_semester = [];
foreach ($results as $result) {
    $sem = $result['semester'];
    if (!isset($results_by_semester[$sem])) {
        $results_by_semester[$sem] = [];
    }
    $results_by_semester[$sem][] = $result;
}

echo json_encode(['success' => true, 'data' => $results_by_semester]);
?>
