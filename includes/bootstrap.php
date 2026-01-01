<?php
/**
 * MSPGuild Global Bootstrap
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Load the merged configuration
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

// Use Composer autoloader if available, otherwise fallback to manual requires
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    // Fallback for core classes if composer hasn't been run yet
    require_once __DIR__ . '/../src/Core/Database.php';
    require_once __DIR__ . '/../src/Core/Auth.php';
    require_once __DIR__ . '/../src/Core/Session.php';
}
// Load helper functions
require_once __DIR__ . '/functions.php';

use MSPGuild\Core\Auth;

// Start Session using the secure function defined in Auth.php
Auth::startSecureSession();