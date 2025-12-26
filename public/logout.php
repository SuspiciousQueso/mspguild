<?php
require_once __DIR__ . '/../includes/Auth.php';

logoutUser();

header('Location: ' . SITE_URL . '/index.php');
exit;
