<?php
require_once __DIR__ . '/../../includes/modules/ticketing/functions.php';

requireAuth();
$user = getCurrentUser();

// Module check
if (!defined('ENABLE_TICKETING') || !ENABLE_TICKETING) {
    header("Location: ../index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic CSRF check if you have it implemented, or just standard POST handling
    $data = [
        'subject' => trim($_POST['subject'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'priority' => $_POST['priority'] ?? 'medium'
    ];

    if (empty($data['subject']) || empty($data['description'])) {
        $error = "Subject and Description are required.";
    } else {
        $ticketId = createTicket($user['id'], $data);
        if ($ticketId) {
            // Success! Redirect to the new ticket view
            header("Location: view.php?id=" . $ticketId);
            exit;
        } else {
            $error = "Database error: Could not create ticket.";
        }
    }
}

$pageTitle = "Open New Ticket";
include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Tickets</a></li>
            <li class="breadcrumb-item active">New Ticket</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white p-3">
                    <h4 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Open a New Support Ticket</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo sanitizeOutput($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="create.php">
                        <div class="mb-4">
                            <label for="subject" class="form-label fw-bold">Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control form-control-lg" 
                                   required placeholder="e.g., Cannot access email on mobile">
                            <div class="form-text">A brief summary of the issue you're experiencing.</div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="priority" class="form-label fw-bold">Priority Level</label>
                                <select name="priority" id="priority" class="form-select">
                                    <option value="low">Low - General Inquiry</option>
                                    <option value="medium" selected>Medium - Standard Support</option>
                                    <option value="high">High - Critical Issue</option>
                                    <option value="emergency">Emergency - System Down</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Department</label>
                                <input type="text" class="form-control" value="Technical Support" disabled>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Issue Details</label>
                            <textarea name="description" id="description" rows="8" class="form-control" 
                                      required placeholder="Please describe the steps to reproduce the issue..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                Submit Ticket <i class="bi bi-send ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-4 text-center text-muted">
                <small><i class="bi bi-info-circle me-1"></i> Typical response time is within 4 hours during business hours.</small>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>