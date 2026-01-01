<?php
require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once __DIR__ . '/../../../includes/modules/frontdesk/functions.php';

use MSPGuild\Core\Auth;

Auth::requireAuth();

$user = Auth::getCurrentUser();
if (!$user) {
    header("Location: " . SITE_URL . "/login.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';

    if (!Auth::verifyCsrfToken($csrf)) {
        $error = "Invalid security token.";
    } else {
        $subject = trim($_POST['subject'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? 'medium';
        $ticketType = $_POST['ticket_type'] ?? 'R';

        $allowedPriorities = ['low','medium','high','emergency'];
        if (!in_array($priority, $allowedPriorities, true)) {
            $priority = 'medium';
        }

        if ($subject === '' || $description === '') {
            $error = "Subject and description are required.";
        } elseif (!in_array($ticketType, ['R', 'T'], true)) {
            $error = "Invalid ticket type.";
        } else {
            $ticketId = createTicket((int)$user['id'], [
                    'subject' => $subject,
                    'description' => $description,
                    'priority' => $priority,
                    'ticket_type' => $ticketType,
            ]);

            if ($ticketId) {
                header("Location: view.php?id=" . (int)$ticketId . "&created=1");
                exit;
            }

            $error = "Failed to create ticket. Please try again.";
        }
    }
}

$pageTitle = "Open Ticket";
$currentPage = 'frontdesk';
$isLoggedIn = true;

include __DIR__ . '/../../../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0"><i class="bi bi-inbox"></i> FrontDesk</h1>
                <a href="index.php" class="btn btn-outline-light text-dark border">
                    Back to Tickets
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo sanitizeOutput($error); ?></div>
            <?php endif; ?>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <strong>New Ticket</strong>
                </div>
                <div class="card-body">
                    <form method="POST" action="create.php">
                        <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCsrfToken(); ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ticket Type</label>
                                <select name="ticket_type" class="form-select" required>
                                    <option value="R" selected>Request (R)</option>
                                    <option value="T">Trouble / Incident (T)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-select">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="emergency">Emergency</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control" required maxlength="255"
                                       placeholder="Short summary of the issue">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="6" required
                                          placeholder="Details, errors, steps, impact, screenshots, etc."></textarea>
                            </div>

                            <div class="col-12 d-grid">
                                <button class="btn btn-primary btn-lg" type="submit">
                                    Submit Ticket
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
