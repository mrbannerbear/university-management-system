<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        setErrorMessage('Please enter both email and password.');
        header('Location: /index.php');
        exit();
    }
    
    // Fetch user from database
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        setErrorMessage('Invalid email or password.');
        header('Location: /index.php');
        exit();
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        setErrorMessage('Invalid email or password.');
        header('Location: /index.php');
        exit();
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['department_id'] = $user['department_id'];
    
    // Redirect based on role
    $role = $user['role'];
    header("Location: /$role/index.php");
    exit();
} else {
    header('Location: /index.php');
    exit();
}
?>
