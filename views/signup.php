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
    <title>Create an account - Webibo</title>
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
            <img src="../assets/img/wiza/wiza-signup.png" alt="Wiza welcoming you to sign up" loading="lazy">
        </div>

        <div class="signup-container">
            <h1>Create an account</h1>

            <form method="POST" action="../controllers/auth_register.php">
                <div class="name-row">
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input 
                            type="text" 
                            name="first_name" 
                            placeholder="First name"
                            value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                        >
                    </div>
                    <div class="input-group">
                        <i class="fas fa-user input-icon"></i>
                        <input 
                            type="text" 
                            name="last_name" 
                            placeholder="Last name"
                            value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                        >
                    </div>
                </div>

                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input 
                        type="email" 
                        name="email" 
                        placeholder="Email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    >
                </div>

                <div class="input-group">
                    <i class="fas fa-user-circle input-icon"></i>
                    <input 
                        type="text" 
                        name="username" 
                        placeholder="Username"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                    >
                </div>

                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Password"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password', this)">SHOW</button>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input 
                        type="password" 
                        id="confirmPassword" 
                        name="confirm_password" 
                        placeholder="Confirm password"
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

                <button type="submit" class="signup-btn">SIGN UP</button>
            </form>
            
            <div class="auth-switch">
                Already have an account? <a href="login.php">Login.</a>
            </div>

            <div class="footer-text">
                By signing up to Webibo, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a>.<br>
            </div>
        </div>
    </div>

    <script src="../assets/js/password_toggle.js"></script>
</body>
</html>

