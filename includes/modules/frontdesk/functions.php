<?php
/**
 * MSPGuild: Ticketing Module Logic
 */
require_once __DIR__ . '/../../bootstrap.php';

use MSPGuild\Core\Database;

/**
 * Register a new user
 * @param int $userId
 * @return array
 */
function getUserTickets($userId) {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Get a single ticket by ID (and verify ownership)
 * @param int $ticketId
 * @param int $userId
 * @return array|false
 */
function getTicketById($ticketId, $userId) {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? AND user_id = ?");
    $stmt->execute([$ticketId, $userId]);
    return $stmt->fetch();
}

/**
 * Create a new support ticket
 */
function createTicket(int $userId, array $data)
{
    $pdo = \MSPGuild\Core\Database::getConnection();

    // 1) Determine site_id for the user (default to 1 if missing)
    $stmt = $pdo->prepare("SELECT site_id FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $userRow = $stmt->fetch();
    $siteId = (int)($userRow['site_id'] ?? 1);

    // 2) Get site code (default to GUILD)
    $stmt = $pdo->prepare("SELECT code FROM sites WHERE id = ?");
    $stmt->execute([$siteId]);
    $siteRow = $stmt->fetch();
    $siteCode = $siteRow['code'] ?? 'GUILD';

    // 3) Get default FrontDesk queue for the site (slug=frontdesk)
    $stmt = $pdo->prepare("SELECT id FROM queues WHERE site_id = ? AND slug = 'frontdesk' LIMIT 1");
    $stmt->execute([$siteId]);
    $queueRow = $stmt->fetch();
    $queueId = isset($queueRow['id']) ? (int)$queueRow['id'] : null;

    // 4) Validate ticket type
    $ticketType = $data['ticket_type'] ?? 'R';
    if (!in_array($ticketType, ['R', 'I'], true)) {
        $ticketType = 'R';
    }

    // 5) Insert the ticket (no ticket_number yet)
    $sql = "
        INSERT INTO tickets (user_id, site_id, queue_id, ticket_type, subject, description, priority, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'open')
    ";
    $stmt = $pdo->prepare($sql);

    $ok = $stmt->execute([
        $userId,
        $siteId,
        $queueId, // can be null
        $ticketType,
        $data['subject'],
        $data['description'],
        $data['priority'] ?? 'medium'
    ]);

    if (!$ok) {
        return false;
    }

    $ticketId = (int)$pdo->lastInsertId();

    // 6) Generate ticket_number: SITE-TYPE-000123
    $ticketNumber = sprintf('%s-%s-%06d', $siteCode, $ticketType, $ticketId);

    $stmt = $pdo->prepare("UPDATE tickets SET ticket_number = ? WHERE id = ?");
    $stmt->execute([$ticketNumber, $ticketId]);

    return $ticketId;
}

function getTicketMessages($ticketId) {
    $pdo = Database::getConnection();
    $sql = "SELECT tm.*, u.full_name
            FROM ticket_messages tm
            JOIN users u ON tm.user_id = u.id
            WHERE tm.ticket_id = ?
            ORDER BY tm.created_at ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ticketId]);
    return $stmt->fetchAll();
}

function addTicketMessage($ticketId, $userId, $body, $visibility = 'public') {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("INSERT INTO ticket_messages (ticket_id, user_id, body, visibility) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$ticketId, $userId, $body, $visibility]);
}

function logTicketEvent($ticketId, $eventType, $actorUserId = null, $meta = null) {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("INSERT INTO ticket_events (ticket_id, actor_user_id, event_type, meta) VALUES (?, ?, ?, ?)");
    $metaJson = $meta ? json_encode($meta) : null;
    return $stmt->execute([$ticketId, $actorUserId, $eventType, $metaJson]);
}

/**
 * Helper to get CSS classes for status badges
 */
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'open': return 'bg-primary';
        case 'in-progress': return 'bg-info text-dark';
        case 'waiting-on-client': return 'bg-warning text-dark';
        case 'closed': return 'bg-secondary';
        default: return 'bg-light text-dark';
    }
    return false;
}
