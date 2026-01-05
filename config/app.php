<?php
/**
 * MSPGuild Global Configuration
 */
// Define site development state toggle
const DISABLE_DEV_BANNER = false;
// 1. Module Switches
const ENABLE_TICKETING = true;
const ENABLE_CRM = false;
const ENABLE_RMM = true;
const ENABLE_INVOICING = false;

// 2. Site Info (Updated to your production domain)
const SITE_NAME = 'MSPGuild';
const SITE_URL = 'https://mspguild.tech';
const SUPPORT_EMAIL = 'support@mspguild.tech';
const SUPPORT_PHONE = '(555) 123-4567';
const SITE_TAGLINE = 'Where we find the way';

// 3. Module URLs
const KNOWLEDGE_BASE_URL = SITE_URL . '/kb/index.php';
const RESUME_URL = 'https://baldwinit.tech/';

const FRONTDESK_URL = SITE_URL . '/modules/frontdesk/index.php';


// 4. Security Settings
const CSRF_TOKEN_NAME = 'csrf_token';

// 5. Session & Security Settings (Moved from app.php)
// We wrap these in a check to prevent the "Session already active" warning
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 1); // Set to 0 if not using HTTPS yet
}
