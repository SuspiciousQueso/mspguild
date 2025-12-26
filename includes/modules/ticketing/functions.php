<?php
/**
 * MSPGuild: Ticketing Module Logic
 */
require_once __DIR__ . '/../../bootstrap.php';

/**
 * Get all tickets for a specific user
 * @param int $userId
 * @return array
 */
function getUserTickets($userId) {
    $pdo = getDbConnection();
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
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? AND user_id = ?");
    $stmt->execute([$ticketId, $userId]);
    return $stmt->fetch();
}

/**
 * Create a new support ticket
 */
function createTicket($userId, $data) {
    $pdo = getDbConnection();
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

/**
 * Get comments for a specific ticket
 */
function getTicketComments($ticketId) {
    $pdo = getDbConnection();
    $sql = "SELECT tc.*, u.full_name 
            FROM ticket_comments tc 
            JOIN users u ON tc.user_id = u.id 
            WHERE tc.ticket_id = ? 
            ORDER BY tc.created_at ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$ticketId]);
    return $stmt->fetchAll();
}

/**
 * Add a comment to a ticket
 */
function addTicketComment($ticketId, $userId, $comment) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment) VALUES (?, ?, ?)");
    return $stmt->execute([$ticketId, $userId, $comment]);
}

/**
 * Helper to get CSS classes for status badges
 */
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'open': return 'badge-primary';
        case 'in-progress': return 'badge-info';
        case 'waiting-on-client': return 'badge-warning';
        case 'closed': return 'badge-secondary';
        default: return 'badge-light';
    }
}
