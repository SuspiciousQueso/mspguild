<?php
/**
 * Authentication Functions
 */

// Removed redundant require_once calls to config files 
// as they are handled by bootstrap.php or the calling script.

/**
 * Start secure session
 */
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        
        // Regenerate session ID periodically for security
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    startSecureSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

/**
 * Require authentication (redirect if not logged in)
 */
function requireAuth() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: ' . SITE_URL . '/login.php');
        exit;
    }
}

/**
 * Get current user data
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT id, email, full_name, company_name, contact_phone, service_tier, created_at, last_login FROM users WHERE id = ? AND is_active = 1");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Authenticate user
 * @param string $email
 * @param string $password
 * @return array|false Returns user data or false
 */
function authenticateUser($email, $password) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT id, email, password_hash, full_name, is_active FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && $user['is_active'] && password_verify($password, $user['password_hash'])) {
        // Update last login
        $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $updateStmt->execute([$user['id']]);
        
        return $user;
    }
    
    return false;
}

/**
 * Register a new user
 * @param array $userData
 * @return int|false ID of new user or false
 */
function registerUser($userData) {
    $pdo = getDbConnection();
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$userData['email']]);
    if ($stmt->fetch()) {
        return false;
    }

    $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (email, password_hash, full_name, company_name, contact_phone, service_tier, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, 1, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $userData['email'],
        $passwordHash,
        $userData['full_name'],
        $userData['company_name'] ?? null,
        $userData['contact_phone'] ?? null,
        $userData['service_tier'] ?? 'basic'
    ]);

    return $result ? $pdo->lastInsertId() : false;
}

/**
 * Update user profile
 * @param int $userId
 * @param array $data
 * @return bool
 */
function updateUserProfile($userId, $data) {
    $pdo = getDbConnection();
    
    $fields = [];
    $params = [];
    
    $allowedFields = ['full_name', 'company_name', 'contact_phone'];
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $fields[] = "$field = ?";
            $params[] = $data[$field];
        }
    }
    
    if (empty($fields)) return false;
    
    $params[] = $userId;
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

/**
 * Log in user
 * @param array $user User data from database
 */
function loginUser($user) {
    startSecureSession();
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['login_time'] = time();
}

/**
 * Log out user
 */
function logoutUser() {
    startSecureSession();
    
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Generate CSRF token
 * @return string
 */
if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken() {
        startSecureSession();
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
if (!function_exists('verifyCsrfToken')) {
    function verifyCsrfToken($token) {
        startSecureSession();
        return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
}

/**
 * Sanitize output to prevent XSS
 * @param string $string
 * @return string
 */
    if (!function_exists('sanitizeOutput')) {
        function sanitizeOutput($data) {
            return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
        }
    }

