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
function createTicket($userId, $data) {
    $pdo = Database::getConnection();
    $sql = "INSERT INTO tickets (user_id, subject, description, priority, status) VALUES (?, ?, ?, ?, 'open')";
    $stmt = $pdo->prepare($sql);

    $result = $stmt->execute([
        $userId,
        $data['subject'],
        $data['description'],
        $data['priority'] ?? 'medium'
    ]);

    return $result ? $pdo->lastInsertId() : false;
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
