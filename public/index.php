<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$pageTitle = "Welcome to MSPGuild";
include __DIR__ . '/../includes/header.php';
?>

    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-3 fw-bold text-primary mb-4">Your Command Center</h1>
                <p class="lead mb-4">Self-hosted, modular, and built for technicians. Welcome to the MSPGuild Customer Portal.</p>

                <?php if (isLoggedIn()): ?>
                    <a href="dashboard.php" class="btn btn-primary btn-lg px-5">Go to Dashboard</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-lg px-5">Client Login</a>
                    <a href="contact.php" class="btn btn-outline-secondary btn-lg px-5 ms-2">Contact Us</a>
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&q=80&w=800" alt="Tech Dashboard" class="img-fluid rounded shadow-lg">
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>