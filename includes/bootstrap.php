<?php
/**
 * MSPGuild Global Bootstrap
 */

// Load the merged configuration
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

// Load Auth AFTER settings are applied so constants like CSRF_TOKEN_NAME exist
require_once __DIR__ . '/auth.php';

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Any logic that must run on EVERY request goes here
 */