<?php
/**
 * User Registration Handler
 * This is a basic registration endpoint for admin use or future self-registration feature
 * Add proper authorization checks before enabling in production
 */

require_once __DIR__ . '/../includes/bootstrap.php';

use MSPGuild\Core\Database;

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// For security, you may want to restrict this to admin users only
// Uncomment and modify as needed:
// requireAuth();
// if (!isAdmin()) { die('Unauthorized'); }

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$requiredFields = ['email', 'password', 'full_name'];
foreach ($requiredFields as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: $field"]);
        exit;
    }
}

$email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
$password = $input['password'];
$fullName = trim($input['full_name']);
$companyName = trim($input['company_name'] ?? '');
$contactPhone = trim($input['contact_phone'] ?? '');
$serviceTier = $input['service_tier'] ?? 'Basic';

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

// Validate password length
if (strlen($password) < MIN_PASSWORD_LENGTH) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least ' . MIN_PASSWORD_LENGTH . ' characters']);
    exit;
}

// Validate service tier
$validTiers = ['Basic', 'Professional', 'Enterprise', 'Custom'];
if (!in_array($serviceTier, $validTiers)) {
    $serviceTier = 'Basic';
}

try {
    $pdo = Database::getConnection();
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['error' => 'Email already registered']);
        exit;
    }
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password_hash, full_name, company_name, contact_phone, service_tier) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $email,
        $passwordHash,
        $fullName,
        $companyName ?: null,
        $contactPhone ?: null,
        $serviceTier
    ]);
    
    $userId = $pdo->lastInsertId();
    
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'User registered successfully',
        'user_id' => $userId
    ]);
    
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed. Please try again.']);
}
