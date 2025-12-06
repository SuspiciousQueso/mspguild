<?php
/**
 * Database Configuration
 * Update these values to match your local/production environment
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'msp_portal');
define('DB_USER', 'root'); // Change for production
define('DB_PASS', ''); // Change for production
define('DB_CHARSET', 'utf8mb4');

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
