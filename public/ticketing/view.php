<?php
require_once __DIR__ . '/../../includes/modules/ticketing/functions.php';

requireAuth();
$user = getCurrentUser();

if (!defined('ENABLE_TICKETING') || !ENABLE_TICKETING) {
    header("Location: ../index.php");
    exit;
}

$tickets = getUserTickets($user['id']);
$pageTitle = "My Support Tickets";
include __DIR__ . '/../../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Support Tickets</h2>
        <a href="create.php" class="btn btn-success">Open New Ticket</a>
    </div>
    <!-- ... (rest of the table code) ... -->
    <!-- Update the 'View' link to point to view.php?id=... -->
    <a href="view.php?id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
    <!-- ... -->
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
