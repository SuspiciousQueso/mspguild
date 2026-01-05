<?php
/**
 * Mail Configuration
 *
 * Default driver is PHP's built-in mail(). For SMTP, install PHPMailer and set env vars.
 *
 * Env vars supported:
 *  - MAIL_DRIVER=mail|smtp
 *  - MAIL_FROM_EMAIL, MAIL_FROM_NAME
 *  - SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, SMTP_SECURE (tls|ssl|''), SMTP_AUTH (true/false)
 */

define('MAIL_DRIVER', getenv('MAIL_DRIVER') ?: 'mail');

define('MAIL_FROM_EMAIL', getenv('MAIL_FROM_EMAIL') ?: (defined('SUPPORT_EMAIL') ? SUPPORT_EMAIL : 'support@example.com'));
define('MAIL_FROM_NAME',  getenv('MAIL_FROM_NAME')  ?: (defined('SITE_NAME') ? SITE_NAME : 'MSPGuild'));

// SMTP settings (only used when MAIL_DRIVER=smtp and PHPMailer is available)
define('SMTP_HOST',   getenv('SMTP_HOST')   ?: '');
define('SMTP_PORT',   (int)(getenv('SMTP_PORT') ?: 587));
define('SMTP_USER',   getenv('SMTP_USER')   ?: '');
define('SMTP_PASS',   getenv('SMTP_PASS')   ?: '');
define('SMTP_SECURE', getenv('SMTP_SECURE') ?: 'tls'); // tls|ssl|''

// Some hosts want auth disabled for local relay
$auth = getenv('SMTP_AUTH');
define('SMTP_AUTH', $auth === null ? true : filter_var($auth, FILTER_VALIDATE_BOOLEAN));
