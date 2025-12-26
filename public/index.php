<?php
require_once __DIR__ . '/../includes/auth.php';

$user = getCurrentUser();
$pageTitle = "Dashboard";
include __DIR__ . '/../includes/header.php';
?>

    <div class="container mt-4">
        <?php if (!$user): ?>
            <div class="jumbotron text-center" style="padding: 4rem 2rem; background: #f8f9fa; border-radius: 0.3rem;">
                <h1 class="display-4">Welcome to MSP Guild</h1>
                <p class="lead">The ultimate portal for managing your managed service provider resources.</p>
                <hr class="my-4">
                <p>Please log in or register to access your dashboard and ticketing system.</p>
                <a class="btn btn-primary btn-lg" href="login.php" role="button">Login</a>
                <a class="btn btn-outline-secondary btn-lg" href="user_registration.php" role="button">Register</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-12">
                    <h2>Welcome back, <?php echo sanitizeOutput($user['full_name']); ?>!</h2>
                    <p class="text-muted">Company: <?php echo sanitizeOutput($user['company_name'] ?? 'Individual'); ?> | Tier: <span class="badge badge-info"><?php echo strtoupper($user['service_tier']); ?></span></p>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Ticketing Card (The "Toggleable" Feature) -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Support Tickets</h5>
                            <p class="card-text">View your active requests or open a new support ticket.</p>
                            <?php if (defined('ENABLE_TICKETING') && ENABLE_TICKETING): ?>
                                <a href="tickets.php" class="btn btn-primary">Go to Tickets</a>
                            <?php else: ?>
                                <span class="badge badge-secondary">Module Disabled</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Profile Management Card -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Account Settings</h5>
                            <p class="card-text">Update your contact information and company details.</p>
                            <a href="user_profile_update.php" class="btn btn-outline-primary">Manage Profile</a>
                        </div>
                    </div>
                </div>

                <!-- Resources/Docs Card -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Guild Resources</h5>
                            <p class="card-text">Access documentation and tools available for your tier.</p>
                            <a href="#" class="btn btn-outline-success disabled">Coming Soon</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>