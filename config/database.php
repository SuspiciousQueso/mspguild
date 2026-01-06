<?php
/**
 * Database Configuration
 * Update these values to match your local/production environment
 */

define('DB_HOST', getenv('DB_HOST') ?: 'mspguild_db');
define('DB_NAME', getenv('DB_NAME') ?: 'mspguild');
define('DB_USER', getenv('DB_USER') ?: 'mspguild');
define('DB_PASS', getenv('DB_PASS') ?: 'SassyPeopleEatCheese');
define('DB_CHARSET', 'utf8');

// Security settings
// ... existing code ...
// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
// Set cookie secure to false if we are on localhost/HTTP
$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
ini_set('session.cookie_secure', $isSecure ? 1 : 0);
ini_set('session.cookie_samesite', 'Strict');

/**
 * Get database connection using PDO
 * @return PDO Database connection object
 */
function getDbConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please contact support.");
        }
    }
    
    return $pdo;
}
