<?php
require_once __DIR__ . '/../includes/bootstrap.php';

use MSPGuild\Core\Auth;

$pageTitle   = "Login";
$currentPage = 'login';
$isLoggedIn  = Auth::isLoggedIn();

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!Auth::verifyCsrfToken($csrf_token)) {
        $error = "Invalid security token.";
    } else {
        $user = Auth::authenticate($email, $password);

        if ($user) {
            Auth::loginUser($user);

            // Redirect to the dashboard or the page they were trying to reach
            $redirect = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
            unset($_SESSION['redirect_after_login']);

            // Safety: prevent weird external redirects
            if (str_starts_with($redirect, 'http://') || str_starts_with($redirect, 'https://')) {
                $redirect = 'dashboard.php';
            }

            header("Location: " . $redirect);
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="mx-auto w-full max-w-md px-4 sm:px-6 lg:px-8">
    <div class="py-12">

        <div class="mb-6 text-center">
            <div class="text-[11px] font-mono text-zinc-500">
                <span class="text-emerald-400">$</span> sh_ auth --login
            </div>
            <h1 class="mt-3 text-2xl font-semibold tracking-tight text-zinc-100">Client Portal</h1>
            <p class="mt-1 text-sm text-zinc-400">Sign in to access your account</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-5 rounded-2xl border border-red-900/50 bg-red-950/40 p-4 text-sm text-red-200">
                <div class="font-medium">Login failed</div>
                <div class="mt-1"><?= sanitizeOutput($error) ?></div>
            </div>
        <?php endif; ?>

        <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-6 shadow-sm">
            <form action="login.php" method="POST" class="space-y-4" id="loginForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Auth::generateCsrfToken()); ?>">

                <div>
                    <label for="email" class="block text-sm text-zinc-300">Email</label>
                    <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            autocomplete="username"
                            placeholder="you@example.com"
                            value="<?= htmlspecialchars($email) ?>"
                            class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm text-zinc-300">Password</label>
                    <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter your password"
                            class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                    >
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="inline-flex items-center gap-2 text-sm text-zinc-400">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-zinc-700 bg-zinc-950">
                        Remember me
                    </label>

                    <a href="<?= (defined('SITE_URL') ? SITE_URL : '') ?>/contact.php"
                       class="text-sm text-zinc-400 hover:text-zinc-200 hover:underline">
                        Need help?
                    </a>
                </div>

                <button type="submit"
                        class="mt-2 inline-flex w-full items-center justify-center rounded-lg bg-zinc-100 px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-white">
                    Sign In →
                </button>
            </form>

            <div class="mt-6 border-t border-zinc-800 pt-5 text-center">
                <p class="text-sm text-zinc-400">Don’t have an account yet?</p>
                <a href="user_registration.php"
                   class="mt-3 inline-flex items-center justify-center rounded-lg border border-zinc-800 bg-zinc-950 px-4 py-2 text-sm text-zinc-100 hover:border-zinc-700">
                    Create Guild Account
                </a>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
