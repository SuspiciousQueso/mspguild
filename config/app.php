<?php
/**
 * MSPGuild Global Configuration
 */

// 1. Module Switches
define('ENABLE_TICKETING', true);
define('ENABLE_KNOWLEDGEBASE', false);
define('ENABLE_CRM', false);
define('ENABLE_RMM', false);
define('ENABLE_INVOICING', false);

// 2. Site Info (Updated to your production domain)
define('SITE_URL', 'https://mspguild.tech');
define('SUPPORT_EMAIL', 'support@mspguild.tech');
define('SUPPORT_PHONE', '(555) 123-4567');

// 3. Module URLs
define('TICKET_SYSTEM_URL', SITE_URL . '/ticketing/index.php');
define('KNOWLEDGE_BASE_URL', SITE_URL . '/kb/index.php');

// 4. Session & Security Settings (Moved from app.php)
// We wrap these in a check to prevent the "Session already active" warning
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 1); // Set to 0 if not using HTTPS yet
}

/**
 * Global Helper Functions
 * Wrapped in if(!function_exists) to prevent redeclaration errors
 */

if (!function_exists('sanitizeOutput')) {
    function sanitizeOutput($data) {
        return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}