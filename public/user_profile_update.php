<?php
require_once __DIR__ . '/../includes/bootstrap.php';

use MSPGuild\Core\Auth;

// Require login
Auth::requireAuth();

$user    = Auth::getCurrentUser();
$error   = '';
$success = '';

// Sticky form values (default from current user)
$form = [
        'full_name'     => $user['full_name'] ?? '',
        'company_name'  => $user['company_name'] ?? '',
        'contact_phone' => $user['contact_phone'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Sticky capture
    $form['full_name']     = trim($_POST['full_name'] ?? '');
    $form['company_name']  = trim($_POST['company_name'] ?? '');
    $form['contact_phone'] = trim($_POST['contact_phone'] ?? '');

    if (!Auth::verifyCsrfToken($csrf_token)) {
        $error = "Invalid security token.";
    } else {
        $updateData = [
                'full_name'     => $form['full_name'],
                'company_name'  => $form['company_name'],
                'contact_phone' => $form['contact_phone'],
        ];

        if ($updateData['full_name'] === '') {
            $error = "Full name is required.";
        } else {
            // Assumes updateUserProfile() exists in includes/functions.php (loaded by bootstrap.php)
            if (updateUserProfile((int)$user['id'], $updateData)) {
                $success = "Profile updated successfully!";

                // Refresh user data for display
                $user = Auth::getCurrentUser();

                // Update session name if you store it
                if (!empty($user['full_name'])) {
                    $_SESSION['user_name'] = $user['full_name'];
                }
            } else {
                $error = "Failed to update profile. Please try again.";
            }
        }
    }
}

$pageTitle   = "My Profile";
$currentPage = "profile";
$isLoggedIn  = true;

include __DIR__ . '/../includes/header.php';
?>

<div class="mx-auto w-full max-w-2xl px-4 sm:px-6 lg:px-8">
    <div class="py-10">

        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="text-[11px] font-mono text-zinc-500">
                    <span class="text-emerald-400">$</span> sh_ user --profile
                </div>
                <h1 class="mt-2 text-2xl font-semibold tracking-tight text-zinc-100">Account Profile</h1>
                <p class="mt-1 text-sm text-zinc-400">Manage your MSPGuild account details.</p>
            </div>

            <div class="text-xs text-zinc-500">
                Member since:
                <span class="text-zinc-300"><?= isset($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : '—'; ?></span>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="mb-5 rounded-2xl border border-red-900/50 bg-red-950/40 p-4 text-sm text-red-200">
                <div class="font-medium">Update failed</div>
                <div class="mt-1"><?= sanitizeOutput($error) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="mb-5 rounded-2xl border border-emerald-900/50 bg-emerald-950/40 p-4 text-sm text-emerald-200">
                <div class="font-medium">Saved</div>
                <div class="mt-1"><?= sanitizeOutput($success) ?></div>
            </div>
        <?php endif; ?>

        <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-6 shadow-sm">
            <form method="POST" action="user_profile_update.php" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Auth::generateCsrfToken()); ?>">

                <div>
                    <label class="block text-sm text-zinc-300">Email Address</label>
                    <input type="text" value="<?= sanitizeOutput($user['email'] ?? '') ?>" disabled
                           class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-900/40 px-3 py-2 text-sm text-zinc-400">
                    <div class="mt-2 text-xs text-zinc-500">Email address cannot be changed.</div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="full_name" class="block text-sm text-zinc-300">
                            Full Name <span class="text-zinc-600">(required)</span>
                        </label>
                        <input type="text" name="full_name" id="full_name" required maxlength="255"
                               value="<?= htmlspecialchars($form['full_name']) ?>"
                               class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                               placeholder="Your name">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="company_name" class="block text-sm text-zinc-300">Company Name</label>
                        <input type="text" name="company_name" id="company_name" maxlength="255"
                               value="<?= htmlspecialchars($form['company_name']) ?>"
                               class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                               placeholder="Optional">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="contact_phone" class="block text-sm text-zinc-300">Contact Phone</label>
                        <input type="text" name="contact_phone" id="contact_phone" maxlength="50"
                               value="<?= htmlspecialchars($form['contact_phone']) ?>"
                               class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                               placeholder="Optional">
                    </div>
                </div>

                <div class="flex items-center justify-between rounded-xl border border-zinc-800 bg-zinc-950 p-4">
                    <div>
                        <div class="text-xs font-mono text-zinc-500">tier://service</div>
                        <div class="mt-1 text-sm text-zinc-300">
                            <?= strtoupper(sanitizeOutput($user['service_tier'] ?? 'basic')) ?>
                        </div>
                    </div>

                    <a href="<?= (defined('SITE_URL') ? SITE_URL : '') ?>/contact.php"
                       class="text-xs text-zinc-400 hover:text-zinc-200 hover:underline">
                        Upgrade / billing →
                    </a>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pt-2">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-zinc-100 px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-white">
                        Save Changes →
                    </button>

                    <a href="logout.php"
                       class="text-sm text-zinc-400 hover:text-red-300 hover:underline">
                        Log out
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
