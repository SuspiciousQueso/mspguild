<?php
namespace MSPGuild\Core;

/**
 * Auth Class - Handles all security and user state
 */
class Auth {
    
    public static function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            
            if (!isset($_SESSION['created'])) {
                $_SESSION['created'] = time();
            } else if (time() - $_SESSION['created'] > 1800) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }

    public static function isLoggedIn() {
        self::startSecureSession();
        return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
    }

    public static function requireAuth() {
        if (!self::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . SITE_URL . '/login.php');
            exit;
        }
    }

    public static function getCurrentUser() {
        if (!self::isLoggedIn()) return null;

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id, email, full_name, company_name, contact_phone, service_tier, created_at, is_admin, enabled_modules FROM users WHERE id = ? AND is_active = 1");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }

    public static function loginUser($user) {
        self::startSecureSession();
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['login_time'] = time();
    }

    public static function logoutUser() {
        self::startSecureSession();
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
    }

    public static function generateCsrfToken() {
        self::startSecureSession();
        $tokenName = defined('CSRF_TOKEN_NAME') ? CSRF_TOKEN_NAME : 'csrf_token';
        if (!isset($_SESSION[$tokenName])) {
            $_SESSION[$tokenName] = bin2hex(random_bytes(32));
        }
        return $_SESSION[$tokenName];
    }

    public static function verifyCsrfToken($token) {
        self::startSecureSession();
        $tokenName = defined('CSRF_TOKEN_NAME') ? CSRF_TOKEN_NAME : 'csrf_token';
        return isset($_SESSION[$tokenName]) && hash_equals($_SESSION[$tokenName], $token);
    }
}

