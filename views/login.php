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
    <title>Log in - WebQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <a href="../index.php" class="brand-link">
        <img src="../assets/img/webibo/webibo-logo.png" alt="Webibo logo" class="brand-logo">
        <span class="brand-name">Webibo</span>
    </a>

    <div class="auth-layout fade-up">
        <div class="auth-illustration">
            <img src="../assets/img/wiza/wiza-login.png" alt="Wiza ready to log in" loading="lazy">
        </div>

        <div class="login-container">
            <h1>Log in</h1>

            <form method="POST" action="../controllers/auth_login.php">
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        placeholder="Email or username" 
                        autocomplete="username"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                    >
                </div>

                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="Password" 
                        autocomplete="current-password"
                    >
                    <a href="forgot_password.php" class="forgot-link">FORGOT?</a>
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

                <button type="submit" class="login-btn">LOG IN</button>
            </form>

            <div class="auth-switch">
                Don't have an account yet? <a href="signup.php">Sign up.</a>
            </div>

            <div class="footer-text">
                By signing in to Webibo, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.<br>
            </div>
        </div>
    </div>
</body>
</html>

