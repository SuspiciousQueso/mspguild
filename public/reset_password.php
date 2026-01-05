<?php
require_once __DIR__ . '/../includes/bootstrap.php';

use MSPGuild\Core\Auth;
use MSPGuild\Services\PasswordReset;

$pageTitle   = "Set New Password";
$currentPage = 'reset_password';
$isLoggedIn  = Auth::isLoggedIn();

$token = trim((string)($_GET['token'] ?? ($_POST['token'] ?? '')));
$error = '';
$success = '';

$tokenRow = null;
if ($token !== '') {
    $tokenRow = PasswordReset::validateToken($token);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = (string)($_POST['csrf_token'] ?? '');
    $pass1 = (string)($_POST['password'] ?? '');
    $pass2 = (string)($_POST['password_confirm'] ?? '');

    if (!Auth::verifyCsrfToken($csrf)) {
        $error = "Session expired. Please try again.";
    } elseif (!$tokenRow) {
        $error = "That reset link is invalid or expired. Request a new one.";
    } elseif ($pass1 === '' || strlen($pass1) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($pass1 !== $pass2) {
        $error = "Passwords do not match.";
    } else {
        $ok = PasswordReset::resetWithToken($token, $pass1);
        if ($ok) {
            header("Location: " . (defined('SITE_URL') ? SITE_URL : '') . "/login.php?reset=1");
            exit;
        }
        $error = "Could not reset password. Please request a new reset link.";
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="flex min-h-[70vh] w-full items-center justify-center px-4 py-10">
    <div class="w-full max-w-md rounded-3xl border border-zinc-800 bg-zinc-950/60 p-7 shadow-2xl">
        <div class="mb-6 text-center">
            <div class="text-[11px] font-mono text-zinc-500">
                <span class="text-emerald-400">$</span> sh_ auth --set-password
            </div>
            <h1 class="mt-3 text-2xl font-semibold tracking-tight text-zinc-100">Set a new password</h1>
            <p class="mt-1 text-sm text-zinc-400">
                <?= $tokenRow ? "Choose something strong." : "This link looks invalid or expired." ?>
            </p>
        </div>

        <?php if ($error): ?>
            <div class="mb-5 rounded-2xl border border-red-900/50 bg-red-950/40 p-4 text-sm text-red-200">
                <div class="font-medium">Password reset failed</div>
                <div class="mt-1"><?= sanitizeOutput($error) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!$tokenRow): ?>
            <div class="rounded-2xl border border-zinc-800 bg-zinc-950/40 p-4 text-sm text-zinc-300">
                <p class="text-zinc-400">Request a new reset link:</p>
                <a class="mt-2 inline-flex items-center justify-center rounded-lg border border-zinc-800 px-4 py-2 text-sm text-zinc-100 hover:border-zinc-700"
                   href="forgot_password.php">Forgot password</a>
            </div>
        <?php else: ?>
            <form method="post" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= sanitizeOutput(Auth::generateCsrfToken()) ?>">
                <input type="hidden" name="token" value="<?= sanitizeOutput($token) ?>">

                <div>
                    <label for="password" class="text-sm text-zinc-300">New password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="At least 8 characters"
                        class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                    >
                </div>

                <div>
                    <label for="password_confirm" class="text-sm text-zinc-300">Confirm new password</label>
                    <input
                        id="password_confirm"
                        name="password_confirm"
                        type="password"
                        required
                        autocomplete="new-password"
                        placeholder="Repeat password"
                        class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                    >
                </div>

                <button type="submit"
                        class="mt-2 w-full rounded-lg border border-emerald-900/40 bg-emerald-950/30 px-4 py-2 text-sm font-medium text-emerald-100 hover:bg-emerald-950/50">
                    Update password
                </button>

                <div class="pt-2 text-center text-sm text-zinc-400">
                    <a class="hover:text-white" href="login.php">Back to login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
