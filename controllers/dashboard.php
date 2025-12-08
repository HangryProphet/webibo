<?php
/**
 * Dashboard Controller
 * 
 * Prepares data for the dashboard view
 * Loads user stats and progress from mock models
 */

// Load helper functions
require_once __DIR__ . '/../core/functions.php';

// Require user to be logged in
require_login();

// Load required models
require_once __DIR__ . '/../core/models/StatsModel.php';
require_once __DIR__ . '/../core/models/ProgressModel.php';

// Get user ID from session
$userId = get_current_user_id();

// Get user data from session
$username = get_current_username() ?? 'User';
$firstName = $_SESSION['first_name'] ?? 'User';
$lastName = $_SESSION['last_name'] ?? '';

// Fetch user statistics from model
$userStats = StatsModel::getStatsByUserId($userId);

// Fetch user progress from model
$userProgress = ProgressModel::getProgressByUserId($userId);

// Handle case where user data is not found
if (!$userStats) {
    // Use default stats if not found
    $userStats = [
        "hearts" => ["current" => 10, "max" => 10, "next_heart_in_seconds" => 7200],
        "streak" => ["current_days" => 0, "reset_in_seconds" => 0, "weekly_progress" => [false, false, false, false, false, false, false], "target_days" => 30],
        "courses" => []
    ];
}

if (!$userProgress) {
    // Use empty progress if not found
    $userProgress = [];
}