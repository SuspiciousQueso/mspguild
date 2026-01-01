<?php
require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once __DIR__ . '/../../../includes/modules/frontdesk/functions.php';

use MSPGuild\Core\Auth;

// Require login
Auth::requireAuth();

$user    = Auth::getCurrentUser();
$tickets = getUserTickets((int)$user['id']);

$pageTitle   = 'FrontDesk';
$currentPage = 'frontdesk';
$isLoggedIn  = true;

require_once __DIR__ . '/../../../includes/header.php';

// Helpers (local to this page)
function prettyDate(?string $ts): string {
    if (!$ts) return '';
    $t = strtotime($ts);
    if (!$t) return '';
    return date('M j, Y', $t);
}

function statusPillClasses(string $status): string {
    $s = strtolower(trim($status));
    return match ($s) {
        'open'       => 'border-emerald-900/50 bg-emerald-950/40 text-emerald-200',
        'closed'     => 'border-zinc-800 bg-zinc-950 text-zinc-300',
        'pending'    => 'border-amber-900/50 bg-amber-950/40 text-amber-200',
        'resolved'   => 'border-sky-900/50 bg-sky-950/40 text-sky-200',
        default      => 'border-zinc-800 bg-zinc-950 text-zinc-300',
    };
}

function priorityPillClasses(string $priority): string {
    $p = strtolower(trim($priority));
    return match ($p) {
        'low'       => 'border-zinc-800 bg-zinc-950 text-zinc-300',
        'medium'    => 'border-sky-900/50 bg-sky-950/40 text-sky-200',
        'high'      => 'border-amber-900/50 bg-amber-950/40 text-amber-200',
        'emergency' => 'border-red-900/50 bg-red-950/40 text-red-200',
        default     => 'border-zinc-800 bg-zinc-950 text-zinc-300',
    };
}
?>

<div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
    <div class="py-10">

        <!-- Page header -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-100">FrontDesk</h1>
                <p class="mt-1 text-sm text-zinc-400">Your support tickets. Track requests and incidents.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="create.php"
                   class="inline-flex items-center rounded-lg bg-zinc-100 px-3 py-2 text-sm font-medium text-zinc-900 hover:bg-white">
                    + Open New Ticket
                </a>
            </div>
        </div>

        <!-- Shell badge -->
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="rounded-full border border-zinc-800 bg-zinc-950 px-3 py-1 text-xs text-zinc-300">
                sh_ frontdesk --tickets
            </span>
            <span class="text-xs text-zinc-500">•</span>
            <span class="text-xs text-zinc-500">
                Logged in as <span class="text-zinc-300"><?= htmlspecialchars($user['email'] ?? 'user') ?></span>
            </span>
        </div>

        <?php if (empty($tickets)): ?>
            <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-8 text-center">
                <div class="text-sm font-medium text-zinc-200">No tickets found</div>
                <p class="mt-2 text-sm text-zinc-500">
                    Need help? Open a ticket and we’ll track it like adults.
                </p>
                <div class="mt-5">
                    <a href="create.php"
                       class="inline-flex items-center rounded-lg bg-zinc-100 px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-white">
                        + Open New Ticket
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-950/60">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-800">
                        <thead class="bg-zinc-950">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-400">Ticket</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-400">Subject</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-400">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-400">Priority</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-zinc-400">Created</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-zinc-400">Action</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-zinc-900">
                        <?php foreach ($tickets as $ticket): ?>
                            <?php
                            $ticketId     = (int)($ticket['id'] ?? 0);
                            $ticketNumber = $ticket['ticket_number'] ?? ('#' . $ticketId);
                            $subject      = $ticket['subject'] ?? '(no subject)';
                            $status       = $ticket['status'] ?? 'open';
                            $priority     = $ticket['priority'] ?? 'medium';
                            $createdAt    = $ticket['created_at'] ?? null;
                            ?>
                            <tr class="hover:bg-zinc-950/60">
                                <td class="px-4 py-3 text-sm">
                                    <a class="font-mono text-zinc-100 hover:underline"
                                       href="view.php?id=<?= $ticketId ?>">
                                        <?= sanitizeOutput($ticketNumber) ?>
                                    </a>
                                    <div class="mt-1 text-xs text-zinc-500">#<?= $ticketId ?></div>
                                </td>

                                <td class="px-4 py-3 text-sm text-zinc-200">
                                    <?= sanitizeOutput($subject) ?>
                                </td>

                                <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs <?= statusPillClasses((string)$status) ?>">
                                            <?= sanitizeOutput(ucfirst((string)$status)) ?>
                                        </span>
                                </td>

                                <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs <?= priorityPillClasses((string)$priority) ?>">
                                            <?= sanitizeOutput(ucfirst((string)$priority)) ?>
                                        </span>
                                </td>

                                <td class="px-4 py-3 text-sm text-zinc-400">
                                    <?= sanitizeOutput(prettyDate($createdAt)) ?>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <a href="view.php?id=<?= $ticketId ?>"
                                       class="inline-flex items-center rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 hover:border-zinc-700">
                                        View →
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 text-xs text-zinc-500">
                Tip: Ticket numbers follow <span class="font-mono text-zinc-300">&lt;SITE&gt;-&lt;TYPE&gt;-&lt;NUMBER&gt;</span>
                (ex: <span class="font-mono text-zinc-300">GUILD-R-000123</span>)
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
