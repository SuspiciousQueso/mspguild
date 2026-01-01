<?php
require_once __DIR__ . '/../../../includes/bootstrap.php';
require_once __DIR__ . '/../../../includes/modules/frontdesk/functions.php';

use MSPGuild\Core\Auth;

Auth::requireAuth();

$user = Auth::getCurrentUser();
if (!$user) {
    header("Location: " . SITE_URL . "/login.php");
    exit;
}

$error = '';

// Defaults (so the form is sticky on errors)
$form = [
        'ticket_type' => 'R',
        'priority'    => 'medium',
        'subject'     => '',
        'description' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';

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
        } elseif (!in_array($form['ticket_type'], ['R', 'T'], true)) {
            $error = "Invalid ticket type.";
        } else {
            $ticketId = createTicket((int)$user['id'], [
                    'subject'     => $form['subject'],
                    'description' => $form['description'],
                    'priority'    => $form['priority'],
                    'ticket_type' => $form['ticket_type'],
            ]);

            if ($ticketId) {
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
                   class="inline-fl
