<?php
require_once __DIR__ . '/../includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . SITE_URL . '/contact.php');
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    $_SESSION['contact_error'] = 'Invalid request. Please try again.';
    header('Location: ' . SITE_URL . '/contact.php');
    exit;
}

// Get and sanitize inputs
$name = trim($_POST['name'] ?? '');
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$company = trim($_POST['company'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validate required fields
if (empty($name) || empty($email) || empty($message)) {
    $_SESSION['contact_error'] = 'Please fill in all required fields.';
    header('Location: ' . SITE_URL . '/contact.php');
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['contact_error'] = 'Invalid email format.';
    header('Location: ' . SITE_URL . '/contact.php');
    exit;
}

// Validate lengths
if (strlen($name) > 255 || strlen($email) > 255 || strlen($company) > 255 || 
    strlen($phone) > 50 || strlen($message) > 5000) {
    $_SESSION['contact_error'] = 'One or more fields exceed maximum length.';
    header('Location: ' . SITE_URL . '/contact.php');
    exit;
}

try {
    // Insert into database
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("
        INSERT INTO contact_submissions (name, email, company, phone, message, status) 
        VALUES (?, ?, ?, ?, ?, 'New')
    ");
    
    $stmt->execute([
        $name,
        $email,
        $company ?: null,
        $phone ?: null,
        $message
    ]);
    
    // Success
    $_SESSION['contact_success'] = 'Thank you for contacting us! We will respond to your inquiry shortly.';
    
    // Optional: Send email notification to support team
    // mail(SUPPORT_EMAIL, "New Contact Form Submission", $message, "From: $email");
    
} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage());
    $_SESSION['contact_error'] = 'An error occurred while submitting your message. Please try again or contact us directly.';
}

header('Location: ' . SITE_URL . '/contact.php');
exit;
