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

// Handle POST request (lecture completion)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete'])) {
    $levelId = isset($_POST['level_id']) ? (int)$_POST['level_id'] : 0;
    
    if ($levelId > 0) {
        require_once __DIR__ . '/../core/models/ProgressModel.php';
        require_once __DIR__ . '/../core/models/StatsModel.php';
        
        $userId = $_SESSION['user_id'];
        
        // Get level for XP reward
        $level = LevelModel::getLevelById($pdo, $levelId);
        if ($level) {
            // Award XP
            $xpReward = $level['xp_reward'] ?? 10;
            StatsModel::addXP($pdo, $userId, $xpReward);
            
            // Mark level as complete
            $completionSuccess = ProgressModel::completeLevel($pdo, $userId, $levelId);
            
            // Award achievements for level completion
            if ($completionSuccess) {
                require_once __DIR__ . '/../core/services/AchievementService.php';
                $awardedAchievements = AchievementService::checkAchievementsOnEvent(
                    $pdo, 
                    $userId, 
                    'level_completed', 
                    ['level_id' => $levelId]
                );
            }
        }
        
        // Check if user wants to go to dashboard instead of next level
        $redirectTo = $_POST['redirect_to'] ?? 'next';
        
        if ($redirectTo === 'dashboard') {
            // User chose to go back to dashboard
            header("Location: dashboard.php");
            exit;
        }
        
        // Redirect to next level or dashboard
        $nextLevel = LevelModel::getNextLevel($pdo, $levelId);
        if ($nextLevel) {
            if ($nextLevel['level_type'] === 'lecture') {
                header("Location: lecture.php?id={$nextLevel['id']}");
            } else {
                header("Location: activity.php?id={$nextLevel['id']}");
            }
        } else {
            header("Location: dashboard.php");
        }
        exit;
    }
}

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

// Determine next level URL and data for redirect after completion
$nextLevel = LevelModel::getNextLevel($pdo, $levelId);
$hasNextLevel = ($nextLevel !== false);
$nextLevelUrl = '';
if ($hasNextLevel) {
    if ($nextLevel['level_type'] === 'lecture') {
        $nextLevelUrl = "lecture.php?id={$nextLevel['id']}";
    } else {
        $nextLevelUrl = "activity.php?id={$nextLevel['id']}";
    }
}
$redirectUrl = $hasNextLevel ? $nextLevelUrl : "dashboard.php";

// Make data available to view
// The view will need: $lectureHtml, $currentHearts, $currentProgress, $level, $redirectUrl, $hasNextLevel, $nextLevelUrl
// No HTML output here - this is pure controller logic
// The view template will be required separately
