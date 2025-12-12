<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
$conn = require_once __DIR__ . '/../config/database.php';

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Check user role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /index.php');
        exit();
    }
}

// Redirect if user doesn't have required role
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header('Location: /index.php');
        exit();
    }
}

// Sanitize output
function clean($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Calculate grade and grade point from marks
function calculateGrade($marks) {
    if ($marks >= 90) {
        return ['grade' => 'A+', 'grade_point' => 4.00];
    } elseif ($marks >= 85) {
        return ['grade' => 'A', 'grade_point' => 3.75];
    } elseif ($marks >= 80) {
        return ['grade' => 'A-', 'grade_point' => 3.50];
    } elseif ($marks >= 75) {
        return ['grade' => 'B+', 'grade_point' => 3.25];
    } elseif ($marks >= 70) {
        return ['grade' => 'B', 'grade_point' => 3.00];
    } elseif ($marks >= 65) {
        return ['grade' => 'B-', 'grade_point' => 2.75];
    } elseif ($marks >= 60) {
        return ['grade' => 'C+', 'grade_point' => 2.50];
    } elseif ($marks >= 55) {
        return ['grade' => 'C', 'grade_point' => 2.25];
    } elseif ($marks >= 50) {
        return ['grade' => 'D', 'grade_point' => 2.00];
    } else {
        return ['grade' => 'F', 'grade_point' => 0.00];
    }
}

// Calculate GPA for a semester
function calculateGPA($conn, $student_id, $semester) {
    $stmt = $conn->prepare("
        SELECT r.grade_point, c.credit 
        FROM results r
        JOIN courses c ON r.course_id = c.id
        WHERE r.student_id = ? AND r.semester = ? AND r.published = 1
    ");
    $stmt->bind_param("ii", $student_id, $semester);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $total_points = 0;
    $total_credits = 0;
    
    while ($row = $result->fetch_assoc()) {
        $total_points += $row['grade_point'] * $row['credit'];
        $total_credits += $row['credit'];
    }
    
    $stmt->close();
    
    if ($total_credits == 0) {
        return 0;
    }
    
    return round($total_points / $total_credits, 2);
}

// Calculate CGPA for all semesters
function calculateCGPA($conn, $student_id) {
    $stmt = $conn->prepare("
        SELECT r.grade_point, c.credit 
        FROM results r
        JOIN courses c ON r.course_id = c.id
        WHERE r.student_id = ? AND r.published = 1
    ");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $total_points = 0;
    $total_credits = 0;
    
    while ($row = $result->fetch_assoc()) {
        $total_points += $row['grade_point'] * $row['credit'];
        $total_credits += $row['credit'];
    }
    
    $stmt->close();
    
    if ($total_credits == 0) {
        return 0;
    }
    
    return round($total_points / $total_credits, 2);
}

// Format date
function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

// Get user info
function getUserInfo($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Get department name by id
function getDepartmentName($conn, $dept_id) {
    if (!$dept_id) return 'N/A';
    $stmt = $conn->prepare("SELECT name FROM departments WHERE id = ?");
    $stmt->bind_param("i", $dept_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $dept = $result->fetch_assoc();
    $stmt->close();
    return $dept ? $dept['name'] : 'N/A';
}

// Success message
function setSuccessMessage($message) {
    $_SESSION['success_message'] = $message;
}

// Error message
function setErrorMessage($message) {
    $_SESSION['error_message'] = $message;
}

// Display and clear messages
function displayMessages() {
    $html = '';
    if (isset($_SESSION['success_message'])) {
        $html .= '<div class="alert alert-success">' . clean($_SESSION['success_message']) . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        $html .= '<div class="alert alert-error">' . clean($_SESSION['error_message']) . '</div>';
        unset($_SESSION['error_message']);
    }
    return $html;
}
?>
