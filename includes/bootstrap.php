<?php
/**
 * MSPGuild Global Bootstrap
 * This file initializes the application environment.
 */

// 1. Load Configuration & Constants
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

// 2. Start Session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Global Helpers (could also be moved to a helpers.php later)
require_once __DIR__ . '/auth.php';

/**
 * Any logic that must run on EVERY request goes here
 */