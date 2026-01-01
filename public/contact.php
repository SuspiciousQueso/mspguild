<?php
require_once __DIR__ . '/../includes/bootstrap.php';

use MSPGuild\Core\Auth;

$pageTitle = "Contact Us";
$currentPage = 'contact';
$isLoggedIn = Auth::isLoggedIn();

// ... existing logic (like form handling) ...

// When you need the token in the form:
// $token = Auth::generateCsrfToken();

include __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">Get in Touch</h1>
                <p class="lead text-muted">We're here to help with all your IT needs. Send us a message and we'll respond promptly.</p>
            </div>
<!--
            <?php // if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?php //echo sanitizeOutput($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php // endif; ?>

            <?php // if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?php //echo sanitizeOutput($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php //endif; ?>
-->
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <form action="<?php echo SITE_URL; ?>/../api/contact_handler.php" method="POST" id="contactForm">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required 
                                       placeholder="John Doe" maxlength="255">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required 
                                       placeholder="john@example.com" maxlength="255">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="company" name="company" 
                                       placeholder="Your Company Inc." maxlength="255">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       placeholder="(555) 123-4567" maxlength="50">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="6" required 
                                      placeholder="Tell us about your IT needs or questions..." maxlength="5000"></textarea>
                            <div class="form-text">Maximum 5000 characters</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send"></i> Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Information Cards -->
            <div class="row mt-5">
                <div class="col-md-4 mb-3">
                    <div class="card text-center h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-envelope text-primary display-4"></i>
                            <h5 class="mt-3">Email Us</h5>
                            <p class="text-muted mb-0">
                                <a href="mailto:<?php echo SUPPORT_EMAIL; ?>" class="text-decoration-none">
                                    <?php echo SUPPORT_EMAIL; ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-center h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-telephone text-success display-4"></i>
                            <h5 class="mt-3">Call Us</h5>
                            <p class="text-muted mb-0">
                                <a href="tel:<?php echo str_replace(['(', ')', ' ', '-'], '', SUPPORT_PHONE); ?>" class="text-decoration-none">
                                    <?php echo SUPPORT_PHONE; ?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card text-center h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-clock text-info display-4"></i>
                            <h5 class="mt-3">Business Hours</h5>
                            <p class="text-muted mb-0">
                                Mon-Fri: 8:00 AM - 6:00 PM<br>
                                24/7 Emergency Support
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
