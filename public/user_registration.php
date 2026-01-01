<?php
require_once __DIR__ . '/../includes/bootstrap.php';

use MSPGuild\Core\Auth;

$error   = '';
$success = '';

// Sticky form defaults
$form = [
        'full_name'    => '',
        'email'        => '',
        'company_name' => '',
        'contact_phone'=> '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // capture sticky inputs
    $form['full_name']     = trim($_POST['full_name'] ?? '');
    $form['email']         = trim($_POST['email'] ?? '');
    $form['company_name']  = trim($_POST['company_name'] ?? '');
    $form['contact_phone'] = trim($_POST['contact_phone'] ?? '');

    if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid security token.";
    } else {
        $userData = [
                'email'         => $form['email'],
                'password'      => $_POST['password'] ?? '',
                'full_name'     => $form['full_name'],
                'company_name'  => $form['company_name'],
                'contact_phone' => $form['contact_phone'],
                'service_tier'  => 'basic'
        ];

        if (empty($userData['email']) || empty($userData['password']) || empty($userData['full_name'])) {
            $error = "Please fill in all required fields.";
        } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (strlen((string)$userData['password']) < 8) {
            $error = "Password must be at least 8 characters.";
        } else {
            $userId = registerUser($userData);
            if ($userId) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Registration failed. Email might already be in use.";
            }
        }
    }
}

$pageTitle   = "Join the Guild";
$currentPage = 'register';
$isLoggedIn  = Auth::isLoggedIn();

include __DIR__ . '/../includes/header.php';
?>

<div class="mx-auto w-full max-w-md px-4 sm:px-6 lg:px-8">
    <div class="py-12">

        <div class="mb-6 text-center">
            <div class="text-[11px] font-mono text-zinc-500">
                <span class="text-emerald-400">$</span> sh_ auth --register
            </div>
            <h1 class="mt-3 text-2xl font-semibold tracking-tight text-zinc-100">Join the MSP Guild</h1>
            <p class="mt-1 text-sm text-zinc-400">Create an account to access FrontDesk and future modules.</p>
        </div>

        <?php if ($error): ?>
            <div class="mb-5 rounded-2xl border border-red-900/50 bg-red-950/40 p-4 text-sm text-red-200">
                <div class="font-medium">Registration failed</div>
                <div class="mt-1"><?= sanitizeOutput($error) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="mb-5 rounded-2xl border border-emerald-900/50 bg-emerald-950/40 p-4 text-sm text-emerald-200">
                <div class="font-medium">Welcome aboard</div>
                <div class="mt-1">
                    <?= sanitizeOutput($success) ?>
                    <a href="login.php" class="ml-1 text-emerald-200 underline hover:text-emerald-100">Login now →</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-6 shadow-sm">
            <?php if (!$success): ?>
                <form method="POST" action="user_registration.php" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Auth::generateCsrfToken()); ?>">

                    <div>
                        <label class="block text-sm text-zinc-300">Full Name <span class="text-zinc-600">(required)</span></label>
                        <input type="text" name="full_name" required maxlength="255"
                               value="<?= htmlspecialchars($form['full_name']) ?>"
                               class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                               placeholder="Billy Baldwin">
                    </div>

                    <div>
                        <label class="block text-sm text-zinc-300">Email Address <span class="text-zinc-600">(required)</span></label>
                        <input type="email" name="email" required maxlength="255"
                               value="<?= htmlspecialchars($form['email']) ?>"
                               class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                               placeholder="you@example.com">
                    </div>

                    <div>
                        <label class="block text-sm text-zinc-300">Company Name</label>
                        <input type="text" name="company_name" maxlength="255"
                               value="<?= htmlspecialchars($form['company_name']) ?>"
                               class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                               placeholder="Your Company Inc.">
                    </div>

                    <div>
                        <label class="block text-sm text-zinc-300">Phone</label>
                        <input type="tel" name="contact_phone" maxlength="50"
                               value="<?= htmlspecialchars($form['contact_phone']) ?>"
                               class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                               placeholder="(555) 123-4567">
                    </div>

                    <div>
                        <label class="block text-sm text-zinc-300">Password <span class="text-zinc-600">(min 8 chars)</span></label>
                        <input type="password" name="password" required minlength="8"
                               class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"
                               placeholder="Create a strong password">
                    </div>

                    <button type="submit"
                            class="mt-2 inline-flex w-full items-center justify-center rounded-lg bg-zinc-100 px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-white">
                        Create Account →
                    </button>

                    <div class="pt-3 text-center text-sm text-zinc-400">
                        Already have an account?
                        <a href="login.php" class="text-zinc-200 hover:underline">Login here</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <div class="mt-6 text-center text-xs text-zinc-500">
            By joining, you’re opting into clean workflows, sane defaults, and a UI that doesn’t scream.
        </div>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
