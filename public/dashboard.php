<?php
require_once __DIR__ . '/../includes/bootstrap.php';

use MSPGuild\Core\Auth;

// Protect the page
Auth::Auth::requireAuth();

$pageTitle   = "Dashboard";
$currentPage = 'dashboard';
$isLoggedIn  = true;

$user = Auth::getCurrentUser();

include __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <h1 class="mb-4">Welcome, <?php echo sanitizeOutput($user['full_name']); ?> 👋</h1>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">FrontDesk</h5>
                    <p class="card-text">View and manage your support requests.</p>
                    <a href="<?php echo FRONTDESK_URL; ?>" class="btn btn-primary">Go to FrontDesk</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Profile</h5>
                    <p class="card-text">Update your account information.</p>
                    <a href="<?php echo SITE_URL; ?>/user_profile_update.php" class="btn btn-outline-secondary">Manage Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
