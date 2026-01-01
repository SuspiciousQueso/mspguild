<?php
/**
 * Global Helper Functions for MSPGuild
 */
use MSPGuild\Core\Database;

/**
 * Sanitize output for safe display in HTML
 *
 * @param string|null $data The string to sanitize
 * @return string The sanitized string
 */
function sanitizeOutput($data) {
    if ($data === null) {
        return '';
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function userEmailExists(string $email): bool
{
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([trim($email)]);
    return (bool)$stmt->fetchColumn();
}


/**
 * Register a new user
 */
function registerUser($data)
{
    $pdo = Database::getConnection();

    $email     = trim((string)($data['email'] ?? ''));
    $password  = (string)($data['password'] ?? '');
    $fullName  = trim((string)($data['full_name'] ?? ''));

    $company   = trim((string)($data['company_name'] ?? ''));
    $phone     = trim((string)($data['contact_phone'] ?? ''));
    $tier      = (string)($data['service_tier'] ?? 'basic');

    if ($email === '' || $password === '' || $fullName === '') {
        return false;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    if (strlen($password) < 8) {
        return false;
    }

    if (userEmailExists($email)) {
        return false;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (email, password_hash, full_name, company_name, contact_phone, service_tier)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $ok = $stmt->execute([
            $email,
            $passwordHash,
            $fullName,
            $company !== '' ? $company : null,
            $phone !== '' ? $phone : null,
            $tier
        ]);

        return $ok ? (int)$pdo->lastInsertId() : false;
    } catch (PDOException $e) {
        // For now: fail quietly. Later: log it.
        return false;
    }
}
// Update profile
function updateUserProfile(int $userId, array $data): bool
{
    $pdo = Database::getConnection();

    $fullName = trim((string)($data['full_name'] ?? ''));
    $company  = trim((string)($data['company_name'] ?? ''));
    $phone    = trim((string)($data['contact_phone'] ?? ''));

    if ($userId <= 0 || $fullName === '') {
        return false;
    }

    // hard limits to match columns
    $fullName = mb_substr($fullName, 0, 255);
    $company  = $company !== '' ? mb_substr($company, 0, 255) : null;
    $phone    = $phone !== '' ? mb_substr($phone, 0, 50) : null;

    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, company_name = ?, contact_phone = ? WHERE id = ? LIMIT 1");

    try {
        return $stmt->execute([$fullName, $company, $phone, $userId]);
    } catch (PDOException $e) {
        return false;
    }
}
