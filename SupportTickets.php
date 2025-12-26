<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/tickets.php';

requireAuth();
$user = getCurrentUser();

if (!defined('ENABLE_TICKETING') || !ENABLE_TICKETING) {
    header("Location: index.php");
    exit;
}

$tickets = getUserTickets($user['id']);
$pageTitle = "My Support Tickets";
include __DIR__ . '/../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Support Tickets</h2>
        <a href="ticket_create.php" class="btn btn-success">Open New Ticket</a>
    </div>

    <?php if (empty($tickets)): ?>
        <div class="alert alert-info">
            You don't have any support tickets yet.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover border">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Last Updated</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td>#<?php echo $ticket['id']; ?></td>
                            <td><strong><?php echo sanitizeOutput($ticket['subject']); ?></strong></td>
                            <td>
                                <span class="badge <?php echo getStatusBadgeClass($ticket['status']); ?>">
                                    <?php echo ucfirst(str_replace('-', ' ', $ticket['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo ucfirst($ticket['priority']); ?></td>
                            <td><?php echo date('M j, Y g:i a', strtotime($ticket['updated_at'])); ?></td>
                            <td>
                                <a href="ticket_view.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
