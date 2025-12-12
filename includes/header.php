<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? clean($page_title) . ' - ' : ''; ?>University Management System</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="main-wrapper">
        <?php include __DIR__ . '/sidebar.php'; ?>
        <div class="content-wrapper">
            <header class="top-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title"><?php echo isset($page_title) ? clean($page_title) : 'Dashboard'; ?></h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo clean($_SESSION['name']); ?></span>
                        <span class="user-role">(<?php echo ucfirst(clean($_SESSION['role'])); ?>)</span>
                    </div>
                </div>
            </header>
            <main class="main-content">
                <?php echo displayMessages(); ?>
    <?php else: ?>
    <div class="login-wrapper">
    <?php endif; ?>
