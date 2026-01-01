<?php
$pageTitle   = $pageTitle   ?? '';
$currentPage = $currentPage ?? '';
$isLoggedIn  = $isLoggedIn  ?? false;
/**
 * MSPGuild Header
 */
// The bootstrap is already loaded in the main entry files (index.php, etc.)
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
<?php if ($isLoggedIn): ?>
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
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/index.php">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo defined('FRONTDESK_URL') ? FRONTDESK_URL : (SITE_URL . '/modules/frontdesk/index.php'); ?>">
                            FrontDesk
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/contact.php">Contact</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo defined('RESUME_URL') ? RESUME_URL : '#'; ?>" target="_blank">Resume</a>
                    </li>

                    <?php if (!empty($isLoggedIn)): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage === 'profile' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/user_profile_update.php">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage === 'register' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/user_registration.php">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage === 'login' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <?php else: ?>
                <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
                    <div class="container">
                        <a class="navbar-brand fw-bold" href="<?php echo SITE_URL; ?>/index.php">
                            <i class="bi bi-shield-check"></i> <?php echo SITE_NAME; ?>
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                                <li class="nav-item">
                                    <a class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/index.php">Home</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo defined('FRONTDESK_URL') ? FRONTDESK_URL : (SITE_URL . '/modules/frontdesk/index.php'); ?>">
                                        FrontDesk
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/contact.php">Contact</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="<?php echo defined('RESUME_URL') ? RESUME_URL : '#'; ?>" target="_blank">Resume</a>
                                </li>
                                <li class="nav-item">
                        <a class="nav-link <?php echo $currentPage === 'login' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/login.php"><i class="bi bi-box-arrow-in-right"></i>Login</a>
                    </li>

                       <!--        <?php // if (!empty($isLoggedIn)): ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?php //echo $currentPage === 'dashboard' ? 'active' : ''; ?>" href="<?php //echo SITE_URL; ?>/dashboard.php">Dashboard</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php //echo $currentPage === 'profile' ? 'active' : ''; ?>" href="<?php //echo SITE_URL; ?>/user_profile_update.php">Profile</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php //echo SITE_URL; ?>/logout.php">Logout</a>
                                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php //echo $currentPage === 'register' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/user_registration.php"><i class="bi bi-person-plus"></i>Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php //echo $currentPage === 'login' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/login.php"><i class="bi bi-box-arrow-in-right"></i>Login</a>
                    </li>
                <?php //endif; ?>
                -->
            </div>
        </div>
    </nav>
<?php endif; ?>