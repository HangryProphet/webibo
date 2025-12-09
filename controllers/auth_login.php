<?php
/**
 * Authentication Controller - User Login
 * 
 * Handles user login form submissions
 * Validates credentials, verifies user, manages sessions
 * NO HTML OUTPUT - Pure logic and redirection
 */

// Load helper functions
require_once __DIR__ . '/../core/functions.php';

// Load database connection
$pdo = require_once __DIR__ . '/../core/db_connect.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/login.php');
}

// Start session for storing messages and user data
start_session_securely();

// Load the User Model
require_once __DIR__ . '/../core/models/UserModel.php';

// Retrieve and sanitize form data
$identifier = trim($_POST['username'] ?? ''); // Can be username or email
$password = $_POST['password'] ?? '';

error_log("=== LOGIN ATTEMPT START ===");
error_log("Identifier: $identifier");
error_log("Password length: " . strlen($password));

// Validation: Check if fields are filled
if (empty($identifier) || empty($password)) {
    error_log("Login failed: Empty fields");
    set_error('Please fill in all fields');
    redirect('../views/login.php');
}

// Attempt to get user by username or email
$user = UserModel::getUserByUsernameOrEmail($pdo, $identifier);

// Check if user exists
if (!$user) {
    error_log("Login failed: User not found for identifier: $identifier");
    set_error('Invalid username/email or password');
    redirect('../views/login.php');
}

// Debug logging
error_log("Login attempt - User found: " . $user['username']);
error_log("Password from DB (first 20 chars): " . substr($user['password_hash'], 0, 20));
error_log("Is verified: " . ($user['is_verified'] ? 'true' : 'false'));

// Verify password using secure password_verify()
if (!password_verify($password, $user['password_hash'])) {
    error_log("Login failed: Password verification failed for user: " . $user['username']);
    set_error('Invalid username/email or password');
    redirect('../views/login.php');
}

// CRITICAL: Check if user's email is verified
if (!$user['is_verified']) {
    set_error('Your account is not verified. Please check your email for the verification link.');
    redirect('../views/login.php');
}

// Login successful - Set session variables
$_SESSION['user'] = $user['username'];
$_SESSION['user_id'] = $user['id'];
$_SESSION['first_name'] = $user['first_name'];
$_SESSION['last_name'] = $user['last_name'];
$_SESSION['email'] = $user['email'];

// Initialize game stats for new session
$_SESSION['hearts'] = $_SESSION['hearts'] ?? 10;
$_SESSION['current_question'] = $_SESSION['current_question'] ?? 1;

// Set success message
set_success('Login successful! Welcome back, ' . $user['first_name'] . '!');

// Redirect to dashboard
redirect('../views/dashboard.php');
