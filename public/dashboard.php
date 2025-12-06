<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';

// Require authentication
requireAuth();

$user = getCurrentUser();
if (!$user) {
    logoutUser();
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}
?>

<div class="container py-5">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white shadow">
                <div class="card-body p-4">
                    <h2 class="mb-2">Welcome back, <?php echo sanitizeOutput($user['full_name']); ?>!</h2>
                    <p class="mb-0">
                        <i class="bi bi-building"></i> <?php echo sanitizeOutput($user['company_name'] ?? 'N/A'); ?>
                        <span class="ms-3"><i class="bi bi-award"></i> <?php echo sanitizeOutput($user['service_tier']); ?> Plan</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3">Quick Actions</h4>
        </div>
        <div class="col-md-4 mb-3">
            <a href="<?php echo TICKET_SYSTEM_URL; ?>" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-ticket-perforated text-primary display-4"></i>
                        <h5 class="mt-3">Open Support Ticket</h5>
                        <p class="text-muted small">Submit a new support request</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="<?php echo KNOWLEDGE_BASE_URL; ?>" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-book text-success display-4"></i>
                        <h5 class="mt-3">Knowledge Base</h5>
                        <p class="text-muted small">Browse help articles and guides</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="<?php echo SITE_URL; ?>/contact.php" class="text-decoration-none">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    <div class="card-body text-center p-4">
                        <i class="bi bi-envelope text-info display-4"></i>
                        <h5 class="mt-3">Contact Us</h5>
                        <p class="text-muted small">Send us a message</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Account Information -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle"></i> Account Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted"><strong>Full Name:</strong></td>
                                <td><?php echo sanitizeOutput($user['full_name']); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted"><strong>Email:</strong></td>
                                <td><?php echo sanitizeOutput($user['email']); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted"><strong>Phone:</strong></td>
                                <td><?php echo sanitizeOutput($user['contact_phone'] ?? 'Not provided'); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted"><strong>Company:</strong></td>
                                <td><?php echo sanitizeOutput($user['company_name'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted"><strong>Service Tier:</strong></td>
                                <td><span class="badge bg-primary"><?php echo sanitizeOutput($user['service_tier']); ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Account Activity</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted"><strong>Member Since:</strong></td>
                                <td><?php echo date('F j, Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted"><strong>Last Login:</strong></td>
                                <td><?php echo $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'N/A'; ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <div class="d-grid">
                        <a href="<?php echo RESUME_URL; ?>" class="btn btn-outline-primary" target="_blank">
                            <i class="bi bi-file-earmark-person"></i> View Service Provider Resume
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resources Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-link-45deg"></i> Useful Resources</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="<?php echo TICKET_SYSTEM_URL; ?>" class="list-group-item list-group-item-action">
                            <i class="bi bi-ticket-perforated text-primary"></i> View My Support Tickets
                        </a>
                        <a href="<?php echo KNOWLEDGE_BASE_URL; ?>" class="list-group-item list-group-item-action">
                            <i class="bi bi-book text-success"></i> Browse Knowledge Base
                        </a>
                        <a href="<?php echo RESUME_URL; ?>" class="list-group-item list-group-item-action" target="_blank">
                            <i class="bi bi-file-earmark-person text-info"></i> Service Provider Resume
                        </a>
                        <a href="<?php echo SITE_URL; ?>/contact.php" class="list-group-item list-group-item-action">
                            <i class="bi bi-envelope text-warning"></i> Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
