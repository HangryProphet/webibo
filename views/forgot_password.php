<?php
require_once '../core/functions.php';
start_session_securely();

// Retrieve any error or success messages from the session
$error = get_error();
$success = get_success();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - WebQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <a href="login.php" class="close-btn">✕</a>

    <div class="login-container fade-up">
        <h1>Reset Password</h1>
        <p style="text-align: center; color: #8b95a5; margin-bottom: 24px; font-size: 14px;">
            Enter your email address and we'll send you a link to reset your password.
        </p>

        <form method="POST" action="../controllers/forgot_password_handler.php">
            <div class="input-group">
                <i class="fas fa-envelope input-icon"></i>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    placeholder="Email address" 
                    required
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                >
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

            <button type="submit" class="login-btn">SEND RESET LINK</button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="login.php" style="color: #1cb0f6; text-decoration: none; font-weight: 600;">
                ← Back to Login
            </a>
        </div>

        <div class="footer-text">
            By signing in to Webibo, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.<br>
        </div>
    </div>
</body>
</html>
