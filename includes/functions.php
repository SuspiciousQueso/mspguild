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

    // ... email check ...

    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

    // Fixed: Changed 'password' column to 'password_hash' to match schema.sql
    $sql = "INSERT INTO users (email, password_hash, full_name, company_name, contact_phone, service_tier) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $result = $stmt->execute([
            $data['email'],
            $passwordHash,
            $data['full_name'],
            $data['company_name'] ?? null,
            $data['contact_phone'] ?? null,
            $data['service_tier'] ?? 'Basic'
        ]);
        return $result ? $pdo->lastInsertId() : false;
    } catch (PDOException $e) {
// ... existing code ...
        /**
         * Authenticate a user
         */
        function authenticateUser($email, $password) {
            $pdo = Database::getConnection();
            // Fixed: Changed column name to 'password_hash'
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                return $user;
            }
            return false;
        }