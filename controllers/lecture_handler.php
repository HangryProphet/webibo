<?php
session_start();

// Security check - user must be logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../views/login.php");
    exit;
}

// Database connection
require_once __DIR__ . '/../core/db_connect.php';
require_once __DIR__ . '/../core/models/LevelModel.php';

// Get level ID from URL
$levelId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($levelId <= 0) {
    die("Invalid level ID");
}

// Fetch level metadata from database
$level = LevelModel::getLevelById($pdo, $levelId);

if (!$level) {
    die("Level not found");
}

// Verify this is a lecture level
if ($level['level_type'] !== 'lecture') {
    die("Invalid level type. Expected 'lecture', got '{$level['level_type']}'");
}

// Read the HTML content file
$lectureHtml = LevelModel::readContentFile($levelId, 'lecture');

if ($lectureHtml === false) {
    die("Failed to load lecture content for level {$levelId}");
}

// Get user stats for hearts display
require_once __DIR__ . '/../core/models/StatsModel.php';
$userId = $_SESSION['user_id'];
$userStats = StatsModel::getStatsByUserId($pdo, $userId);

if ($userStats === false || !is_array($userStats)) {
    die("Failed to load user stats");
}

$currentHearts = $userStats['hearts']['current'] ?? 10;

// Calculate progress (simplified - based on level order)
$totalLevels = 7; // From migration
$currentProgress = ($level['order_in_course'] / $totalLevels) * 10; // Scale to 0-10

// Determine next level URL for redirect after completion
$nextLevel = LevelModel::getNextLevel($pdo, $levelId);
if ($nextLevel) {
    if ($nextLevel['level_type'] === 'lecture') {
        $redirectUrl = "lecture.php?id={$nextLevel['id']}";
    } else {
        $redirectUrl = "activity.php?id={$nextLevel['id']}";
    }
} else {
    $redirectUrl = "dashboard.php";
}

// Make data available to view
// The view will need: $lectureHtml, $currentHearts, $currentProgress, $level, $redirectUrl
// No HTML output here - this is pure controller logic
// The view template will be required separately
