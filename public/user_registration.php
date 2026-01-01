<?php
require_once __DIR__ . '/../includes/bootstrap.php';
// Remove the old includes/Auth.php require if it's there

use MSPGuild\Core\Auth;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = "Invalid security token.";
    } else {
        // ... existing registration logic ...
        // After successful registerUser():
        // $success = "Registration successful! You can now log in.";
    }
}
// ...
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
            <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCsrfToken(); ?>">
            
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
