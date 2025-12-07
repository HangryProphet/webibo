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
    <title>Profile - WebQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <a href="edit_profile.php" class="edit-btn" style="text-decoration: none; display: inline-block;">
                <i class="fas fa-pencil"></i> EDIT
            </a>
            
            <div class="avatar">
                <i class="fas fa-user"></i>
            </div>
            
            <h1 class="username"><?php echo htmlspecialchars($username); ?></h1>
            <div class="user-handle"><?php echo htmlspecialchars($username); ?>14</div>
            <div class="join-date">Joined December 2024</div>
            
            <div class="tech-badges">
                <div class="tech-badge" title="HTML5">
                    <i class="fab fa-html5" style="color: #e34c26;"></i>
                </div>
                <div class="tech-badge" title="CSS3">
                    <i class="fab fa-css3-alt" style="color: #264de4;"></i>
                </div>
                <div class="tech-badge" title="JavaScript">
                    <i class="fab fa-js" style="color: #f0db4f;"></i>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <h2 class="section-title">Statistics</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon fire">
                    <i class="fas fa-fire"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $day_streak; ?></div>
                    <div class="stat-label">Day streak</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon lightning">
                    <i class="fas fa-bolt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $total_xp; ?></div>
                    <div class="stat-label">Total XP</div>
                </div>
            </div>
        </div>

        <!-- Achievements -->
        <div class="achievements-header">
            <h2 class="section-title" style="margin-bottom: 0;">Achievements</h2>
            <a href="achievements.php" class="view-all-btn" style="text-decoration: none;">VIEW ALL</a>
        </div>

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