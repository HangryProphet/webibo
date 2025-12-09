<?php
/**
 * Forgot Password Handler
 * 
 * Handles password reset link generation and email sending
 */

require_once __DIR__ . '/../core/db_connect.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../core/models/UserModel.php';
require_once __DIR__ . '/../core/models/TokenModel.php';
require_once __DIR__ . '/../core/helpers/email_helper.php';

start_session_securely();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/views/forgot_password.php');
    exit;
}

// Get and validate email
$email = trim($_POST['email'] ?? '');

if (empty($email)) {
    set_error('Email address is required.');
    redirect('/views/forgot_password.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_error('Please enter a valid email address.');
    redirect('/views/forgot_password.php');
    exit;
}

try {
    // Check if user exists with this email
    $user = UserModel::getUserByUsernameOrEmail($pdo, $email);
    
    // Always show success message for security (don't reveal if email exists)
    // But only send email if user actually exists
    if ($user) {
        // Create password reset token
        $token = TokenModel::createPasswordResetToken($pdo, $email);
        
        if (!empty($token)) {
            // Check if running on localhost (dev mode)
            $isLocalhost = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1');
            
            if ($isLocalhost) {
                // DEV MODE: Show reset link directly
                $resetLink = "http://localhost/Webibo/views/reset_password.php?token=" . urlencode($token);
                set_success("DEV MODE: Copy this link to reset password: <a href='$resetLink' style='color: #1cb0f6;'>$resetLink</a>");
            } else {
                // PRODUCTION MODE: Send email
                $resetLink = "https://yourdomain.com/views/reset_password.php?token=" . urlencode($token);
                
                $emailSent = sendPasswordResetEmail(
                    $email,
                    $resetLink,
                    $user['first_name'] ?? 'User'
                );
                
                if ($emailSent) {
                    set_success('If an account exists with this email, you will receive a password reset link shortly.');
                } else {
                    error_log("Failed to send password reset email to: " . $email);
                    set_success('If an account exists with this email, you will receive a password reset link shortly.');
                }
            }
        } else {
            error_log("Failed to create password reset token for: " . $email);
            set_error('An error occurred. Please try again later.');
            redirect('/views/forgot_password.php');
            exit;
        }
    } else {
        // User doesn't exist, but show success message anyway (security)
        set_success('If an account exists with this email, you will receive a password reset link shortly.');
    }
    
    redirect('/views/forgot_password.php');
    exit;
    
} catch (PDOException $e) {
    error_log("Forgot Password Error: " . $e->getMessage());
    set_error('An error occurred. Please try again later.');
    redirect('/views/forgot_password.php');
    exit;
}
