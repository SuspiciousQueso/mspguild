<?php
require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once __DIR__ . '/../../../includes/modules/frontdesk/functions.php';


use MSPGuild\Core\Auth;

Auth::Auth::requireAuth();
$user = Auth::getCurrentUser();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid security token.";
    } else {
        $ticketData = [
            'subject' => $_POST['subject'] ?? '',
            'description' => $_POST['description'] ?? '',
            'priority' => $_POST['priority'] ?? 'medium'
        ];

        if (empty($ticketData['subject']) || empty($ticketData['description'])) {
            $error = "Subject and Description are required.";
        } else {
            $ticketId = createTicket($user['id'], $ticketData);
            if ($ticketId) {
                header("Location: view.php?id=" . $ticketId . "&created=1");
                exit;
            } else {
                $error = "Failed to create ticket. Please try again.";
            }
        }
    }
}

$pageTitle = "Open New Ticket";
$currentPage = 'ticketing';
$isLoggedIn = true;
include __DIR__ . '/../../../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Tickets</a></li>
                    <li class="breadcrumb-item active">New Ticket</li>
                </ol>
            </nav>

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Submit Support Request</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCsrfToken(); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="Brief summary of the issue" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="low">Low - General Inquiry</option>
                                <option value="medium" selected>Medium - Normal Support</option>
                                <option value="high">High - Impacting Work</option>
                                <option value="emergency">Emergency - System Down</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="6" placeholder="Please provide as much detail as possible..." required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Submit Ticket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>