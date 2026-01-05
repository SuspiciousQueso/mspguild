<?php
require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once __DIR__ . '/../../../includes/modules/frontdesk/functions.php';

use MSPGuild\Core\Auth;
use MSPGuild\Services\Mailer;
use MSPGuild\Email\EmailTemplates;

Auth::requireAuth();

$user = Auth::getCurrentUser();
if (!$user) {
    header("Location: " . SITE_URL . "/login.php");
    exit;
}

$error = '';

// Defaults (sticky on errors)
$form = [
        'ticket_type' => 'R',
        'priority'    => 'medium',
        'subject'     => '',
        'description' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';

    // Sticky values
    $form['ticket_type'] = $_POST['ticket_type'] ?? 'R';
    $form['priority']    = $_POST['priority'] ?? 'medium';
    $form['subject']     = trim($_POST['subject'] ?? '');
    $form['description'] = trim($_POST['description'] ?? '');

    if (!Auth::verifyCsrfToken($csrf)) {
        $error = "Invalid security token.";
    } else {
        $allowedPriorities = ['low','medium','high','emergency'];
        if (!in_array($form['priority'], $allowedPriorities, true)) {
            $form['priority'] = 'medium';
        }

        if ($form['subject'] === '' || $form['description'] === '') {
            $error = "Subject and description are required.";
        } elseif (!in_array($form['ticket_type'], ['R', 'I'], true)) {
            $error = "Invalid ticket type.";
        } else {
            $ticketId = createTicket((int)$user['id'], [
                    'subject'     => $form['subject'],
                    'description' => $form['description'],
                    'priority'    => $form['priority'],
                    'ticket_type' => $form['ticket_type'],
            ]);

            if ($ticketId) {
                
                // Best-effort email notifications (customer + support)
                try {
                    $ticket = getTicketById((int)$ticketId, (int)$user['id']);
                    $ticketNumber = $ticket['ticket_number'] ?? ('#' . (int)$ticketId);
                    $ticketUrl = (defined('SITE_URL') ? SITE_URL : '') . '/modules/frontdesk/view.php?id=' . (int)$ticketId;

                    // Customer copy
                    Mailer::send(EmailTemplates::TICKET_CREATED, $user['email'], [
                        'full_name'      => $user['full_name'] ?? '',
                        'ticket_id'      => (int)$ticketId,
                        'ticket_number'  => $ticketNumber,
                        'ticket_subject' => $ticket['subject'] ?? $form['subject'],
                        'ticket_url'     => $ticketUrl,
                        'priority'       => $ticket['priority'] ?? $form['priority'],
                    ]);

                    // Support copy
                    if (defined('SUPPORT_EMAIL') && filter_var(SUPPORT_EMAIL, FILTER_VALIDATE_EMAIL)) {
                        Mailer::send(EmailTemplates::TICKET_CREATED, SUPPORT_EMAIL, [
                            'full_name'      => $user['full_name'] ?? '',
                            'ticket_id'      => (int)$ticketId,
                            'ticket_number'  => $ticketNumber,
                            'ticket_subject' => $ticket['subject'] ?? $form['subject'],
                            'ticket_url'     => $ticketUrl,
                            'priority'       => $ticket['priority'] ?? $form['priority'],
                        ]);
                    }
                } catch (\Throwable $e) {
                    error_log('[FrontDesk] Ticket created email failed: ' . $e->getMessage());
                }

header("Location: view.php?id=" . (int)$ticketId . "&created=1");
                exit;
            }

            $error = "Failed to create ticket. Please try again.";
        }
    }
}

$pageTitle   = "Open Ticket";
$currentPage = 'frontdesk';
$isLoggedIn  = true;

include __DIR__ . '/../../../includes/header.php';
?>

<div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-8">
    <div class="py-10">

        <!-- Page header -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-100">FrontDesk</h1>
                <p class="mt-1 text-sm text-zinc-400">Open a new ticket. Keep it tight, keep it useful.</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="index.php"
                   class="inline-flex items-center rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 hover:border-zinc-700">
                    ← Back to Tickets
                </a>
            </div>
        </div>

        <!-- Shell badge -->
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="rounded-full border border-zinc-800 bg-zinc-950 px-3 py-1 text-xs text-zinc-300">
                sh_ frontdesk --open
            </span>
            <span class="text-xs text-zinc-500">•</span>
            <span class="text-xs text-zinc-500">
                Logged in as <span class="text-zinc-300"><?= htmlspecialchars($user['email'] ?? 'user') ?></span>
            </span>
        </div>

        <?php if ($error): ?>
            <div class="mb-5 rounded-2xl border border-red-900/50 bg-red-950/40 p-4 text-sm text-red-200">
                <div class="font-medium">Ticket not created</div>
                <div class="mt-1"><?= sanitizeOutput($error) ?></div>
            </div>
        <?php endif; ?>

        <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-6 shadow-sm">
            <form method="POST" action="create.php" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Auth::generateCsrfToken()); ?>">

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm text-zinc-300">Ticket Type</label>
                        <select name="ticket_type" required
                                class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-zinc-600">
                            <option value="R" <?= $form['ticket_type'] === 'R' ? 'selected' : '' ?>>Request (R)</option>
                            <option value="I" <?= $form['ticket_type'] === 'I' ? 'selected' : '' ?>>Incident (I)</option>
                        </select>
                        <div class="mt-2 text-xs text-zinc-500">Requests are planned work. Incidents are break/fix.</div>
                    </div>

                    <div>
                        <label class="block text-sm text-zinc-300">Priority</label>
                        <select name="priority"
                                class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-2 focus:ring-zinc-600">
                            <option value="low" <?= $form['priority'] === 'low' ? 'selected' : '' ?>>Low</option>
                            <option value="medium" <?= $form['priority'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="high" <?= $form['priority'] === 'high' ? 'selected' : '' ?>>High</option>
                            <option value="emergency" <?= $form['priority'] === 'emergency' ? 'selected' : '' ?>>Emergency</option>
                        </select>
                        <div class="mt-2 text-xs text-zinc-500">Emergency = production down / business impact.</div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-zinc-300">Subject</label>
                    <input type="text" name="subject" required maxlength="255"
                           value="<?= htmlspecialchars($form['subject']) ?>"
                           class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                           placeholder="Short summary of the issue">
                </div>

                <div>
                    <label class="block text-sm text-zinc-300">Description</label>
                    <textarea name="description" rows="8" required
                              class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                              placeholder="Details, errors, steps, impact, screenshots, etc."><?= htmlspecialchars($form['description']) ?></textarea>
                    <div class="mt-2 text-xs text-zinc-500">
                        Include: what changed, what you expected, what happened, and any error text.
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pt-2">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-zinc-100 px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-white">
                        Submit Ticket →
                    </button>

                    <div class="text-xs text-zinc-500">
                        Ticket numbers follow <span class="font-mono text-zinc-300">&lt;SITE&gt;-&lt;TYPE&gt;-&lt;NUMBER&gt;</span>
                        (ex: <span class="font-mono text-zinc-300">GUILD-I-000123</span>)
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../../../includes/footer.php'; ?>
