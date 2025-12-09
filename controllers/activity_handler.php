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
require_once __DIR__ . '/../core/models/StatsModel.php';
require_once __DIR__ . '/../core/models/ProgressModel.php';
require_once __DIR__ . '/../core/services/AchievementService.php';

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

// Verify this is NOT a lecture level
if ($level['level_type'] === 'lecture') {
    die("Invalid level type. This handler is for activities only.");
}

// Get user ID and stats
$userId = $_SESSION['user_id'];
$userStats = StatsModel::getStatsByUserId($pdo, $userId);

if ($userStats === false) {
    die("Failed to load user stats");
}

$currentHearts = $userStats['hearts']['current'] ?? 10;

// Calculate progress (simplified - based on level order)
$totalLevels = 7; // From migration
$currentProgress = ($level['order_in_course'] - 1); // Progress out of 10 (0-10 scale)

// ============================================
// HANDLE POST REQUEST (Answer Submission)
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read activity content to get correct answer
    $activityContent = LevelModel::readContentFile($levelId, $level['level_type'], true);
    
    if (!$activityContent) {
        die("Failed to load activity content");
    }
    
    $correctAnswer = $activityContent['correct_answer'] ?? $activityContent['correct_code'] ?? '';
    $enemyHP = isset($activityContent['enemy']) ? ($activityContent['enemy']['hp'] ?? 10) : 10;
    
    // Get submitted answer
    $submittedAnswer = '';
    
    if (isset($_POST['answer'])) {
        $submittedAnswer = trim($_POST['answer']);
    } elseif (isset($_POST['code'])) {
        $submittedAnswer = trim($_POST['code']);
    }
    
    // Handle SKIP action
    if (isset($_POST['skip'])) {
        // Decrease hearts for skipping
        $currentHearts = max(0, $currentHearts - 1);
        $_SESSION['hearts'] = $currentHearts;
        
        if ($currentHearts <= 0) {
            header("Location: gameover.php");
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
    
    // Validate answer
    $isCorrect = false;
    
    // Different validation based on activity type
    if ($level['level_type'] === 'multiple-choice') {
        $isCorrect = ($submittedAnswer === $correctAnswer);
    } elseif ($level['level_type'] === 'fill-blank') {
        $isCorrect = (strtolower($submittedAnswer) === strtolower($correctAnswer));
    } elseif ($level['level_type'] === 'code-editor') {
        // For code editor, use the validation rules from JSON
        $validation = $activityContent['validation'] ?? [];
        $userCode = $submittedAnswer;
        $expectedCode = $correctAnswer;
        
        if (isset($validation['ignore_whitespace']) && $validation['ignore_whitespace']) {
            $userCode = preg_replace('/\s+/', '', $userCode);
            $expectedCode = preg_replace('/\s+/', '', $expectedCode);
        }
        
        if (isset($validation['case_sensitive']) && !$validation['case_sensitive']) {
            $userCode = strtolower($userCode);
            $expectedCode = strtolower($expectedCode);
        }
        
        $isCorrect = ($userCode === $expectedCode);
    }
    
    if ($isCorrect) {
        // CORRECT ANSWER
        // Award XP
        $xpReward = $level['xp_reward'] ?? 10;
        StatsModel::addXP($pdo, $userId, $xpReward);
        
        // Mark level as complete
        ProgressModel::completeLevel($pdo, $userId, $levelId);
        
        // Check and award achievements
        AchievementService::checkAchievementsOnEvent($pdo, $userId, 'level_completed', [
            'level_id' => $levelId
        ]);
        
        // Redirect to next level or dashboard
        $nextLevel = LevelModel::getNextLevel($pdo, $levelId);
        if ($nextLevel) {
            // Determine next handler based on level type
            if ($nextLevel['level_type'] === 'lecture') {
                header("Location: lecture.php?id={$nextLevel['id']}");
            } else {
                header("Location: activity.php?id={$nextLevel['id']}");
            }
        } else {
            header("Location: dashboard.php");
        }
        exit;
        
    } else {
        // WRONG ANSWER
        // Decrease hearts
        $currentHearts = max(0, $currentHearts - 1);
        $_SESSION['hearts'] = $currentHearts;
        
        if ($currentHearts <= 0) {
            header("Location: gameover.php");
            exit;
        }
        
        // Reload the same activity (JavaScript will show feedback)
        // We don't redirect - let the JavaScript handle the feedback display
        // The page will reload with the wrong answer state
    }
}

// ============================================
// HANDLE GET REQUEST (Load Activity)
// ============================================

// Read activity content from JSON file
$activityContent = LevelModel::readContentFile($levelId, $level['level_type'], true);

if (!$activityContent) {
    die("Failed to load activity content for level {$levelId}");
}

// Determine next level URL for redirect after completion
$nextLevel = LevelModel::getNextLevel($pdo, $levelId);
$redirectUrl = '';
if ($nextLevel) {
    if ($nextLevel['level_type'] === 'lecture') {
        $redirectUrl = "lecture.php?id={$nextLevel['id']}";
    } else {
        $redirectUrl = "activity.php?id={$nextLevel['id']}";
    }
} else {
    $redirectUrl = "dashboard.php";
}

// ============================================
// BUILD ACTIVITY DATA ARRAY
// This must match window.activityConfig exactly!
// ============================================

$activity_data = [
    'type' => $level['level_type'],
    'correctAnswer' => $activityContent['correct_answer'] ?? $activityContent['correct_code'] ?? '',
    'currentHearts' => $currentHearts,
    'currentProgress' => $currentProgress,
    'redirectUrl' => $redirectUrl,
    'enemyHP' => 10,
    'hasEnemy' => isset($activityContent['enemy']) && $activityContent['enemy'] !== null,
    'correctTitle' => $activityContent['feedback']['correct']['title'] ?? 'Correct!',
    'correctDetails' => $activityContent['feedback']['correct']['details'] ?? '',
    'wrongTitle' => $activityContent['feedback']['wrong']['title'] ?? 'Not quite!',
    'wrongDetails' => $activityContent['feedback']['wrong']['details'] ?? '',
    'validation' => $activityContent['validation'] ?? null
];

// Update enemy HP if enemy exists
if ($activity_data['hasEnemy'] && isset($activityContent['enemy']['hp'])) {
    $activity_data['enemyHP'] = $activityContent['enemy']['hp'];
}

// Extract other activity-specific data for the view
$question = $activityContent['question'] ?? $activityContent['instruction'] ?? '';
$options = $activityContent['options'] ?? [];
$enemy = $activityContent['enemy'] ?? null;
$codeTemplate = $activityContent['code_template'] ?? '';
$starterCode = $activityContent['starter_code'] ?? '';
$hint = $activityContent['hint'] ?? '';
$teacher = $activityContent['teacher'] ?? null;

// Controller logic complete - all data prepared for view
// No HTML output here - this is pure controller logic
// The view template will be required separately

