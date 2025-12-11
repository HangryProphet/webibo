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
    
    // Handle multi-question format
    $currentQuestionData = $activityContent;
    if (isset($activityContent['questions']) && is_array($activityContent['questions']) && !empty($activityContent['questions'])) {
        $currentQuestionData = $activityContent['questions'][0];
        if (isset($activityContent['enemy'])) {
            $currentQuestionData['enemy'] = $activityContent['enemy'];
        }
    }
    
    $correctAnswer = $currentQuestionData['correct_answer'] ?? $currentQuestionData['correct_code'] ?? '';
    $enemyHP = isset($currentQuestionData['enemy']) ? ($currentQuestionData['enemy']['hp'] ?? 10) : 10;
    
    // Check if this is a level completion request (from victory modal)
    $isCompletionRequest = isset($_POST['complete_level']) && $_POST['complete_level'] == '1';
    
    // DEBUG: Log completion request
    error_log("Activity Handler - Level $levelId: complete_level flag = " . ($_POST['complete_level'] ?? 'NOT SET'));
    error_log("Activity Handler - isCompletionRequest = " . ($isCompletionRequest ? 'TRUE' : 'FALSE'));
    
    if ($isCompletionRequest) {
        error_log("Activity Handler - Processing completion for Level $levelId, User $userId");
        
        // Level already completed client-side, just mark as complete and redirect
        $xpReward = $level['xp_reward'] ?? 10;
        StatsModel::addXP($pdo, $userId, $xpReward);
        $completionResult = ProgressModel::completeLevel($pdo, $userId, $levelId);
        
        error_log("Activity Handler - ProgressModel::completeLevel result: " . ($completionResult ? 'SUCCESS' : 'FAILED'));
        
        // Award achievements
        require_once __DIR__ . '/../core/services/AchievementService.php';
        AchievementService::checkAchievementsOnEvent($pdo, $userId, 'level_completed', ['level_id' => $levelId]);
        
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
    
    // Get submitted answer
    $submittedAnswer = '';
    
    if (isset($_POST['answer'])) {
        $submittedAnswer = trim($_POST['answer']);
    } elseif (isset($_POST['code'])) {
        $submittedAnswer = trim($_POST['code']);
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
        $validation = $currentQuestionData['validation'] ?? [];
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
            
            // Log awarded achievements for debugging
            if (!empty($awardedAchievements)) {
                error_log("User {$userId} earned achievements: " . implode(', ', $awardedAchievements));
            }
        }
        
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

// ============================================
// HANDLE MULTI-QUESTION FORMAT
// ============================================
// Check if this is a multi-question quiz (has "questions" array)
$isMultiQuestion = isset($activityContent['questions']) && is_array($activityContent['questions']);
$currentQuestionData = $activityContent;

if ($isMultiQuestion) {
    // For now, just use the first question
    // TODO: In future, implement multi-question progression
    if (!empty($activityContent['questions'])) {
        $currentQuestionData = $activityContent['questions'][0];
        // Keep the enemy data from the root level
        if (isset($activityContent['enemy'])) {
            $currentQuestionData['enemy'] = $activityContent['enemy'];
        }
    }
}

// Always redirect to dashboard after level completion
$redirectUrl = 'dashboard.php';

// ============================================
// BUILD ACTIVITY DATA ARRAY
// This must match window.activityConfig exactly!
// ============================================

// Calculate adaptive enemy HP (2 HP per question)
$totalQuestions = $isMultiQuestion ? count($activityContent['questions']) : 1;
$adaptiveEnemyHP = $totalQuestions * 2; // 2 HP damage per correct answer

// Get next level information
$nextLevel = LevelModel::getNextLevel($pdo, $levelId);
$hasNextLevel = ($nextLevel !== false);
$nextLevelUrl = '';
if ($hasNextLevel) {
    if ($nextLevel['level_type'] === 'lecture') {
        $nextLevelUrl = 'lecture.php?id=' . $nextLevel['id'];
    } else {
        $nextLevelUrl = 'activity.php?id=' . $nextLevel['id'];
    }
}

$activity_data = [
    'type' => $level['level_type'],
    'question' => $currentQuestionData['question'] ?? $currentQuestionData['instruction'] ?? '',
    'options' => $currentQuestionData['options'] ?? [],
    'correctAnswer' => $currentQuestionData['correct_answer'] ?? $currentQuestionData['correct_code'] ?? '',
    'currentHearts' => $currentHearts,
    'currentProgress' => $currentProgress,
    'redirectUrl' => $redirectUrl,
    'enemyHP' => $adaptiveEnemyHP,
    'maxEnemyHP' => $adaptiveEnemyHP,
    'hasEnemy' => isset($currentQuestionData['enemy']) && $currentQuestionData['enemy'] !== null,
    'enemy' => $currentQuestionData['enemy'] ?? null,
    'correctTitle' => ($currentQuestionData['feedback']['correct']['title'] ?? null) ?: 'Correct!',
    'correctDetails' => ($currentQuestionData['feedback']['correct']['details'] ?? null) ?: '',
    'wrongTitle' => ($currentQuestionData['feedback']['wrong']['title'] ?? null) ?: 'Not quite!',
    'wrongDetails' => ($currentQuestionData['feedback']['wrong']['details'] ?? null) ?: '',
    'validation' => $currentQuestionData['validation'] ?? null,
    'codeTemplate' => $currentQuestionData['code_template'] ?? '',
    'starterCode' => $currentQuestionData['starter_code'] ?? '',
    'hint' => $currentQuestionData['hint'] ?? '',
    'teacher' => $currentQuestionData['teacher'] ?? null,
    // Multi-question support
    'isMultiQuestion' => $isMultiQuestion,
    'allQuestions' => $isMultiQuestion ? $activityContent['questions'] : [],
    'totalQuestions' => $totalQuestions,
    'currentQuestionIndex' => 0,
    // Next level information
    'hasNextLevel' => $hasNextLevel,
    'nextLevelUrl' => $nextLevelUrl,
    'nextLevelTitle' => $hasNextLevel ? $nextLevel['title'] : ''
];

// Extract other activity-specific data for the view
$question = $currentQuestionData['question'] ?? $currentQuestionData['instruction'] ?? '';
$options = $currentQuestionData['options'] ?? [];
$enemy = $currentQuestionData['enemy'] ?? null;
$codeTemplate = $currentQuestionData['code_template'] ?? '';
$starterCode = $currentQuestionData['starter_code'] ?? '';
$hint = $currentQuestionData['hint'] ?? '';
$teacher = $currentQuestionData['teacher'] ?? null;

// Controller logic complete - all data prepared for view
// No HTML output here - this is pure controller logic
// The view template will be required separately

