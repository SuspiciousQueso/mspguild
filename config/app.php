<?php
/**
 * MSPGuild Module Configuration
 * Toggle modules on/off system-wide.
 */

// Module Switches
define('ENABLE_TICKETING', true);
define('ENABLE_KNOWLEDGEBASE', false);
define('ENABLE_CRM', false);
define('ENABLE_RMM', false);
define('ENABLE_INVOICING', false);

// Site Info
define('SITE_URL', 'https://mspguild.tech');
define('SUPPORT_EMAIL', 'support@mspguild.tech');
define('SUPPORT_PHONE', '(555) 123-4567');

// Module URLs
define('TICKET_SYSTEM_URL', SITE_URL . '/ticketing/index.php');
define('KNOWLEDGE_BASE_URL', SITE_URL . '/kb/index.php');
define('RESUME_URL', 'https://your-resume-link.com'); // Update this!

// Security & Environment