<?php
require_once __DIR__ . '/../includes/bootstrap.php';

use MSPGuild\Core\Auth;
use MSPGuild\Services\PasswordReset;

$pageTitle   = "Forgot Password";
$currentPage = 'forgot_password';
$isLoggedIn  = Auth::isLoggedIn();

$success = '';
$error   = '';
$email   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $csrf  = (string)($_POST['csrf_token'] ?? '');

    if (!Auth::verifyCsrfToken($csrf)) {
        $error = "Session expired. Please try again.";
    } else {
        // Always respond generically (avoid email enumeration)
        $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
        $ua = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');

        PasswordReset::request($email, $ip, $ua);

        $success = "If an account exists for that email, a reset link has been sent.";
        $email = '';
    }
}
?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="flex min-h-[70vh] w-full items-center justify-center px-4 py-10">
    <div class="w-full max-w-md rounded-3xl border border-zinc-800 bg-zinc-950/60 p-7 shadow-2xl">
        <div class="mb-6 text-center">
            <div class="text-[11px] font-mono text-zinc-500">
                <span class="text-emerald-400">$</span> sh_ auth --reset
            </div>
            <h1 class="mt-3 text-2xl font-semibold tracking-tight text-zinc-100">Reset your password</h1>
            <p class="mt-1 text-sm text-zinc-400">We’ll email you a secure link.</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-5 rounded-2xl border border-red-900/50 bg-red-950/40 p-4 text-sm text-red-200">
                <div class="font-medium">Couldn’t send reset link</div>
                <div class="mt-1"><?= sanitizeOutput($error) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="mb-5 rounded-2xl border border-emerald-900/40 bg-emerald-950/25 p-4 text-sm text-emerald-200">
                <div class="font-medium">Check your inbox</div>
                <div class="mt-1"><?= sanitizeOutput($success) ?></div>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= sanitizeOutput(Auth::generateCsrfToken()) ?>">

            <div>
                <label for="email" class="text-sm text-zinc-300">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="<?= sanitizeOutput($email) ?>"
                    required
                    autocomplete="email"
                    placeholder="you@company.com"
                    class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                >
            </div>

            <button type="submit"
                    class="mt-2 w-full rounded-lg border border-emerald-900/40 bg-emerald-950/30 px-4 py-2 text-sm font-medium text-emerald-100 hover:bg-emerald-950/50">
                Send reset link
            </button>

            <div class="pt-2 text-center text-sm text-zinc-400">
                <a class="hover:text-white" href="login.php">Back to login</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
