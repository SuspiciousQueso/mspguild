<?php
/**
 * MSPGuild Global Bootstrap
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load the merged configuration
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

// Load Auth
require_once __DIR__ . '/Auth.php';

// Start Session using the secure function defined in Auth.php
if (function_exists('startSecureSession')) {
    startSecureSession();
} elseif (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Any logic that must run on EVERY request goes here
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? sanitizeOutput($pageTitle) . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <meta name="description" content="<?php echo SITE_TAGLINE; ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/custom.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo SITE_URL; ?>/index.php">
                <i class="bi bi-shield-check"></i> <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo TICKET_SYSTEM_URL; ?>">
                            <i class="bi bi-ticket-perforated"></i> Support Tickets
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/contact.php">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo RESUME_URL; ?>" target="_blank">
                            <i class="bi bi-file-earmark-person"></i> Resume
                        </a>
                    </li>
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/logout.php">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage === 'login' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/login.php">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
