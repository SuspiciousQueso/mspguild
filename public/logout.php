<?php
require_once __DIR__ . '/../includes/bootstrap.php';
use MSPGuild\Core\Auth;

// Require login
Auth::requireAuth();

header('Location: ' . SITE_URL . '/index.php');
exit;
