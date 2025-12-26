<?php
/**
 * MSPGuild Global Bootstrap
 */

// Load the merged configuration
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

// Start Session AFTER settings are applied in app.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth.php';

/**
 * Any logic that must run on EVERY request goes here
 */