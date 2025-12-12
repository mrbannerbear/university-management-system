<?php
// Helper functions for the API

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']);
}

// Check user role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Require login (return error if not logged in)
function requireLogin() {
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized. Please login.']);
        exit();
    }
}

// Require specific role (return error if user doesn't have role)
function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Forbidden. Insufficient permissions.']);
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

// Get JSON input from request body
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}
?>
