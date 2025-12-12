<?php
require_once __DIR__ . '/../includes/functions.php';
requireRole('student');

// Get student info
$stmt = $conn->prepare("SELECT s.*, d.name as dept_name, d.code as dept_code, u.email 
                        FROM students s 
                        LEFT JOIN departments d ON s.department_id = d.id
                        LEFT JOIN users u ON s.user_id = u.id
                        WHERE s.user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$student_info = $result->fetch_assoc();
$stmt->close();

$student_id = $student_info['id'];

// Get results grouped by semester
$stmt = $conn->prepare("
    SELECT r.*, c.course_code, c.course_name, c.credit, r.semester
    FROM results r
    JOIN courses c ON r.course_id = c.id
    WHERE r.student_id = ? AND r.published = 1
    ORDER BY r.semester DESC, c.course_code
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$all_results = $stmt->get_result();

// Group results by semester
$results_by_semester = [];
while ($row = $all_results->fetch_assoc()) {
    $sem = $row['semester'];
    if (!isset($results_by_semester[$sem])) {
        $results_by_semester[$sem] = [];
    }
    $results_by_semester[$sem][] = $row;
}

// Calculate overall CGPA
$cgpa = calculateCGPA($conn, $student_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result - <?php echo clean($student_info['student_id']); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
        }
        .print-container {
            max-width: 900px;
            margin: 20px auto;
            background: white;
            padding: 40px;
        }
        .print-header {
            text-align: center;
            border-bottom: 3px solid #2196F3;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .print-header h1 {
            margin: 0;
            color: #2196F3;
        }
        .print-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }
        .print-info-item {
            display: flex;
            gap: 10px;
        }
        .print-info-item strong {
            min-width: 120px;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="/student/results.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        
        <div class="print-header">
            <h1>University Management System</h1>
            <h2>Academic Result Sheet</h2>
        </div>
        
        <div class="print-info">
            <div class="print-info-item">
                <strong>Student ID:</strong>
                <span><?php echo clean($student_info['student_id']); ?></span>
            </div>
            <div class="print-info-item">
                <strong>Student Name:</strong>
                <span><?php echo clean($student_info['name']); ?></span>
            </div>
            <div class="print-info-item">
                <strong>Department:</strong>
                <span><?php echo clean($student_info['dept_name']); ?></span>
            </div>
            <div class="print-info-item">
                <strong>Email:</strong>
                <span><?php echo clean($student_info['email']); ?></span>
            </div>
            <div class="print-info-item">
                <strong>Current Year:</strong>
                <span>Year <?php echo $student_info['year']; ?></span>
            </div>
            <div class="print-info-item">
                <strong>Current Semester:</strong>
                <span>Semester <?php echo $student_info['semester']; ?></span>
            </div>
        </div>
        
        <div style="background: #2196F3; color: white; padding: 15px; margin-bottom: 30px; text-align: center;">
            <h3 style="margin: 0;">Cumulative GPA (CGPA): <?php echo number_format($cgpa, 2); ?></h3>
        </div>
        
        <?php foreach ($results_by_semester as $semester => $results): ?>
        <div style="margin-bottom: 40px;">
            <h3 style="background: #f5f5f5; padding: 10px; border-left: 4px solid #2196F3;">
                Semester <?php echo $semester; ?> 
                <span style="float: right;">GPA: <?php echo number_format(calculateGPA($conn, $student_id, $semester), 2); ?></span>
            </h3>
            
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background: #f5f5f5;">
                        <th style="border: 1px solid #ddd; padding: 10px; text-align: left;">Course Code</th>
                        <th style="border: 1px solid #ddd; padding: 10px; text-align: left;">Course Name</th>
                        <th style="border: 1px solid #ddd; padding: 10px; text-align: center;">Credit</th>
                        <th style="border: 1px solid #ddd; padding: 10px; text-align: center;">Marks</th>
                        <th style="border: 1px solid #ddd; padding: 10px; text-align: center;">Grade</th>
                        <th style="border: 1px solid #ddd; padding: 10px; text-align: center;">Grade Point</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total_points = 0;
                    $total_credits = 0;
                    foreach ($results as $result): 
                        $total_points += $result['grade_point'] * $result['credit'];
                        $total_credits += $result['credit'];
                    ?>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?php echo clean($result['course_code']); ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?php echo clean($result['course_name']); ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?php echo $result['credit']; ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?php echo $result['marks']; ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center; font-weight: bold;"><?php echo clean($result['grade']); ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?php echo $result['grade_point']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="background: #f5f5f5; font-weight: bold;">
                        <td colspan="2" style="border: 1px solid #ddd; padding: 8px;">Semester Total</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?php echo $total_credits; ?></td>
                        <td colspan="2" style="border: 1px solid #ddd; padding: 8px; text-align: center;">Semester GPA:</td>
                        <td style="border: 1px solid #ddd; padding: 8px; text-align: center;"><?php echo number_format($total_credits > 0 ? $total_points / $total_credits : 0, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>
        
        <div style="margin-top: 60px; border-top: 2px solid #ddd; padding-top: 20px;">
            <p><strong>Grading Scale:</strong></p>
            <p style="font-size: 12px; line-height: 1.6;">
                A+ (90-100): 4.00 | A (85-89): 3.75 | A- (80-84): 3.50 | B+ (75-79): 3.25 | 
                B (70-74): 3.00 | B- (65-69): 2.75 | C+ (60-64): 2.50 | C (55-59): 2.25 | 
                D (50-54): 2.00 | F (<50): 0.00
            </p>
        </div>
        
        <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #666;">
            <p>Generated on: <?php echo date('F d, Y h:i A'); ?></p>
            <p>This is a computer-generated result sheet.</p>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></script>
</body>
</html>
