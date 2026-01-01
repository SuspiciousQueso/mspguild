<?php
/**
 * MSPGuild Header (OpsNerds-style, no Bootstrap)
 *
 * Expect pages to set:
 *  - $pageTitle
 *  - $currentPage
 *  - $isLoggedIn (optional)
 */

// Safe defaults for template vars (keeps IDE happy too)
$pageTitle   = $pageTitle   ?? '';
$currentPage = $currentPage ?? '';
$isLoggedIn  = $isLoggedIn  ?? (class_exists('\MSPGuild\Core\Auth') ? \MSPGuild\Core\Auth::isLoggedIn() : (isset($_SESSION['user_id'])));

// Helpful URL fallbacks (won't fatal if constants aren't defined yet)
$frontdeskUrl = defined('FRONTDESK_URL') ? FRONTDESK_URL : (defined('SITE_URL') ? SITE_URL . '/modules/frontdesk/index.php' : '/modules/frontdesk/index.php');
$resumeUrl    = defined('RESUME_URL') ? RESUME_URL : '#';
$siteName     = defined('SITE_NAME') ? SITE_NAME : 'MSPGuild';
$tagline      = defined('SITE_TAGLINE') ? SITE_TAGLINE : '';
$siteUrl      = defined('SITE_URL') ? SITE_URL : '';
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
    <div class="flex items-center gap-6">
        <a href="<?php echo $siteUrl; ?>/index.php"
           class="text-2xl font-black tracking-tighter text-emerald-400 uppercase">
            <?php echo sanitizeOutput($siteName); ?>
        </a>
        <!-- // Badge logic (shell context)
        if (!$isLoggedIn) { $badgeCmd = 'sh_ auth --login'; } else {

        // Default when logged in $badgeCmd = 'sh_ dashboard --status';
        // Optional: context-aware overrides if ($currentPage === 'frontdesk') { $badgeCmd = 'sh_ frontdesk --tickets';
        } elseif ($currentPage === 'profile') { $badgeCmd = 'sh_ user --profile'; } } ?>
        -->
        <div class="hidden md:flex items-center bg-slate-800 border border-slate-700 rounded px-3 py-1
            text-[10px] font-mono text-slate-400">
            <?php echo sanitizeOutput($badgeCmd); ?>
        </div>

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
            Resume
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
