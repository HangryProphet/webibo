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
$hours_learning = $_SESSION['hours_learning'] ?? 42;
$lessons_completed = $_SESSION['lessons_completed'] ?? 18;
$challenges_completed = $_SESSION['challenges_completed'] ?? 12;

// Sample recent activities (in a real app, this would come from a database)
$recent_activities = [
    ['type' => 'badge', 'text' => 'Earned a 3 Day Streak badge', 'icon' => 'fa-fire', 'time' => '2 hours ago'],
    ['type' => 'stage', 'text' => 'Completed Stage 3', 'icon' => 'fa-check-circle', 'time' => '5 hours ago'],
    ['type' => 'lesson', 'text' => 'Completed HTML Basics lesson', 'icon' => 'fa-book', 'time' => '1 day ago'],
    ['type' => 'badge', 'text' => 'Earned Early Bird badge', 'icon' => 'fa-sun', 'time' => '2 days ago'],
    ['type' => 'stage', 'text' => 'Completed Stage 2', 'icon' => 'fa-check-circle', 'time' => '3 days ago'],
    ['type' => 'milestone', 'text' => 'Reached 7000 XP milestone', 'icon' => 'fa-bolt', 'time' => '4 days ago'],
];
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

            <div class="stat-card">
                <div class="stat-icon clock">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $hours_learning; ?></div>
                    <div class="stat-label">Hours spent learning</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon book">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $lessons_completed; ?></div>
                    <div class="stat-label">Lessons completed</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon trophy">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-value"><?php echo $challenges_completed; ?></div>
                    <div class="stat-label">Challenges completed</div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <h2 class="section-title">Recent Activity</h2>
        <div class="activity-list">
            <?php foreach ($recent_activities as $activity): ?>
                <div class="activity-item">
                    <div class="activity-icon <?php echo $activity['type']; ?>">
                        <i class="fas <?php echo $activity['icon']; ?>"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text"><?php echo htmlspecialchars($activity['text']); ?></div>
                        <div class="activity-time"><?php echo htmlspecialchars($activity['time']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>