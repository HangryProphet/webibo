<?php
/**
 * Achievements Controller
 * 
 * Fetches all achievements and user's earned achievements
 * Prepares data for the achievements view
 */

session_start();

// Security check - user must be logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Database connection
require_once __DIR__ . '/../core/db_connect.php';
require_once __DIR__ . '/../core/models/AchievementModel.php';
require_once __DIR__ . '/../core/models/StatsModel.php';

// Get user ID
$userId = $_SESSION['user_id'];

// Fetch all achievements
$allAchievements = AchievementModel::getAllAchievements($pdo);

if ($allAchievements === false) {
    die("Failed to load achievements");
}

// Fetch user's earned achievements
$earnedAchievements = AchievementModel::getUserAchievements($pdo, $userId);

if ($earnedAchievements === false) {
    $earnedAchievements = [];
}

// Create a map of earned achievement IDs for quick lookup
$earnedIds = array_column($earnedAchievements, 'id');

// Get user stats for header
$userStats = StatsModel::getStatsByUserId($pdo, $userId);
$currentHearts = $userStats['hearts']['current'] ?? 10;
$totalXP = $userStats['xp'] ?? 0;
$currentStreak = $userStats['streak']['current'] ?? 0;

// Prepare achievements data with earned status
$achievementsData = [];

foreach ($allAchievements as $achievement) {
    $achievementsData[] = [
        'id' => $achievement['id'],
        'title' => $achievement['title'],
        'description' => $achievement['description'],
        'icon_path' => $achievement['icon_path'],
        'is_earned' => in_array($achievement['id'], $earnedIds),
        'earned_at' => null // Will be filled if earned
    ];
}

// Fill in earned_at dates for earned achievements
foreach ($achievementsData as &$achievement) {
    if ($achievement['is_earned']) {
        foreach ($earnedAchievements as $earned) {
            if ($earned['id'] == $achievement['id']) {
                $achievement['earned_at'] = $earned['earned_at'];
                break;
            }
        }
    }
}

// Calculate stats
$totalAchievements = count($allAchievements);
$earnedCount = count($earnedIds);
$completionPercentage = $totalAchievements > 0 ? round(($earnedCount / $totalAchievements) * 100) : 0;

// Variables available to the view:
// - $achievementsData (array of all achievements with earned status)
// - $totalAchievements (int)
// - $earnedCount (int)
// - $completionPercentage (int)
// - $currentHearts (int)
// - $totalXP (int)
// - $currentStreak (int)
