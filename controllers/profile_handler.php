<?php
/**
 * Profile Handler - Controller for User Profile Page
 * 
 * Fetches and prepares all data needed for the profile page display
 * Phase 1: Read-only dynamic data
 * Phase 2: Profile editing and avatar upload
 */

// Include dependencies
require_once __DIR__ . '/../core/db_connect.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../core/models/UserModel.php';
require_once __DIR__ . '/../core/models/StatsModel.php';
require_once __DIR__ . '/../core/models/ProgressModel.php';

// Require user to be logged in
require_login();

// Get current user ID
$userId = get_current_user_id();

try {
    // Fetch user data
    $user = UserModel::getUserById($pdo, $userId);
    
    if (!$user) {
        set_error("Unable to load profile data.");
        redirect('/views/dashboard.php');
        exit;
    }
    
    // Fetch user stats
    $stats = StatsModel::getStatsByUserId($pdo, $userId);
    
    if (!$stats) {
        // Initialize default stats if not found
        $stats = [
            'xp_points' => 0,
            'current_streak' => 0,
            'total_hours' => 0
        ];
    }
    
    // Count completed levels
    $completedLevels = ProgressModel::countCompletedLevels($pdo, $userId);
    
    // Get achievement/badge count
    require_once __DIR__ . '/../core/models/AchievementModel.php';
    $badgeCount = AchievementModel::getAchievementCount($pdo, $userId);
    
    // Get recent activity (last 5 completed levels)
    $recentActivity = ProgressModel::getRecentActivity($pdo, $userId, 5);
    
    // Calculate level and progress from XP
    $levelData = calculateLevelProgress((int)$stats['xp_points']);
    
    // Prepare profile variables for view
    $username = sanitize_output($user['username']);
    $firstName = sanitize_output($user['first_name'] ?? '');
    $lastName = sanitize_output($user['last_name'] ?? '');
    $email = sanitize_output($user['email']);
    $avatarPath = $user['avatar_path'] ?? '../assets/img/avatars/default.png';
    $joinDate = date('F Y', strtotime($user['created_at']));
    
    // Profile stats
    $total_xp = (int)($stats['xp_points'] ?? 0);
    $day_streak = (int)($stats['streak']['current_days'] ?? 0);
    $lessons_completed = $completedLevels;
    $challenges_completed = $badgeCount; // Display badge/achievement count as "challenges completed"
    
    // Level information
    $current_level = $levelData['level'];
    $current_xp = $levelData['current_xp'];
    $next_level_xp = $levelData['next_level_xp'];
    $level_progress = $levelData['progress_percent'];
    
    // Format recent activity for display
    $activities = [];
    foreach ($recentActivity as $activity) {
        $activityType = strtolower($activity['level_type'] ?? 'lesson');
        $levelTitle = sanitize_output($activity['level_title'] ?? 'Unknown Level');
        $timeAgo = timeAgo($activity['completed_at']);
        $xpReward = (int)($activity['xp_reward'] ?? 0);
        
        $activities[] = [
            'type' => $activityType,
            'text' => "Completed {$levelTitle}",
            'time' => $timeAgo,
            'xp' => $xpReward
        ];
    }
    
    // If no recent activity, show placeholder
    if (empty($activities)) {
        $activities[] = [
            'type' => 'info',
            'text' => 'Start learning to see your activity here!',
            'time' => 'Just now',
            'xp' => 0
        ];
    }

} catch (PDOException $e) {
    error_log("Profile Handler Error: " . $e->getMessage());
    set_error("An error occurred while loading your profile.");
    redirect('/views/dashboard.php');
    exit;
}

/**
 * Helper function to calculate time ago from timestamp
 * 
 * @param string $datetime Datetime string
 * @return string Human-readable time ago
 */
function timeAgo(string $datetime): string
{
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}
