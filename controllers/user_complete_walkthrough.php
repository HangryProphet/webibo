<?php
/**
 * User Walkthrough Completion Controller
 * 
 * Handles AJAX request to mark the onboarding tour as completed
 */

// Load helper functions
require_once __DIR__ . '/../core/functions.php';

// Only process POST requests from logged-in users
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(['success' => false, 'error' => 'Invalid request method'], 405);
}

start_session_securely();
if (!is_logged_in()) {
    send_json_response(['success' => false, 'error' => 'Authentication required'], 401);
}

// Load database connection and model
$pdo = require_once __DIR__ . '/../core/db_connect.php';
require_once __DIR__ . '/../core/models/UserModel.php';

$userId = get_current_user_id();

// Mark walkthrough as completed
$success = UserModel::completeWalkthrough($pdo, $userId);

if ($success) {
    // Also update session to prevent tour showing again in the same session without reload
    $_SESSION['has_walkthrough_completed'] = true;
    send_json_response(['success' => true]);
} else {
    send_json_response(['success' => false, 'error' => 'Database update failed'], 500);
}
