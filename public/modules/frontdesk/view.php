<?php
require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once __DIR__ . '/../../../includes/modules/frontdesk/functions.php';


use MSPGuild\Core\Auth;

Auth::Auth::requireAuth();
$user = Auth::getCurrentUser();
$ticketId = $_GET['id'] ?? 0;
$ticket = getTicketById($ticketId, $user['id']);

if (!$ticket) {
    die("Ticket not found or access denied.");
}

// Handle new comments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if (Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        addTicketComment($ticketId, $user['id'], $_POST['comment']);
        header("Location: view.php?id=" . $ticketId);
        exit;
    }
}

$comments = getTicketComments($ticketId);
$pageTitle = "Ticket #" . $ticketId;
$currentPage = 'ticketing';
$isLoggedIn = true;
include __DIR__ . '/../../../includes/header.php';
?>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?php echo sanitizeOutput($ticket['subject']); ?> <span class="text-muted">#<?php echo $ticket['id']; ?></span></h2>
                    <span class="badge <?php echo getStatusBadgeClass($ticket['status']); ?> fs-6"><?php echo ucfirst($ticket['status']); ?></span>
                </div>

                <!-- Original Description -->
                <div class="card mb-4 border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <strong><?php echo sanitizeOutput($user['full_name']); ?></strong>
                            <small class="text-muted"><?php echo date('M j, Y g:i a', strtotime($ticket['created_at'])); ?></small>
                        </div>
                        <p class="mb-0 text-pre-wrap"><?php echo nl2br(sanitizeOutput($ticket['description'])); ?></p>
                    </div>
                </div>

                <h4 class="mb-3">Updates</h4>
                <?php foreach ($comments as $comment): ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold"><?php echo sanitizeOutput($comment['full_name']); ?></span>
                                <small class="text-muted"><?php echo date('M j, Y g:i a', strtotime($comment['created_at'])); ?></small>
                            </div>
                            <p class="mb-0"><?php echo nl2br(sanitizeOutput($comment['comment'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Post a Reply -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5>Post a Reply</h5>
                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCsrfToken(); ?>">
                            <div class="mb-3">
                                <textarea name="comment" class="form-control" rows="4" required placeholder="Type your update here..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Post Reply</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <div class="card shadow-sm mt-5 mt-lg-0">
                    <div class="card-header bg-light">Ticket Details</div>
                    <div class="card-body">
                        <p><strong>Priority:</strong> <?php echo ucfirst($ticket['priority']); ?></p>
                        <p><strong>Created:</strong> <?php echo date('F j, Y', strtotime($ticket['created_at'])); ?></p>
                        <p><strong>Last Updated:</strong> <?php echo date('F j, Y', strtotime($ticket['updated_at'])); ?></p>
                        <hr>
                        <a href="index.php" class="btn btn-outline-secondary w-100">Back to List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>