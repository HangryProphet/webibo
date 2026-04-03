<?php
/**
 * Reset Password Handler
 * 
 * Validates token and updates user password
 */

require_once __DIR__ . '/../core/db_connect.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../core/models/UserModel.php';
require_once __DIR__ . '/../core/models/TokenModel.php';

start_session_securely();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/views/login.php');
    exit;
}

// Get form data
$token = trim($_POST['token'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validate inputs
if (empty($token)) {
    set_error('Invalid or missing reset token.');
    redirect('/views/login.php');
    exit;
}

if (empty($password) || empty($confirmPassword)) {
    set_error('All fields are required.');
    redirect('/views/reset_password.php?token=' . urlencode($token));
    exit;
}

// Validate password length
if (strlen($password) < 8) {
    set_error('Password must be at least 8 characters long.');
    redirect('/views/reset_password.php?token=' . urlencode($token));
    exit;
}

// Check if passwords match
if ($password !== $confirmPassword) {
    set_error('Passwords do not match.');
    redirect('/views/reset_password.php?token=' . urlencode($token));
    exit;
}

try {
    // Validate the reset token
    $userId = TokenModel::validatePasswordResetToken($pdo, $token);
    
    if (!$userId) {
        set_error('Invalid or expired reset token. Please request a new password reset.');
        redirect('/views/forgot_password.php');
        exit;
    }
    
    // Update the user's password
    if (UserModel::updatePassword($pdo, $userId, $password)) {
        // Delete the used token
        TokenModel::deletePasswordResetToken($pdo, $userId);
        
        set_success('Password reset successfully! You can now log in with your new password.');
        redirect('/views/login.php');
        exit;
    } else {
        set_error('Failed to update password. Please try again.');
        redirect('/views/reset_password.php?token=' . urlencode($token));
        exit;
    }
    
} catch (PDOException $e) {
    error_log("Reset Password Error: " . $e->getMessage());
    set_error('An error occurred. Please try again later.');
    redirect('/views/reset_password.php?token=' . urlencode($token));
    exit;
}
