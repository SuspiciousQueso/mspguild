<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/modules/contact/functions.php';

use MSPGuild\Core\Auth;

$pageTitle   = "Contact Us";
$currentPage = 'contact';
$isLoggedIn  = Auth::isLoggedIn();

$csrfToken = Auth::generateCsrfToken();

$form = [
        'name'    => '',
        'email'   => '',
        'company' => '',
        'phone'   => '',
        'message' => '',
];

$success = '';
$error   = '';

if (isset($_GET['sent']) && $_GET['sent'] == '1') {
    $success = "Message sent. We’ll get back to you soon.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['name']    = trim($_POST['name'] ?? '');
    $form['email']   = trim($_POST['email'] ?? '');
    $form['company'] = trim($_POST['company'] ?? '');
    $form['phone']   = trim($_POST['phone'] ?? '');
    $form['message'] = trim($_POST['message'] ?? '');

    $csrf = $_POST['csrf_token'] ?? '';

    if (!Auth::verifyCsrfToken($csrf)) {
        $error = "Invalid security token.";
    } elseif ($form['name'] === '' || $form['email'] === '' || $form['message'] === '') {
        $error = "Name, email, and message are required.";
    } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $user = $isLoggedIn ? Auth::getCurrentUser() : null;

        $savedId = saveContactMessage([
                'site_id'    => $_SESSION['site_id'] ?? null, // ok if null
                'user_id'    => $user['id'] ?? null,
                'name'       => $form['name'],
                'email'      => $form['email'],
                'company'    => $form['company'],
                'phone'      => $form['phone'],
                'message'    => $form['message'],
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);

        if ($savedId) {
            header("Location: contact.php?sent=1");
            exit;
        }

        $error = "Couldn’t send your message. Please try again.";
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-8">
    <div class="py-10">

        <div class="mb-8 text-center">
            <h1 class="text-3xl font-semibold tracking-tight text-zinc-100">Get in Touch</h1>
            <p class="mt-2 text-sm text-zinc-400">
                We’re here to help with real-world IT. Send a message and we’ll respond promptly.
            </p>
        </div>

        <div class="mb-6 flex flex-wrap items-center justify-center gap-2">
            <span class="rounded-full border border-zinc-800 bg-zinc-950 px-3 py-1 text-xs text-zinc-300">
                sh_ contact --send
            </span>
        </div>

        <?php if ($success): ?>
            <div class="mb-5 rounded-2xl border border-emerald-900/50 bg-emerald-950/40 p-4 text-sm text-emerald-200">
                <div class="font-medium">Success</div>
                <div class="mt-1"><?= sanitizeOutput($success) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mb-5 rounded-2xl border border-red-900/50 bg-red-950/40 p-4 text-sm text-red-200">
                <div class="font-medium">Couldn’t send</div>
                <div class="mt-1"><?= sanitizeOutput($error) ?></div>
            </div>
        <?php endif; ?>

        <div class="rounded-2xl border border-zinc-800 bg-zinc-950/60 p-6 shadow-sm">
            <form action="contact.php" method="POST" class="space-y-5" id="contactForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm text-zinc-300">Full Name <span class="text-zinc-600">(required)</span></label>
                        <input type="text" id="name" name="name" required maxlength="255"
                               value="<?= htmlspecialchars($form['name']) ?>"
                               placeholder="John Doe"
                               class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600">
                    </div>

                    <div>
                        <label for="email" class="block text-sm text-zinc-300">Email Address <span class="text-zinc-600">(required)</span></label>
                        <input type="email" id="email" name="email" required maxlength="255"
                               value="<?= htmlspecialchars($form['email']) ?>"
                               placeholder="john@example.com"
                               class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="company" class="block text-sm text-zinc-300">Company Name</label>
                        <input type="text" id="company" name="company" maxlength="255"
                               value="<?= htmlspecialchars($form['company']) ?>"
                               placeholder="Your Company Inc."
                               class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm text-zinc-300">Phone Number</label>
                        <input type="tel" id="phone" name="phone" maxlength="50"
                               value="<?= htmlspecialchars($form['phone']) ?>"
                               placeholder="(555) 123-4567"
                               class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600">
                    </div>
                </div>

                <div>
                    <label for="message" class="block text-sm text-zinc-300">Message <span class="text-zinc-600">(required)</span></label>
                    <textarea id="message" name="message" rows="7" required maxlength="5000"
                              placeholder="Tell us about your IT needs or questions…"
                              class="mt-2 w-full rounded-lg border border-zinc-800 bg-zinc-950 px-3 py-2 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-zinc-600"><?= htmlspecialchars($form['message']) ?></textarea>
                    <div class="mt-2 text-xs text-zinc-500">Max 5000 characters.</div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="text-xs text-zinc-500">
                        Don’t include passwords or secrets. We’ll never ask.
                    </div>

                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-zinc-100 px-4 py-2 text-sm font-medium text-zinc-900 hover:bg-white">
                        Send Message →
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
