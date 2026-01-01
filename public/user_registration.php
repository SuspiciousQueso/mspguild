<?php
require_once __DIR__ . '/../includes/bootstrap.php';

use MSPGuild\Core\Auth;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
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

        if (empty($userData['email']) || empty($userData['password']) || empty($userData['full_name'])) {
            $error = "Please fill in all required fields.";
        } else {
            $userId = registerUser($userData);
            if ($userId) {
                $success = "Registration successful! You can now log in.";
            } else {
                $error = "Registration failed. Email might already be in use.";
            }
        }
    }
}

$pageTitle = "Join the Guild";
$currentPage = 'register';
$isLoggedIn = Auth::isLoggedIn();

include __DIR__ . '/../includes/header.php';
?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h2 class="h4 mb-0">Join the MSP Guild</h2>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo sanitizeOutput($error); ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?php echo $success; ?>
                                <a href="login.php" class="alert-link">Click here to login</a>.
                            </div>
                        <?php else: ?>
                            <form method="POST" action="user_registration.php">
                                <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCsrfToken(); ?>">

                                <div class="mb-3">
                                    <label class="form-label">Full Name *</label>
                                    <input type="text" name="full_name" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email Address *</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Company Name</label>
                                    <input type="text" name="company_name" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password * (min 8 chars)</label>
                                    <input type="password" name="password" class="form-control" required minlength="8">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg">Create Account</button>
                                </div>
                            </form>
                            <p class="mt-3 text-center">
                                Already have an account? <a href="login.php">Login here</a>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>