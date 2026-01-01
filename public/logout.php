<?php
require_once __DIR__ . '/../includes/bootstrap.php';

use MSPGuild\Core\Auth;

Auth::logout();

header('Location: ' . SITE_URL . '/index.php');
exit;

