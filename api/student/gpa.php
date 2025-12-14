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

// Get semester-wise GPA
$query = "
    SELECT DISTINCT semester
    FROM results
    WHERE student_id = ? AND published = 1
    ORDER BY semester
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$semester_gpas = [];
while ($row = $result->fetch_assoc()) {
    $semester = $row['semester'];
    $gpa = calculateGPA($conn, $student_id, $semester);
    $semester_gpas[] = [
        'semester' => $semester,
        'gpa' => (float)$gpa
    ];
}
$stmt->close();

// Calculate CGPA
$cgpa = calculateCGPA($conn, $student_id);

echo json_encode([
    'success' => true,
    'data' => [
        'semester_gpas' => $semester_gpas,
        'cgpa' => (float)$cgpa
    ]
]);
?>
