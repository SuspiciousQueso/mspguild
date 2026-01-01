<?php
require_once __DIR__ . '/../../../includes/bootstrap.php';

use MSPGuild\Core\Auth;

Auth::requireAuth();

$user = Auth::getCurrentUser();
$error = '';

function createTicket(int $userId, array $data)
{
    $pdo = \MSPGuild\Core\Database::getConnection();

    // 1) Get user site_id
    $stmt = $pdo->prepare("SELECT site_id FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $userRow = $stmt->fetch();
    $siteId = (int)($userRow['site_id'] ?? 1);

    // 2) Get site code
    $stmt = $pdo->prepare("SELECT code FROM sites WHERE id = ?");
    $stmt->execute([$siteId]);
    $site = $stmt->fetch();
    $siteCode = $site['code'] ?? 'GUILD';

    // 3) Default queue: frontdesk
    $stmt = $pdo->prepare("SELECT id FROM queues WHERE site_id = ? AND slug = 'frontdesk' LIMIT 1");
    $stmt->execute([$siteId]);
    $queue = $stmt->fetch();
    $queueId = (int)($queue['id'] ?? 0);

    $ticketType = $data['ticket_type'] ?? 'R';

    // 4) Insert ticket
    $stmt = $pdo->prepare("
        INSERT INTO tickets (user_id, site_id, queue_id, ticket_type, subject, description, priority, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'open')
    ");

    $ok = $stmt->execute([
            $userId,
            $siteId,
            $queueId ?: null,
            $ticketType,
            $data['subject'],
            $data['description'],
            $data['priority'] ?? 'medium'
    ]);

    if (!$ok) {
        return false;
    }

    $ticketId = (int)$pdo->lastInsertId();

    // 5) Generate ticket_number: SITE-TYPE-000123
    $ticketNumber = sprintf('%s-%s-%06d', $siteCode, $ticketType, $ticketId);

    $stmt = $pdo->prepare("UPDATE tickets SET ticket_number = ? WHERE id = ?");
    $stmt->execute([$ticketNumber, $ticketId]);

    return $ticketId;
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
