<?php
require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once __DIR__ . '/../../../includes/modules/frontdesk/functions.php';

use MSPGuild\Core\Auth;

Auth::requireAuth();

$user = Auth::getCurrentUser();

$ticketId = (int)($_GET['id'] ?? 0);
$ticket   = getTicketById($ticketId, (int)$user['id']);

if (!$ticket) {
    die("Ticket not found or access denied.");
}

// Handle new comments
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    if (Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $comment = trim($_POST['comment'] ?? '');
        if ($comment !== '') {
            addTicketMessage($ticketId, (int)$user['id'], $comment);
        }
        header("Location: view.php?id=" . $ticketId);
        exit;
    }
}

$comments    = getTicketMessages($ticketId);
$pageTitle   = 'Ticket View';
$currentPage = 'frontdesk';
$isLoggedIn  = true;

require_once __DIR__ . '/../../../includes/header.php';

// Helpers
function prettyDateTime(?string $ts): string {
    if (!$ts) return '';
    $t = strtotime($ts);
    if (!$t) return '';
    return date('M j, Y g:i a', $t);
}

function prettyDate(?string $ts): string {
    if (!$ts) return '';
    $t = strtotime($ts);
    if (!$t) return '';
    return date('F j, Y', $t);
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

// Best-effort display fields (don’t assume your schema)
$ticketNumber = $ticket['ticket_number'] ?? ('#' . (string)($ticket['id'] ?? $ticketId));
$ticketType   = $ticket['ticket_type'] ?? '';
$requester    = $ticket['requester_name'] ?? $ticket['full_name'] ?? $ticket['requester_email'] ?? 'Requester';
$status       = $ticket['status'] ?? 'open';
$priority     = $ticket['priority'] ?? 'medium';
?>

<div class="mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
    <div class="py-10">

        <!-- Page header -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-semibold tracking-tight text-zinc-100">
                        <?= sanitizeOutput($ticket['subject'] ?? 'Ticket') ?>
                    </h1>

                    <span class="rounded-full border border-zinc-800 bg-zinc-950 px-2.5 py-1 text-xs font-mono text-zinc-300">
                        <?= sanitizeOutput($ticketNumber) ?>
                    </span>

                    <?php if ($ticketType): ?>
                        <span class="rounded-full border border-zinc-800 bg-zinc-950 px-2.5 py-1 text-xs font-mono text-zinc-400">
                            TYPE: <?= sanitizeOutput($ticketType) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <p class="mt-1 text-sm text-zinc-400">
                    Created <?= sanitizeOutput(prettyDateTime($ticket['created_at'] ?? null)) ?>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs <?= statusPillClasses((string)$status) ?>">
                    <?= sanitizeOutput(ucfirst((string)$status)) ?>
                </span>

                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs <?= priorityPillClasses((string)$priority) ?>">
                    <?= sanitizeOutput(ucfirst((string)$priority)) ?>
                </span>

                <a href="index.php"
                   class="inline-flex items-center rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 hover:border-zinc-700">
                    ← Back to Tickets
                </a>
            </div>
        </div>

        <!-- Shell badge -->
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="rounded-full border border-zinc-800 bg-zinc-950 px-3 py-1 text-xs text-zinc-300">
                sh_ frontdesk --ticket <?= sanitizeOutput($ticketNumber) ?>
            </span>
            <span class="text-xs text-zinc-500">•</span>
            <span class="text-xs text-zinc-500">
                Viewing as <span class="text-zinc-300"><?= htmlspecialchars($user['email'] ?? 'user') ?></span>
            </span>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Main column -->
            <div class="lg:col-span-2 space-y-4">

                <!-- Original description -->
                <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-6">
                    <div class="mb-3 flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-medium text-zinc-200">
                                <?= sanitizeOutput($requester) ?>
                            </div>
                            <div class="mt-1 text-xs text-zinc-500">
                                <?= sanitizeOutput(prettyDateTime($ticket['created_at'] ?? null)) ?>
                            </div>
                        </div>
                    </div>

                    <div class="prose prose-invert max-w-none prose-p:leading-relaxed">
                        <p class="whitespace-pre-wrap text-sm text-zinc-200">
                            <?= sanitizeOutput($ticket['description'] ?? '') ?>
                        </p>
                    </div>
                </div>

                <!-- Updates -->
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-medium text-zinc-200">Updates</h2>
                    <div class="text-xs text-zinc-500">
                        <?= count($comments) ?> message<?= count($comments) === 1 ? '' : 's' ?>
                    </div>
                </div>

                <?php if (!empty($comments)): ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-5">
                            <div class="mb-3 flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-medium text-zinc-200">
                                        <?= sanitizeOutput($comment['full_name'] ?? 'User') ?>
                                    </div>
                                    <div class="mt-1 text-xs text-zinc-500">
                                        <?= sanitizeOutput(prettyDateTime($comment['created_at'] ?? null)) ?>
                                    </div>
                                </div>
                            </div>

                            <div class="text-sm text-zinc-200 whitespace-pre-wrap">
                                <?= sanitizeOutput($comment['comment'] ?? '') ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-6 text-sm text-zinc-400">
                        No updates yet. First reply wins.
                    </div>
                <?php endif; ?>

                <!-- Post a Reply -->
                <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-6">
                    <h3 class="text-sm font-medium text-zinc-200">Post a Reply</h3>
                    <p class="mt-1 text-xs text-zinc-500">Add context, progress, or the fix.</p>

                    <form method="POST" class="mt-4 space-y-3">
                        <input type="hidden" name="csrf_token" value="<?= Auth::generateCsrfToken(); ?>">

                        <textarea name="comment"
                                  rows="5"
                                  required
                                  placeholder="Type your update here…"
                                  class="w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"></textarea>

                        <div class="flex items-center justify-between gap-3">
                            <div class="text-xs text-zinc-500">
                                Tip: paste error output exactly as-is. Don’t “summarize” it.
                            </div>

                            <button type="submit"
                                    class="inline-flex items-center rounded-lg bg-zinc-100 px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-white">
                                Post Reply →
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-4">
                <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-6">
                    <h3 class="text-sm font-medium text-zinc-200">Ticket Details</h3>

                    <dl class="mt-4 space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-zinc-500">Ticket</dt>
                            <dd class="font-mono text-zinc-200"><?= sanitizeOutput($ticketNumber) ?></dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-zinc-500">Status</dt>
                            <dd>
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs <?= statusPillClasses((string)$status) ?>">
                                    <?= sanitizeOutput(ucfirst((string)$status)) ?>
                                </span>
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-zinc-500">Priority</dt>
                            <dd>
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs <?= priorityPillClasses((string)$priority) ?>">
                                    <?= sanitizeOutput(ucfirst((string)$priority)) ?>
                                </span>
                            </dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-zinc-500">Created</dt>
                            <dd class="text-zinc-200"><?= sanitizeOutput(prettyDate($ticket['created_at'] ?? null)) ?></dd>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <dt class="text-zinc-500">Last Updated</dt>
                            <dd class="text-zinc-200"><?= sanitizeOutput(prettyDate($ticket['updated_at'] ?? null)) ?></dd>
                        </div>
                    </dl>

                    <div class="mt-6">
                        <a href="index.php"
                           class="inline-flex w-full items-center justify-center rounded-lg border border-zinc-800 bg-zinc-950 px-4 py-2 text-sm text-zinc-100 hover:border-zinc-700">
                            Back to List
                        </a>
                    </div>
                </div>

                <?php if (isset($_GET['created']) && $_GET['created'] == '1'): ?>
                    <div class="rounded-2xl border border-emerald-900/50 bg-emerald-950/40 p-4 text-sm text-emerald-200">
                        Ticket created successfully.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
