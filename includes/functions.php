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

/**
 * Register a new user
 */
function registerUser($data) {
    $pdo = Database::getConnection();
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        return false;
    }

    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (email, password, full_name, company_name, contact_phone, service_tier) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $result = $stmt->execute([
            $data['email'],
            $passwordHash,
            $data['full_name'],
            $data['company_name'] ?? null,
            $data['contact_phone'] ?? null,
            $data['service_tier'] ?? 'basic'
        ]);
        return $result ? $pdo->lastInsertId() : false;
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        return false;
    }
}

/**
 * Authenticate a user
 */
function authenticateUser($email, $password) {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}