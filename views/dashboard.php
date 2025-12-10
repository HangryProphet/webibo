<?php require_once '../controllers/dashboard.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webibo Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Top Right Buttons -->
    <div class="top-right-buttons">
        <!-- XP Button -->
        <div class="top-btn-wrapper">
            <button class="top-btn xp-btn">
                <i class="fas fa-star"></i>
                <span class="btn-count"><?php echo number_format($userStats['total_xp'] ?? 0); ?></span>
            </button>
            <div class="hover-popup xp-popup">
                <div class="popup-header">TOTAL XP EARNED</div>
                <div class="xp-display">
                    <div class="xp-number"><?php echo number_format($userStats['total_xp'] ?? 0); ?></div>
                    <div class="xp-icon">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
                <div class="xp-message">Keep learning to earn more XP!</div>
            </div>
        </div>

        <!-- Streak Button -->
        <div class="top-btn-wrapper">
            <button class="top-btn streak-btn">
                <i class="fas fa-fire"></i>
                <span class="btn-count"><?php echo htmlspecialchars($userStats['streak']['current_days']); ?></span>
            </button>
            <div class="hover-popup streak-popup">
                <div class="popup-header">STREAK SOCIETY</div>
                <div class="streak-info">
                    <div class="streak-number"><?php echo htmlspecialchars($userStats['streak']['current_days']); ?> day streak</div>
                    <div class="streak-fire-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                </div>
                <div class="streak-timer"><?php echo formatTimeRemaining($userStats['streak']['reset_in_seconds']); ?> until your streak resets!</div>
                <div class="week-tracker">
                    <?php 
                    $days = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
                    foreach ($userStats['streak']['weekly_progress'] as $index => $active): 
                    ?>
                        <div class="day<?php echo $active ? ' active' : ''; ?>"><?php echo $days[$index]; ?></div>
                    <?php endforeach; ?>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                        <div class="progress-glow"></div>
                    </div>
                    <div class="progress-icon">
                        <i class="fas fa-fire"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Badges Button -->
        <div class="top-btn-wrapper">
            <button class="top-btn badges-btn">
                <i class="fas fa-trophy"></i>
                <span class="btn-count"><?php echo htmlspecialchars($userStats['total_badges'] ?? 0); ?></span>
            </button>
            <div class="hover-popup badges-popup">
                <div class="popup-title">Total Badges Earned</div>
                <div class="badges-display">
                    <div class="badges-number"><?php echo htmlspecialchars($userStats['total_badges'] ?? 0); ?></div>
                    <div class="badges-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
                <div class="badges-message">Complete achievements to earn more badges!</div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="lesson-header">
            <button class="nav-arrow">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="lesson-title-wrapper">
                <div class="lesson-subtitle">SECTION 1</div>
                <div class="lesson-title">The HTML Forest</div>
            </div>
            
            <button class="nav-arrow">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <div class="roadmap-container">
            <div class="roadmap">
                <svg class="roadmap-trail" preserveAspectRatio="none">
                    <path class="trail-path" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <?php if (!empty($userProgress)): ?>
                    <?php foreach ($userProgress as $level): ?>
                        <?php $status = 'completed'; ?>
                        <div class="level-node <?php echo htmlspecialchars($status); ?>" 
                             style="left: <?php echo htmlspecialchars($level['position']['left']); ?>px; top: <?php echo htmlspecialchars($level['position']['top']); ?>px;"
                             data-level-id="<?php echo htmlspecialchars($level['level_id']); ?>">
                            <i class="fas <?php echo getLevelIcon($level['type']); ?>"></i>
                            <div class="node-hover-popup">
                                <div class="node-popup-text">
                                    <?php 
                                        $activityLabel = $level['activity_type'] ?? 'Lecture';
                                        $title = $level['title'] ?? ('Level ' . ($level['level_id'] ?? ''));
                                        echo htmlspecialchars($activityLabel . ': ' . $title);
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #888; padding: 50px;">No levels available yet. Start your journey!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>