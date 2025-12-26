<?php
require_once __DIR__ . '/../includes/Auth.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    $_SESSION['login_error'] = 'Invalid request. Please try again.';
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

// Get and sanitize inputs
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Validate inputs
if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = 'Please provide both email and password.';
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_error'] = 'Invalid email format.';
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}

// Authenticate user
$user = authenticateUser($email, $password);

if ($user) {
    // Login successful
    loginUser($user);
    
    // Set remember me cookie if requested (optional - implement as needed)
    if ($remember) {
        // Extend session lifetime
        ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); // 30 days
    }
    
    // Redirect to dashboard or originally requested page
    $redirect = $_SESSION['redirect_after_login'] ?? SITE_URL . '/dashboard.php';
    unset($_SESSION['redirect_after_login']);
    
    header('Location: ' . $redirect);
    exit;
} else {
    // Login failed
    $_SESSION['login_error'] = 'Invalid email or password. Please try again.';
    header('Location: ' . SITE_URL . '/login.php');
    exit;
}
