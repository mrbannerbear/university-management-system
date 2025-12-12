<?php
require_once __DIR__ . '/includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    $role = $_SESSION['role'];
    header("Location: /$role/index.php");
    exit();
}

$page_title = 'Login';
include __DIR__ . '/includes/header.php';
?>

<div class="login-container">
    <div class="login-box">
        <div class="login-header">
            <i class="fas fa-university"></i>
            <h2>University Management System</h2>
            <p>Please login to continue</p>
        </div>
        
        <?php echo displayMessages(); ?>
        
        <form action="/login.php" method="POST" class="login-form">
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i>
                    Email Address
                </label>
                <input type="email" id="email" name="email" required placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i>
                    Password
                </label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </button>
        </form>
        
        <div class="login-footer">
            <div class="demo-credentials">
                <h4>Demo Credentials</h4>
                <div class="credentials-grid">
                    <div class="credential-item">
                        <strong>Admin:</strong>
                        <span>admin@university.com / admin123</span>
                    </div>
                    <div class="credential-item">
                        <strong>Faculty:</strong>
                        <span>faculty1@university.com / faculty123</span>
                    </div>
                    <div class="credential-item">
                        <strong>Student:</strong>
                        <span>student1@university.com / student123</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
