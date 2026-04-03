<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Get session data
$total_questions = $_SESSION['total_questions'] ?? 10;
$current_question = $_SESSION['current_question'] ?? $total_questions;
$hearts = $_SESSION['hearts'] ?? 10;
$initial_hearts = $_SESSION['initial_hearts'] ?? 10;
$start_time = $_SESSION['lesson_start_time'] ?? time();

// Calculate stats
$questions_answered = $current_question - 1; // Questions completed
$hearts_lost = $initial_hearts - $hearts;
$incorrect_answers = $hearts_lost; // Each wrong answer/skip loses 1 heart
$correct_answers = max(0, $questions_answered - $incorrect_answers);

// Calculate percentage correctness
$correctness_percentage = $questions_answered > 0 
    ? round(($correct_answers / $questions_answered) * 100) 
    : 100;

// Calculate time spent (in seconds)
$time_spent_seconds = time() - $start_time;
$minutes = floor($time_spent_seconds / 60);
$seconds = $time_spent_seconds % 60;
$time_display = $minutes > 0 
    ? sprintf("%d min %d sec", $minutes, $seconds)
    : sprintf("%d sec", $seconds);

// Calculate XP gained (base XP * correctness multiplier)
$base_xp = $questions_answered * 10; // 10 XP per question
$correctness_multiplier = $correctness_percentage / 100;
$xp_gained = round($base_xp * $correctness_multiplier);

// Bonus XP for perfect score
if ($correctness_percentage == 100 && $questions_answered > 0) {
    $xp_gained += 50; // Bonus 50 XP for perfect score
}

// Store completion data in session (for potential database saving)
$_SESSION['lesson_complete'] = [
    'xp_gained' => $xp_gained,
    'correctness_percentage' => $correctness_percentage,
    'time_spent' => $time_spent_seconds,
    'questions_answered' => $questions_answered,
    'correct_answers' => $correct_answers
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lesson Complete - WebQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/activity.css">
    <link rel="stylesheet" href="../assets/css/completion.css">
</head>
<body>
    <div class="main-content">
        <div class="completion-container">
            <!-- Wiza Image -->
            <img src="../assets/img/wiza/wiza-heart-eyes.png" alt="Wiza Happy" class="completion-image">

            <!-- Title -->
            <h1 class="completion-title">Lesson Complete!</h1>

            <!-- Message -->
            <p class="completion-message">
                Excellent work! You've successfully completed this lesson. Your dedication and effort have paid off. 
                Keep up the amazing progress and continue your learning journey!
            </p>

            <!-- Stats -->
            <div class="stats-container">
                <!-- XP Gained -->
                <div class="stat-card xp">
                    <div class="stat-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-value" id="xpValue"><?php echo $xp_gained; ?></div>
                    <div class="stat-label">XP Gained</div>
                </div>

                <!-- Accuracy -->
                <div class="stat-card accuracy">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value" id="accuracyValue"><?php echo $correctness_percentage; ?>%</div>
                    <div class="stat-label">Accuracy</div>
                </div>

                <!-- Time Spent -->
                <div class="stat-card time">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value" id="timeValue"><?php echo $time_display; ?></div>
                    <div class="stat-label">Time Spent</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="dashboard.php" class="action-btn action-btn-secondary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="#" class="action-btn action-btn-primary" onclick="proceedToNextLesson(event)">
                    <i class="fas fa-arrow-right"></i> Next Lesson
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/completion.js"></script>
</body>
</html>

