<?php
require_once __DIR__ . '/../includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCsrfToken($csrf_token)) {
        $error = "Invalid security token.";
    } else {
        $userData = [
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'full_name' => $_POST['full_name'] ?? '',
            'company_name' => $_POST['company_name'] ?? '',
            'contact_phone' => $_POST['contact_phone'] ?? '',
            'service_tier' => 'basic'
        ];

        // Basic validation
        if (empty($userData['email']) || empty($userData['password']) || empty($userData['full_name'])) {
            $error = "Please fill in all required fields.";
        } elseif (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (strlen($userData['password']) < 8) {
            $error = "Password must be at least 8 characters long.";
        } else {
            $userId = registerUser($userData);
            if ($userId) {
                $success = "Registration successful! You can now log in.";
                // Optionally auto-login:
                // $user = authenticateUser($userData['email'], $userData['password']);
                // loginUser($user);
                // header("Location: index.php"); exit;
            } else {
                $error = "Registration failed. Email might already be in use.";
            }
        }
    }
}

$pageTitle = "Register";
include __DIR__ . '/../includes/header.php';
?>

<div class="register-container" style="max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px;">
    <h2>Join the MSP Guild</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger" style="color: red; margin-bottom: 15px;">
            <?php echo sanitizeOutput($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success" style="color: green; margin-bottom: 15px;">
            <?php echo sanitizeOutput($success); ?> 
            <a href="login.php">Click here to login</a>.
        </div>
    <?php else: ?>
        <form method="POST" action="register.php">
            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="full_name">Full Name *</label>
                <input type="text" name="full_name" id="full_name" class="form-control" required style="width: 100%;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="email">Email Address *</label>
                <input type="email" name="email" id="email" class="form-control" required style="width: 100%;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label for="company_name">Company Name</label>
                <input type="text" name="company_name" id="company_name" class="form-control" style="width: 100%;">
            </div>

            <div class="form-group" style="margin-bottom: 15px;">
                <label for="password">Password * (min 8 chars)</label>
                <input type="password" name="password" id="password" class="form-control" required style="width: 100%;">
            </div>
            
            <button type="submit" class="btn btn-success" style="width: 100%; padding: 10px;">Create Account</button>
        </form>
    <?php endif; ?>
    
    <p style="margin-top: 20px; text-align: center;">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
