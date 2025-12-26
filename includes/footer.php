    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5><i class="bi bi-shield-check"></i> <?php echo SITE_NAME; ?></h5>
                    <p class="text-muted"><?php echo SITE_TAGLINE; ?></p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>/index.php" class="text-muted text-decoration-none">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact.php" class="text-muted text-decoration-none">Contact Us</a></li>
                        <li><a href="<?php echo TICKET_SYSTEM_URL; ?>" class="text-muted text-decoration-none">Support Tickets</a></li>
                        <li><a href="<?php echo KNOWLEDGE_BASE_URL; ?>" class="text-muted text-decoration-none">Knowledge Base</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Contact Information</h6>
                    <p class="text-muted mb-1">
                        <i class="bi bi-envelope"></i> <?php echo SUPPORT_EMAIL; ?>
                    </p>
                    <p class="text-muted">
                        <i class="bi bi-telephone"></i> <?php echo SUPPORT_PHONE; ?>
                    </p>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="row">
                <div class="col-12 text-center text-muted">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
