<?php
require_once '../core/functions.php';
start_session_securely();

// Check if user came from registration
if (!isset($_SESSION['email'])) {
    redirect('signup.php');
}

// Retrieve any error or success messages from the session
$error = get_error();
$success = get_success();
$email = $_SESSION['email'] ?? '';
$firstName = $_SESSION['first_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Your Email - CodeCodex</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <a href="login.php" class="close-btn">✕</a>

    <div class="login-container">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-envelope-open-text"></i>
            </div>
        </div>

        <h1>Check Your Inbox!</h1>
        
        <p class="subtitle">
            We've sent a verification link to
        </p>
        <p class="email-display"><?php echo htmlspecialchars($email); ?></p>
        
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Next Steps:</strong>
                <ol style="margin: 8px 0 0 0; padding-left: 20px; text-align: left;">
                    <li>Open your email inbox</li>
                    <li>Find the email from CodeCodex</li>
                    <li>Click the verification link</li>
                    <li>Return here to log in</li>
                </ol>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <div class="expiry-notice">
            <i class="fas fa-clock"></i>
            <span><strong>Important:</strong> The verification link expires in 24 hours.</span>
        </div>

        <div class="help-section">
            <p>Didn't receive the email?</p>
            <ul>
                <li>Check your spam/junk folder</li>
                <li>Make sure you entered the correct email</li>
                <li>Wait a few minutes and check again</li>
            </ul>
        </div>

        <div class="button-group">
            <a href="login.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>

    <script src="../assets/js/loading.js"></script>
</body>
</html>

