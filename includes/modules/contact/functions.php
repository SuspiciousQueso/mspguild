<?php

use MSPGuild\Core\Database;

/**
 * Save a contact message.
 * Returns inserted id on success, false on failure.
 */
function saveContactMessage(array $data): int|false
{
    $pdo = Database::getConnection();

    $name    = trim((string)($data['name'] ?? ''));
    $email   = trim((string)($data['email'] ?? ''));
    $company = trim((string)($data['company'] ?? ''));
    $phone   = trim((string)($data['phone'] ?? ''));
    $message = trim((string)($data['message'] ?? ''));

    if ($name === '' || $email === '' || $message === '') return false;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;

    // hard limits (match form)
    $name    = mb_substr($name, 0, 255);
    $email   = mb_substr($email, 0, 255);
    $company = $company !== '' ? mb_substr($company, 0, 255) : null;
    $phone   = $phone !== '' ? mb_substr($phone, 0, 50) : null;
    $message = mb_substr($message, 0, 5000);

    $siteId = isset($data['site_id']) && $data['site_id'] !== '' ? (int)$data['site_id'] : null;
    $userId = isset($data['user_id']) && $data['user_id'] !== '' ? (int)$data['user_id'] : null;

    $ip = $data['ip_address'] ?? null;
    $ip = $ip ? mb_substr((string)$ip, 0, 45) : null;

    $ua = $data['user_agent'] ?? null;
    $ua = $ua ? mb_substr((string)$ua, 0, 255) : null;

    $sql = "INSERT INTO contact_messages
            (site_id, user_id, name, email, company, phone, message, ip_address, user_agent)
            VALUES
            (:site_id, :user_id, :name, :email, :company, :phone, :message, :ip, :ua)";

    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        ':site_id' => $siteId,
        ':user_id' => $userId,
        ':name'    => $name,
        ':email'   => $email,
        ':company' => $company,
        ':phone'   => $phone,
        ':message' => $message,
        ':ip'      => $ip,
        ':ua'      => $ua,
    ]);

    if (!$ok) return false;
    return (int)$pdo->lastInsertId();
}
