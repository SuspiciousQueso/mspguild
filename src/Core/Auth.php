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
            self::startSecureSession();
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? (SITE_URL . '/dashboard.php');
            header('Location: ' . SITE_URL . '/login.php');
            exit;
        }
    }

    public static function getCurrentUser() {
        if (!self::isLoggedIn()) return null;

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id, email, full_name, company_name, contact_phone, service_tier, created_at, is_admin, enabled_modules
                               FROM users
                               WHERE id = ? AND is_active = 1");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }

    public static function loginUser($user) {
        self::startSecureSession();
        session_regenerate_id(true);

        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name']  = $user['full_name'];
        $_SESSION['login_time'] = time();
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
        return isset($_SESSION[$tokenName]) && is_string($token) && hash_equals($_SESSION[$tokenName], $token);
    }

    public static function authenticate($email, $password)
    {
        $email = trim((string)$email);
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify((string)$password, $user['password_hash'])) {
            return $user;
        }

        return false;
    }

    /**
     * Single canonical logout method.
     * (Keep this as the one true logout to avoid drift.)
     */
    public static function logout()
    {
        self::startSecureSession();

        // Unset all session variables
        $_SESSION = [];

        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"] ?? '/',
                $params["domain"] ?? '',
                $params["secure"] ?? false,
                $params["httponly"] ?? true
            );
        }

        // Regenerate to prevent fixation (safe even if suppressed)
        @session_regenerate_id(true);

        // Destroy session
        session_destroy();
    }

    /**
     * Back-compat wrapper (optional).
     * If anything still calls logoutUser(), it will still work.
     */
    public static function logoutUser() {
        self::logout();
    }
}
