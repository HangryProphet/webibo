<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['user'];
$hearts = $_SESSION['hearts'] ?? 10;
$total_xp = $_SESSION['total_xp'] ?? 7305;
$day_streak = $_SESSION['day_streak'] ?? 264;

// Calculate achievements progress
$streak_progress = min(3, $day_streak);
$early_bird_achieved = $_SESSION['early_bird'] ?? false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Achievements - WebQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/achievements.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">All Achievements</h1>
        </div>

        <h2 class="section-title">Active Achievements</h2>

        <div class="achievement-card">
            <div class="achievement-icon <?php echo $streak_progress >= 3 ? 'sage' : ''; ?>">
                <i class="fas fa-fire"></i>
                <?php if ($streak_progress >= 3): ?>
                    <span class="achievement-level">✓</span>
                <?php endif; ?>
            </div>
            <div class="achievement-info">
                <div class="achievement-name">3 Day Streak</div>
                <div class="achievement-progress"><?php echo $streak_progress; ?>/3</div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill <?php echo $streak_progress >= 3 ? 'sage' : 'wildfire'; ?>" style="width: <?php echo ($streak_progress / 3 * 100); ?>%;"></div>
                </div>
                <div class="achievement-progress" style="margin-top: 8px; font-size: 13px;">Maintain a 3 day streak</div>
            </div>
        </div>

        <div class="achievement-card">
            <div class="achievement-icon <?php echo $early_bird_achieved ? 'sage' : ''; ?>">
                <i class="fas fa-sun"></i>
                <?php if ($early_bird_achieved): ?>
                    <span class="achievement-level">✓</span>
                <?php endif; ?>
            </div>
            <div class="achievement-info">
                <div class="achievement-name">Early Bird</div>
                <div class="achievement-progress"><?php echo $early_bird_achieved ? '1/1' : '0/1'; ?></div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill <?php echo $early_bird_achieved ? 'sage' : 'wildfire'; ?>" style="width: <?php echo $early_bird_achieved ? '100' : '0'; ?>%;"></div>
                </div>
                <div class="achievement-progress" style="margin-top: 8px; font-size: 13px;">Finish a lesson at 9 AM</div>
            </div>
        </div>
    </div>
</body>
</html>

