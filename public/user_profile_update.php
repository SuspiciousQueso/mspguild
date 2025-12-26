<?php
require_once __DIR__ . '/../includes/Auth.php';

// Ensure the user is logged in
requireAuth();

$user = getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCsrfToken($csrf_token)) {
        $error = "Invalid security token.";
    } else {
        $updateData = [
            'full_name' => $_POST['full_name'] ?? '',
            'company_name' => $_POST['company_name'] ?? '',
            'contact_phone' => $_POST['contact_phone'] ?? ''
        ];

        if (empty($updateData['full_name'])) {
            $error = "Full name is required.";
        } else {
            if (updateUserProfile($user['id'], $updateData)) {
                $success = "Profile updated successfully!";
                // Refresh user data for the display
                $user = getCurrentUser();
                // Update session name if changed
                $_SESSION['user_name'] = $user['full_name'];
            } else {
                $error = "Failed to update profile. Please try again.";
            }
        }
    }
}

$pageTitle = "My Profile";
include __DIR__ . '/../includes/header.php';
?>

<div class="profile-container" style="max-width: 600px; margin: 30px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;">
    <h2>Account Profile</h2>
    <p class="text-muted">Manage your MSP Guild account details.</p>
    
    <?php if ($error): ?>
        <div class="alert alert-danger" style="color: #721c24; background-color: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            <?php echo sanitizeOutput($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success" style="color: #155724; background-color: #d4edda; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
            <?php echo sanitizeOutput($success); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="profile.php">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label>Email Address</label>
            <input type="text" class="form-control" value="<?php echo sanitizeOutput($user['email']); ?>" disabled style="width: 100%; background-color: #e9ecef;">
            <small class="text-muted">Email address cannot be changed.</small>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="full_name">Full Name *</label>
            <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo sanitizeOutput($user['full_name']); ?>" required style="width: 100%;">
        </div>
        
        <div class="form-group" style="margin-bottom: 15px;">
            <label for="company_name">Company Name</label>
            <input type="text" name="company_name" id="company_name" class="form-control" value="<?php echo sanitizeOutput($user['company_name']); ?>" style="width: 100%;">
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label for="contact_phone">Contact Phone</label>
            <input type="text" name="contact_phone" id="contact_phone" class="form-control" value="<?php echo sanitizeOutput($user['contact_phone']); ?>" style="width: 100%;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Service Tier</label>
            <div style="padding: 8px; background: #eee; border-radius: 4px; display: inline-block;">
                <strong><?php echo strtoupper(sanitizeOutput($user['service_tier'])); ?></strong>
            </div>
        </div>
        
        <hr>
        
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">Save Changes</button>
            <span class="text-muted" style="font-size: 0.85em;">Member since: <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
        </div>
    </form>
    
    <div style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px; text-align: center;">
        <a href="logout.php" style="color: #dc3545;">Log Out</a>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
