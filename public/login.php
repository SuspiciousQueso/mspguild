<?php
require_once __DIR__ . '/../includes/bootstrap.php';
$isLoggedIn = \MSPGuild\Core\Auth::isLoggedIn();


use MSPGuild\Core\Auth;
$pageTitle   = "Login";
$currentPage = 'login';
$isLoggedIn  = Auth::isLoggedIn();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!Auth::verifyCsrfToken($csrf_token)) {
        $error = "Invalid security token.";
    } else {
        // Use the authenticateUser function we added to includes/functions.php
        $user = Auth::authenticate($email, $password);
        
        if ($user) {
            Auth::loginUser($user);
            
            // Redirect to dashboard or the page they were trying to reach
            $redirect = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
            unset($_SESSION['redirect_after_login']);
            header("Location: " . $redirect);
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}

$pageTitle = "Login";

include __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock text-primary display-3"></i>
                        <h2 class="fw-bold mt-3">Client Portal</h2>
                        <p class="text-muted">Sign in to access your account</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo sanitizeOutput($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                        <!-- Fix: Changed action to login.php so it handles its own logic -->
                        <form action="login.php" method="POST" id="loginForm">
                            <input type="hidden" name="csrf_token" value="<?php echo Auth::generateCsrfToken(); ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required
                                       placeholder="Enter your password" autocomplete="current-password">
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Sign In
                            </button>
                        </div>
                    </form>

                        <hr class="my-4">

                        <div class="text-center mb-3">
                            <p class="mb-1">Don't have an account yet?</p>
                            <a href="user_registration.php" class="btn btn-outline-success">Create Guild Account</a>
                        </div>

                    <div class="text-center">
                        <p class="text-muted small mb-2">Forgot your password?</p>
                        <a href="<?php echo SITE_URL; ?>/contact.php" class="text-decoration-none">Contact Support</a>
                    </div>

                    <div class="alert alert-info mt-4" role="alert">
                        <strong>Demo Account:</strong><br>
                        Email: demo@example.com<br>
                        Password: Demo123!
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
