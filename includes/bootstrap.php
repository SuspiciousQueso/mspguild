<?php
/**
 * MSPGuild Global Bootstrap
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load the merged configuration
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

// Load Auth
require_once __DIR__ . '/Auth.php';

// Start Session using the secure function defined in Auth.php
if (function_exists('startSecureSession')) {
    startSecureSession();
} elseif (session_status() === PHP_SESSION_NONE) {
    session_start();
}