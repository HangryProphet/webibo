<?php
// Include the profile handler to fetch dynamic data
require_once __DIR__ . '/../controllers/profile_handler.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - WebQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
    <style>
        .fade-up {
            opacity: 0;
            transform: translateY(16px);
            animation: fadeUp 0.6s ease forwards;
            animation-delay: 0.05s;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container fade-up">
        <!-- Profile Header -->
        <div class="profile-header">
            <a href="edit_profile.php" class="edit-btn" style="text-decoration: none; display: inline-block;">
                <i class="fas fa-pencil"></i> EDIT
            </a>
            
            <div class="avatar">
                <?php if (!empty($avatarPath) && $avatarPath !== '/assets/img/avatars/default.png' && file_exists($_SERVER['DOCUMENT_ROOT'] . $avatarPath)): ?>
                    <img src="<?php echo htmlspecialchars($avatarPath); ?>" alt="<?php echo htmlspecialchars($username); ?>" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
            
            <h1 class="username"><?php echo htmlspecialchars($username); ?></h1>
            <div class="user-handle">@<?php echo htmlspecialchars($username); ?></div>
            <div class="join-date">Joined <?php echo htmlspecialchars($joinDate); ?></div>
            
            <!-- Level Progress Bar -->
            <div class="level-progress">
                <div class="level-progress-meta">
                    <span>Level <?php echo $current_level; ?></span>
                    <span class="level-progress-xp"><?php echo $current_xp; ?> / <?php echo $next_level_xp; ?> XP</span>
                </div>
                <div class="level-progress-track">
                    <div class="level-progress-fill" style="width: <?php echo $level_progress; ?>%;"></div>
                </div>
            </div>
            
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
            <?php foreach ($activities as $activity): ?>
                <div class="activity-item">
                    <div class="activity-icon <?php echo htmlspecialchars($activity['type']); ?>">
                        <?php
                        // Determine icon based on activity type
                        $iconClass = 'fa-book';
                        if ($activity['type'] === 'lecture') {
                            $iconClass = 'fa-chalkboard-teacher';
                        } elseif ($activity['type'] === 'practice') {
                            $iconClass = 'fa-code';
                        } elseif ($activity['type'] === 'challenge') {
                            $iconClass = 'fa-trophy';
                        } elseif ($activity['type'] === 'info') {
                            $iconClass = 'fa-info-circle';
                        }
                        ?>
                        <i class="fas <?php echo $iconClass; ?>"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">
                            <?php echo htmlspecialchars($activity['text']); ?>
                            <?php if ($activity['xp'] > 0): ?>
                                <span style="color: #4CAF50; font-weight: 600; margin-left: 8px;">+<?php echo $activity['xp']; ?> XP</span>
                            <?php endif; ?>
                        </div>
                        <div class="activity-time"><?php echo htmlspecialchars($activity['time']); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>