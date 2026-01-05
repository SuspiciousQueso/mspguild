<?php
/**
 * MSPGuild Header (OpsNerds-style, no Bootstrap)
 *
 * Expect pages to set:
 *  - $pageTitle
 *  - $currentPage
 *  - $isLoggedIn (optional)
 */

// Safe defaults for template vars
$pageTitle   = $pageTitle   ?? '';
$currentPage = $currentPage ?? '';
$currentPage = strtolower(trim((string)$currentPage));

$isLoggedIn  = $isLoggedIn  ?? (class_exists('\MSPGuild\Core\Auth')
        ? \MSPGuild\Core\Auth::isLoggedIn()
        : (isset($_SESSION['user_id'])));

// Helpful URL fallbacks
$frontdeskUrl = defined('FRONTDESK_URL') ? FRONTDESK_URL : (defined('SITE_URL') ? SITE_URL . '/modules/frontdesk/index.php' : '/modules/frontdesk/index.php');
$resumeUrl    = defined('RESUME_URL') ? RESUME_URL : '#';
$siteName     = defined('SITE_NAME') ? SITE_NAME : 'MSPGuild';
$tagline      = defined('SITE_TAGLINE') ? SITE_TAGLINE : '';
$siteUrl      = defined('SITE_URL') ? SITE_URL : '';

// Badge logic (shell context)
if (!$isLoggedIn) {
    // logged out contexts
    $badgeCmd = match ($currentPage) {
        'register' => 'sh_ auth --register',
        'contact'  => 'sh_ contact --send',
        default    => 'sh_ auth --login',
    };
} else {
    // logged in contexts
    $badgeCmd = match ($currentPage) {
        'frontdesk' => 'sh_ frontdesk --tickets',
        'dashboard' => 'sh_ dashboard --status',
        'profile'   => 'sh_ user --profile',
        'contact'   => 'sh_ contact --send',
        default     => 'sh_ dashboard --status',
    };
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind (CDN like OpsNerds) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <title><?php echo ($pageTitle ? sanitizeOutput($pageTitle) . ' - ' : ''); ?><?php echo sanitizeOutput($siteName); ?></title>
    <meta name="description" content="<?php echo sanitizeOutput($tagline); ?>">
</head>

<body class="h-full bg-slate-900 text-slate-200 flex flex-col m-0 p-0 overflow-hidden">

<nav class="w-full bg-slate-900 px-6 py-3 flex justify-between items-center z-50 border-b border-slate-800 shrink-0">
    <?php if (!defined('DISABLE_DEV_BANNER')): ?>
        <div class="w-full bg-slate-900 border-b border-slate-800 text-slate-300 text-xs md:text-sm">
            <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
            <span class="text-amber-400 font-semibold uppercase tracking-wide">
                Dev Notice
            </span>
                    <span class="hidden sm:inline">
                This site is under active development. Some features may be incomplete or reset.
            </span>
                    <span class="sm:hidden">
                Active development in progress.
            </span>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <!-- Left -->
    <div class="flex items-center gap-6">
        <a href="<?php echo $siteUrl; ?>/index.php"
           class="text-2xl font-black tracking-tighter text-emerald-400 uppercase">
            <?php echo sanitizeOutput($siteName); ?>
        </a>

        <div class="hidden md:flex items-center bg-slate-800 border border-slate-700 rounded px-3 py-1
            text-[10px] font-mono text-slate-400">
            <?php echo sanitizeOutput($badgeCmd); ?>
        </div>
    </div>

    <!-- Right -->
    <div class="flex gap-6 items-center">
        <a href="<?php echo $siteUrl; ?>/index.php"
           class="text-xs font-bold uppercase tracking-widest <?php echo $currentPage === 'index' ? 'text-white' : 'text-slate-400 hover:text-white'; ?>">
            Home
        </a>

        <a href="<?php echo $frontdeskUrl; ?>"
           class="text-xs font-bold uppercase tracking-widest <?php echo $currentPage === 'frontdesk' ? 'text-white' : 'text-slate-400 hover:text-white'; ?>">
            FrontDesk
        </a>

        <a href="<?php echo $siteUrl; ?>/contact.php"
           class="text-xs font-bold uppercase tracking-widest <?php echo $currentPage === 'contact' ? 'text-white' : 'text-slate-400 hover:text-white'; ?>">
            Contact
        </a>

        <a href="<?php echo $resumeUrl; ?>" target="_blank"
           class="text-xs font-bold uppercase tracking-widest text-slate-400 hover:text-white">
            Support Services
        </a>

        <div class="w-px h-5 bg-slate-800"></div>

        <?php if ($isLoggedIn): ?>
            <a href="<?php echo $siteUrl; ?>/dashboard.php"
               class="bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black uppercase px-4 py-2 rounded transition">
                Dashboard
            </a>

            <a href="<?php echo $siteUrl; ?>/user_profile_update.php"
               class="text-[10px] text-slate-400 hover:text-white transition uppercase font-bold">
                Profile
            </a>

            <a href="<?php echo $siteUrl; ?>/logout.php"
               class="text-[10px] text-slate-500 hover:text-white transition uppercase font-bold">
                Logout
            </a>
        <?php else: ?>
            <a href="<?php echo $siteUrl; ?>/login.php"
               class="text-xs font-bold hover:text-emerald-400 transition uppercase tracking-widest">
                Login
            </a>

            <a href="<?php echo $siteUrl; ?>/user_registration.php"
               class="bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black uppercase px-4 py-2 rounded transition">
                Join Guild
            </a>
        <?php endif; ?>
    </div>
</nav>

<main class="flex-1 overflow-auto">
