<?php
require_once __DIR__ . '/../includes/bootstrap.php';

// Page metadata MUST be set before header include
$pageTitle    = 'Dashboard';
$currentPage  = 'dashboard';

// Keep naming consistent with header.php
$isLoggedIn = (class_exists('\MSPGuild\Core\Auth'))
        ? \MSPGuild\Core\Auth::isLoggedIn()
        : isset($_SESSION['user_id']);

// Tenant/site (fallback to default)
$site_code = $_SESSION['site_code'] ?? 'GUILD';

// Placeholder stats (wire later)
$stats = $stats ?? [
        'open_tickets' => 0,
        'my_queues'    => 0,
        'sites'        => 1,
];

require_once __DIR__ . '/../includes/header.php';
?>

<div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
    <div class="py-10">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-100">Dashboard</h1>
                <p class="mt-1 text-sm text-zinc-400">Status, quick actions, and what needs attention.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="/modules/frontdesk/create.php"
                   class="inline-flex items-center rounded-lg bg-zinc-100 px-3 py-2 text-sm font-medium text-zinc-900 hover:bg-white">
                    + New Ticket
                </a>
                <a href="/modules/frontdesk/"
                   class="inline-flex items-center rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 hover:border-zinc-700">
                    FrontDesk
                </a>
            </div>
        </div>

        <div class="mb-6 flex flex-wrap items-center gap-2">
            <?php if (!$isLoggedIn): ?>
                <span class="rounded-full border border-zinc-800 bg-zinc-950 px-3 py-1 text-xs text-zinc-300">sh_ auth --login</span>
            <?php else: ?>
                <span class="rounded-full border border-zinc-800 bg-zinc-950 px-3 py-1 text-xs text-zinc-300">sh_ dashboard --status</span>
                <span class="text-xs text-zinc-500">•</span>
                <span class="text-xs text-zinc-500">
                    Tenant: <span class="text-zinc-300"><?= htmlspecialchars($site_code) ?></span>
                </span>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-5">
                <div class="text-sm text-zinc-400">Open tickets</div>
                <div class="mt-2 text-3xl font-semibold text-zinc-100"><?= (int)$stats['open_tickets'] ?></div>
            </div>

            <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-5">
                <div class="text-sm text-zinc-400">Queues I can access</div>
                <div class="mt-2 text-3xl font-semibold text-zinc-100"><?= (int)$stats['my_queues'] ?></div>
            </div>

            <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-5">
                <div class="text-sm text-zinc-400">Sites</div>
                <div class="mt-2 text-3xl font-semibold text-zinc-100"><?= (int)$stats['sites'] ?></div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-medium text-zinc-200">Quick actions</h2>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="/modules/frontdesk/create.php"
                       class="rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 hover:border-zinc-700">
                        Create ticket
                    </a>
                    <a href="/modules/frontdesk/"
                       class="rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 hover:border-zinc-700">
                        View tickets
                    </a>
                    <a href="/user_profile_update.php"
                       class="rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 hover:border-zinc-700">
                        Profile
                    </a>
                </div>
            </div>

            <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-5">
                <h2 class="text-sm font-medium text-zinc-200">Activity</h2>
                <p class="mt-2 text-sm text-zinc-400">
                    Placeholder for “recent tickets”, “recent replies”, etc. We’ll wire this after the FrontDesk list is converted.
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
