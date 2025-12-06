<?php
/**
 * Application Configuration
 */

// Site settings
define('SITE_NAME', 'MSPGuild');
define('SITE_TAGLINE', 'OnpenSource AiO MSP Client Support and Management Portal');
define('SITE_URL', 'http://https://baldwinit.tech'); // Update for production
define('SUPPORT_EMAIL', 'support@mspguild.com');
define('SUPPORT_PHONE', '(555) 123-4567');

// Security settings
define('SESSION_LIFETIME', 3600); // 1 hour in seconds
define('CSRF_TOKEN_NAME', 'csrf_token');

// Password requirements
define('MIN_PASSWORD_LENGTH', 8);

// Service tiers
define('SERVICE_TIERS', [
    'Basic' => 'Basic Support Package',
    'Professional' => 'Professional Support Package',
    'Enterprise' => 'Enterprise Support Package',
    'Custom' => 'Custom Solutions'
]);

// External links (update these with your actual links)
define('TICKET_SYSTEM_URL', '/dashboard?action=tickets'); // Placeholder for future ticketing system
define('RESUME_URL', '/assets/resume.pdf'); // Add your resume PDF here
define('KNOWLEDGE_BASE_URL', '/dashboard?section=kb'); // Placeholder for knowledge base

// Timezone
date_default_timezone_set('America/Los_Angeles'); // Update to your timezone

// Error reporting (disable in production)
//if ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1') {
//    error_reporting(E_ALL);
//    ini_set('display_errors', 1);
//} else {
//    error_reporting(0);
//   ini_set('display_errors', 0);
//}

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 when using HTTPS
ini_set('session.cookie_samesite', 'Strict');
