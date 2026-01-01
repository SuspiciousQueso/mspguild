<?php
$siteName = defined('SITE_NAME') ? SITE_NAME : 'MSPGuild';
?>
</main>

<footer class="shrink-0 border-t border-slate-800 bg-slate-900 px-6 py-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
        <div class="text-[11px] text-slate-500 font-mono">
            &copy; <?php echo date('Y'); ?> <?php echo sanitizeOutput($siteName); ?> — infrastructure ready
        </div>

        <div class="text-[11px] text-slate-500 font-mono flex gap-4">
            <a class="hover:text-white" href="<?php echo SITE_URL; ?>/index.php">home</a>
            <a class="hover:text-white" href="<?php echo SITE_URL; ?>/modules/frontdesk/index.php">frontdesk</a>
            <a class="hover:text-white" href="<?php echo SITE_URL; ?>/contact.php">contact</a>
        </div>
    </div>
</footer>

</body>
</html>
