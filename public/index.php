<?php
require_once __DIR__ . '/../includes/bootstrap.php';

use MSPGuild\Core\Auth;

$pageTitle   = "Home";
$currentPage = 'index';
$isLoggedIn  = Auth::isLoggedIn();

$frontdeskUrl = defined('FRONTDESK_URL') ? FRONTDESK_URL : (defined('SITE_URL') ? SITE_URL . '/modules/frontdesk/index.php' : '/modules/frontdesk/index.php');
$siteUrl      = defined('SITE_URL') ? SITE_URL : '';

include __DIR__ . '/../includes/header.php';
?>

<section class="px-6 py-10">
    <div class="max-w-6xl mx-auto">

        <!-- Hero -->
        <div class="border border-zinc-800 bg-zinc-950/40 rounded-2xl p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

                <div class="space-y-4">
                    <div class="text-[11px] font-mono text-zinc-500">
                        <span class="text-emerald-400">status:</span>
                        <span><?= sanitizeOutput($isLoggedIn ? 'authenticated' : 'guest'); ?></span>
                        <span class="mx-2 text-zinc-700">|</span>
                        <span class="text-emerald-400">module:</span>
                        <span>guild</span>
                    </div>

                    <h1 class="text-3xl md:text-4xl font-black tracking-tight text-zinc-100">
                        MSPGuild
                        <span class="text-emerald-400">Command Center</span>
                    </h1>

                    <p class="text-zinc-400 max-w-2xl">
                        Modular MSP framework. Minimal UI. Real workflows.
                        <span class="text-zinc-500 font-mono">auth → frontdesk → ops</span>
                    </p>

                    <div class="flex flex-wrap gap-3 pt-2">
                        <?php if ($isLoggedIn): ?>
                            <a href="<?= $siteUrl; ?>/dashboard.php"
                               class="bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-black uppercase px-5 py-3 rounded-lg transition">
                                Enter Dashboard
                            </a>

                            <a href="<?= htmlspecialchars($frontdeskUrl); ?>"
                               class="border border-zinc-700 hover:border-zinc-500 text-zinc-200 text-[11px] font-black uppercase px-5 py-3 rounded-lg transition">
                                Open FrontDesk
                            </a>
                        <?php else: ?>
                            <a href="<?= $siteUrl; ?>/login.php"
                               class="bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-black uppercase px-5 py-3 rounded-lg transition">
                                Login
                            </a>

                            <a href="<?= $siteUrl; ?>/user_registration.php"
                               class="border border-zinc-700 hover:border-zinc-500 text-zinc-200 text-[11px] font-black uppercase px-5 py-3 rounded-lg transition">
                                Join the Guild
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right-side "terminal" panel -->
                <div class="w-full lg:w-[420px] border border-zinc-800 rounded-2xl bg-zinc-900/40">
                    <div class="flex items-center justify-between px-4 py-2 border-b border-zinc-800">
                        <div class="text-[10px] font-mono text-zinc-500">guild://overview</div>
                        <div class="flex gap-1">
                            <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                            <span class="w-2.5 h-2.5 rounded-full bg-zinc-700"></span>
                        </div>
                    </div>
                    <div class="p-4 space-y-2 text-[12px] font-mono">
                        <div class="text-zinc-400">
                            <span class="text-emerald-400">$</span> whoami
                        </div>
                        <div class="text-zinc-300">
                            <?= sanitizeOutput($isLoggedIn ? 'member' : 'guest'); ?>
                        </div>

                        <div class="pt-2 text-zinc-400">
                            <span class="text-emerald-400">$</span> modules --list
                        </div>
                        <div class="text-zinc-300">
                            <span class="text-emerald-400">•</span> FrontDesk
                            <span class="text-zinc-600">/ tickets + routing</span>
                        </div>
                        <div class="text-zinc-500">
                            <span class="text-zinc-600">•</span> RackRoom <span class="text-zinc-600">(planned)</span>
                        </div>
                        <div class="text-zinc-500">
                            <span class="text-zinc-600">•</span> PatchDay <span class="text-zinc-600">(planned)</span>
                        </div>
                        <div class="text-zinc-500">
                            <span class="text-zinc-600">•</span> NightWatch <span class="text-zinc-600">(planned)</span>
                        </div>
                        <div class="text-zinc-500">
                            <span class="text-zinc-600">•</span> Baseline <span class="text-zinc-600">(planned)</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Three panels -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-6">
            <div class="border border-zinc-800 bg-zinc-950/30 rounded-2xl p-5">
                <div class="text-[10px] font-mono text-zinc-500 mb-2">frontdesk://intake</div>
                <div class="text-zinc-100 font-black tracking-tight">Ticket Intake</div>
                <p class="text-sm text-zinc-400 mt-2">
                    Create <span class="font-mono text-zinc-300">R</span>equests and <span class="font-mono text-zinc-300">T</span>roubles.
                    Track work without chaos.
                </p>
                <div class="mt-4">
                    <a class="text-[11px] uppercase font-black text-emerald-400 hover:text-emerald-300"
                       href="<?= htmlspecialchars($frontdeskUrl); ?>">
                        open frontdesk →
                    </a>
                </div>
            </div>

            <div class="border border-zinc-800 bg-zinc-950/30 rounded-2xl p-5">
                <div class="text-[10px] font-mono text-zinc-500 mb-2">guild://access</div>
                <div class="text-zinc-100 font-black tracking-tight">Access Control</div>
                <p class="text-sm text-zinc-400 mt-2">
                    Site + queue foundations are in place.
                    Roles and routing expand cleanly from here.
                </p>
                <div class="mt-4 text-[11px] font-mono text-zinc-500">
                    next: <span class="text-zinc-300">user_queue_access</span>, <span class="text-zinc-300">staff views</span>
                </div>
            </div>

            <div class="border border-zinc-800 bg-zinc-950/30 rounded-2xl p-5">
                <div class="text-[10px] font-mono text-zinc-500 mb-2">ops://roadmap</div>
                <div class="text-zinc-100 font-black tracking-tight">Planned Modules</div>
                <ul class="text-sm text-zinc-400 mt-2 space-y-1">
                    <li><span class="text-zinc-300 font-mono">RackRoom</span> — installs, racks, wiring</li>
                    <li><span class="text-zinc-300 font-mono">PatchDay</span> — patching, lifecycle</li>
                    <li><span class="text-zinc-300 font-mono">NightWatch</span> — incidents, on-call</li>
                    <li><span class="text-zinc-300 font-mono">Baseline</span> — audits, docs, cleanup</li>
                </ul>
            </div>
        </div>

        <!-- Command footer strip -->
        <div class="mt-6 border border-zinc-800 bg-zinc-950/30 rounded-2xl p-4 text-[12px] font-mono text-zinc-400">
            <span class="text-emerald-400">$</span>
            <?php if ($isLoggedIn): ?>
                cd frontdesk && tickets --open
            <?php else: ?>
                login --email you@company.com
            <?php endif; ?>
        </div>

    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
