<?php
/**
 * Authentication Controller - User Registration
 * 
 * Handles user registration form submissions
 * Validates input, checks for existing users, creates new accounts
 * Sends email verification link using PHPMailer
 * NO HTML OUTPUT - Pure logic and redirection
 */

// Load helper functions
require_once __DIR__ . '/../core/functions.php';

// Load database connection
$pdo = require_once __DIR__ . '/../core/db_connect.php';

// Load required dependencies
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/models/UserModel.php';
require_once __DIR__ . '/../core/models/UserModel.php';
require_once __DIR__ . '/../core/models/TokenModel.php';
require_once __DIR__ . '/../core/helpers/email_helper.php';
require_once __DIR__ . '/../core/services/AchievementService.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../views/signup.php');
}

// Start session for storing messages
start_session_securely();

// Retrieve and sanitize form data
$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validation: Check if all fields are filled
if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($password) || empty($confirmPassword)) {
    set_error('Please fill in all fields');
    redirect('../views/signup.php');
}

// Validation: Check email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_error('Please enter a valid email address');
    redirect('../views/signup.php');
}

// Validation: Check if passwords match
if ($password !== $confirmPassword) {
    set_error('Passwords do not match');
    redirect('../views/signup.php');
}

// Validation: Check password length (minimum 6 characters)
if (strlen($password) < 6) {
    set_error('Password must be at least 6 characters long');
    redirect('../views/signup.php');
}

// Validation: Check if email is already taken
if (UserModel::isEmailTaken($pdo, $email)) {
    set_error('This email is already registered');
    redirect('../views/signup.php');
}

// Validation: Check if username is already taken
if (UserModel::isUsernameTaken($pdo, $username)) {
    set_error('This username is already taken');
    redirect('../views/signup.php');
}

// All validation passed - Create the user
$userId = UserModel::createUser($pdo, $firstName, $lastName, $email, $username, $password);

if (!$userId) {
    set_error('An error occurred during registration. Please try again.');
    redirect('../views/signup.php');
}

// Award "Hello, World!" achievement for registration
AchievementService::checkAchievementsOnEvent($pdo, $userId, 'user_registered');

// DEVELOPMENT MODE: Auto-verify users (skip email verification)
$appEnv = $_ENV['APP_ENV'] ?? 'production';
if ($appEnv === 'development') {
    // Auto-verify the user
    $stmt = $pdo->prepare("UPDATE users SET is_verified = TRUE WHERE id = ?");
    $stmt->execute([$userId]);
    
    set_success('Registration successful! Account auto-verified (dev mode). You can now log in.');
    redirect('../views/login.php');
}

// PRODUCTION MODE: Send verification email
// User created successfully - Generate verification token
$verificationToken = TokenModel::createVerificationToken($pdo, $userId);

if (!$verificationToken) {
    set_error('An error occurred creating verification token. Please try again.');
    redirect('../views/signup.php');
}

// Send verification email
$emailSent = sendVerificationEmail($email, $firstName, $verificationToken);

if (!$emailSent) {
    // Log the error but don't block registration
    error_log("Failed to send verification email to: $email");
    set_error('Account created but verification email failed to send. Please contact support.');
}

// Store minimal data in session for the "check email" page
$_SESSION['email'] = $email;
$_SESSION['first_name'] = $firstName;

// Set success message
set_success('Registration successful! Please check your email to verify your account.');

// Redirect to "check your email" page
redirect('../views/otp.php');
