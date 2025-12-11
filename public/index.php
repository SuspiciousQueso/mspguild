<?php
// Start session FIRST, before any output or includes that might output
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
startSecureSession();

$pageTitle = 'Home';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold mb-3">Professional IT Support & Managed Services</h1>
                <p class="lead mb-4">Your trusted partner for comprehensive IT solutions, cybersecurity, and business technology support.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-light btn-lg">
                        <i class="bi bi-envelope"></i> Get Started
                    </a>
                    <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Client Portal
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="bi bi-laptop display-1"></i>
                    <i class="bi bi-shield-lock display-1 ms-3"></i>
                    <i class="bi bi-cloud-check display-1 ms-3"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Services</h2>
            <p class="text-muted">Comprehensive IT solutions tailored to your business needs</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-primary text-white mb-3">
                            <i class="bi bi-gear fs-1"></i>
                        </div>
                        <h4 class="card-title">Managed IT Services</h4>
                        <p class="card-text text-muted">Proactive monitoring, maintenance, and support for all your IT infrastructure needs.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-success text-white mb-3">
                            <i class="bi bi-shield-check fs-1"></i>
                        </div>
                        <h4 class="card-title">Cybersecurity</h4>
                        <p class="card-text text-muted">Protect your business from threats with enterprise-grade security solutions.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-info text-white mb-3">
                            <i class="bi bi-cloud-arrow-up fs-1"></i>
                        </div>
                        <h4 class="card-title">Cloud Solutions</h4>
                        <p class="card-text text-muted">Migrate, manage, and optimize your cloud infrastructure with confidence.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-warning text-white mb-3">
                            <i class="bi bi-headset fs-1"></i>
                        </div>
                        <h4 class="card-title">Help Desk Support</h4>
                        <p class="card-text text-muted">Responsive technical support when you need it most, keeping your team productive.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-danger text-white mb-3">
                            <i class="bi bi-arrow-repeat fs-1"></i>
                        </div>
                        <h4 class="card-title">Backup & Recovery</h4>
                        <p class="card-text text-muted">Ensure business continuity with reliable backup and disaster recovery solutions.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    <div class="card-body text-center p-4">
                        <div class="icon-circle bg-secondary text-white mb-3">
                            <i class="bi bi-diagram-3 fs-1"></i>
                        </div>
                        <h4 class="card-title">Network Management</h4>
                        <p class="card-text text-muted">Design, implement, and maintain robust network infrastructure for your business.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose Us</h2>
            <p class="text-muted">We're committed to your success</p>
        </div>
        <div class="row g-4">
            <div class="col-md-3 col-6 text-center">
                <i class="bi bi-lightning-charge text-primary display-4"></i>
                <h5 class="mt-3">Fast Response</h5>
                <p class="text-muted small">Quick turnaround on all support requests</p>
            </div>
            <div class="col-md-3 col-6 text-center">
                <i class="bi bi-people text-success display-4"></i>
                <h5 class="mt-3">Expert Team</h5>
                <p class="text-muted small">Certified professionals you can trust</p>
            </div>
            <div class="col-md-3 col-6 text-center">
                <i class="bi bi-clock text-info display-4"></i>
                <h5 class="mt-3">24/7 Monitoring</h5>
                <p class="text-muted small">Round-the-clock system surveillance</p>
            </div>
            <div class="col-md-3 col-6 text-center">
                <i class="bi bi-graph-up text-warning display-4"></i>
                <h5 class="mt-3">Proven Results</h5>
                <p class="text-muted small">Track record of client satisfaction</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Ready to Transform Your IT?</h2>
        <p class="lead mb-4">Let's discuss how we can help your business thrive with reliable IT solutions.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-light btn-lg">
                <i class="bi bi-envelope"></i> Contact Us Today
            </a>
            <a href="tel:<?php echo str_replace(['(', ')', ' ', '-'], '', SUPPORT_PHONE); ?>" class="btn btn-outline-light btn-lg">
                <i class="bi bi-telephone"></i> <?php echo SUPPORT_PHONE; ?>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
