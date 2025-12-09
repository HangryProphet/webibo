<?php
require_once '../core/functions.php';
start_session_securely();

// Retrieve any error or success messages from the session
$error = get_error();
$success = get_success();

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    set_error("Invalid or missing reset token.");
    redirect('/views/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - WebQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <a href="login.php" class="close-btn">✕</a>

    <div class="login-container">
        <h1>Set New Password</h1>
        <p style="text-align: center; color: #8b95a5; margin-bottom: 24px; font-size: 14px;">
            Enter your new password below.
        </p>

        <form method="POST" action="../controllers/reset_password_handler.php">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            
            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    placeholder="New password" 
                    required
                    minlength="8"
                >
                <button type="button" class="password-toggle" onclick="togglePassword('password', this)">SHOW</button>
            </div>

            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input 
                    type="password" 
                    name="confirm_password" 
                    id="confirmPassword" 
                    placeholder="Confirm new password" 
                    required
                    minlength="8"
                >
                <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', this)">SHOW</button>
            </div>

            <?php if ($error): ?>
                <div class="feedback-message error">
                    <span class="feedback-icon">⚠</span>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="feedback-message success">
                    <span class="feedback-icon">✓</span>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
            <?php endif; ?>

            <button type="submit" class="login-btn">RESET PASSWORD</button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="login.php" style="color: #1cb0f6; text-decoration: none; font-weight: 600;">
                ← Back to Login
            </a>
        </div>

        <div class="footer-text">
            By signing in to Webibo, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.<br><br>
            This site is protected by reCAPTCHA Enterprise and the<br>
            Google <a href="#">Privacy Policy</a> and <a href="#">Terms of Service</a> apply.
        </div>
    </div>

    <script src="../assets/js/password_toggle.js"></script>
</body>
</html>
