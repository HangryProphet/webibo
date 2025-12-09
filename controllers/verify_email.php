<?php
/**
 * Email Verification Controller
 * 
 * Handles email verification link clicks
 * Validates token and activates user account
 */

// Load helper functions
require_once __DIR__ . '/../core/functions.php';

// Load database connection
$pdo = require_once __DIR__ . '/../core/db_connect.php';

// Load required models
require_once __DIR__ . '/../core/models/TokenModel.php';
require_once __DIR__ . '/../core/services/AchievementService.php';

// Start session for messages
start_session_securely();

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    set_error('Invalid verification link. Please check your email and try again.');
    redirect('../views/login.php');
}

// Validate the token
$userId = TokenModel::validateAndGetUserId($pdo, $token);

if ($userId) {
    // Token is valid - award "Verified!" achievement
    AchievementService::checkAchievementsOnEvent($pdo, $userId, 'email_verified');
    
    // User is now verified
    set_success('Email verified successfully! You can now log in to your account.');
    redirect('../views/login.php');
} else {
    // Token is invalid or expired
    set_error('Verification link is invalid or has expired. Please contact support or register again.');
    redirect('../views/login.php');
}
