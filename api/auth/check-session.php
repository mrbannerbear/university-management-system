<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

if (isLoggedIn()) {
    echo json_encode([
        'success' => true,
        'data' => [
            'logged_in' => true,
            'user_id' => $_SESSION['user_id'],
            'name' => $_SESSION['name'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'],
            'department_id' => $_SESSION['department_id']
        ]
    ]);
} else {
    echo json_encode([
        'success' => true,
        'data' => [
            'logged_in' => false
        ]
    ]);
}
?>
