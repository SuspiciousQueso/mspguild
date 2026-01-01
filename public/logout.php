<?php
require_once __DIR__ . '/../includes/bootstrap.php';

use MSPGuild\Core\Auth;

// Logout does NOT require login — it should be safe to call anytime
Auth::logout();

// Redirect to home (or login)
header('Location: ' . SITE_URL . '/index.php');
exit;
