<?php
/**
 * Dashboard Controller
 * 
 * Prepares data for the dashboard view
 * Loads user stats and progress from database models
 */

// Load helper functions
require_once __DIR__ . '/../core/functions.php';

// Require user to be logged in
require_login();

// Load database connection
$pdo = require_once __DIR__ . '/../core/db_connect.php';

// Load required models
require_once __DIR__ . '/../core/models/StatsModel.php';
require_once __DIR__ . '/../core/models/ProgressModel.php';

/**
 * Read level metadata (activity type + title) from /data/html/{id}.html|json
 * Title preference: JSON "title" > "instruction" > "question" > HTML <title>
 */
function getLevelMetadataFromData(int $levelId): array
{
    $basePath = realpath(__DIR__ . '/../data/html');
    $fallback = [
        'activity_type' => 'Lecture',
        'title' => "Level {$levelId}",
    ];

    if (!$basePath) {
        return $fallback;
    }

    $htmlPath = $basePath . DIRECTORY_SEPARATOR . "{$levelId}.html";
    $jsonPath = $basePath . DIRECTORY_SEPARATOR . "{$levelId}.json";

    if (is_readable($jsonPath)) {
        $json = json_decode(@file_get_contents($jsonPath), true) ?: [];
        $title = $json['title']
            ?? $json['instruction']
            ?? $json['question']
            ?? $fallback['title'];

        return [
            'activity_type' => 'Challenge',
            'title' => $title,
        ];
    }

    if (is_readable($htmlPath)) {
        $contents = @file_get_contents($htmlPath);
        if ($contents && preg_match('/<title>(.*?)<\/title>/i', $contents, $matches)) {
            $fallback['title'] = trim($matches[1]);
        }
        return $fallback;
    }

    return $fallback;
}

// Get user ID from session
$userId = get_current_user_id();

// Get user data from session
$username = get_current_username() ?? 'User';
$firstName = $_SESSION['first_name'] ?? 'User';
$lastName = $_SESSION['last_name'] ?? '';

// Fetch user statistics from model
$userStats = StatsModel::getStatsByUserId($pdo, $userId);

// Fetch user progress from model
$userProgress = ProgressModel::getProgressByUserId($pdo, $userId);

// Enrich progress with metadata for hover popups
if (is_array($userProgress) && !empty($userProgress)) {
    foreach ($userProgress as &$level) {
        $meta = getLevelMetadataFromData((int)($level['level_id'] ?? 0));
        $level['activity_type'] = $meta['activity_type'];
        $level['title'] = $meta['title'];

        if (!isset($level['type']) || $level['type'] === '') {
            $level['type'] = $meta['activity_type'] === 'Lecture' ? 'lecture' : 'practice';
        }
    }
    unset($level);
}

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