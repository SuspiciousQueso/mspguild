<?php
require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once __DIR__ . '/../../../includes/modules/frontdesk/functions.php';


use MSPGuild\Core\Auth;

// Require login
Auth::requireAuth();

$user = Auth::getCurrentUser();
$tickets = getUserTickets($user['id']);

$pageTitle = "Support Tickets";
$currentPage = 'ticketing';
$isLoggedIn = true;

include __DIR__ . '/../../../includes/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-ticket-perforated"></i> Support Tickets</h1>
        <a href="create.php" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Open New Ticket
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">No tickets found. Need help? Open a ticket above!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td>#<?php echo $ticket['id']; ?></td>
                                <td><strong><?php echo sanitizeOutput($ticket['ticket_number'] ?? ('#' . $ticket['id'])); ?></strong></td>
                                <td>
                                    <span class="badge <?php echo getStatusBadgeClass($ticket['status']); ?>">
                                        <?php echo ucfirst($ticket['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo ucfirst($ticket['priority']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($ticket['created_at'])); ?></td>
                                <td>
                                    <a href="view.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-secondary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
