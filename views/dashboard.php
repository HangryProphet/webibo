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
        <!-- Lessons Button -->
        <div class="top-btn-wrapper">
            <button class="top-btn lessons-btn">
                <i class="fab fa-html5"></i>
            </button>
            <div class="hover-popup lessons-popup">
                <div class="popup-header">MY COURSES</div>
                <div class="course-list">
                    <?php if (!empty($userStats['courses'])): ?>
                        <?php foreach ($userStats['courses'] as $course): ?>
                            <?php if ($course['enrolled']): ?>
                                <div class="course-item">
                                    <div class="course-icon">
                                        <i class="fab <?php echo htmlspecialchars($course['icon']); ?>"></i>
                                    </div>
                                    <span class="course-name"><?php echo htmlspecialchars($course['name']); ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
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

        <!-- Hearts Button -->
        <div class="top-btn-wrapper">
            <button class="top-btn hearts-btn">
                <i class="fas fa-heart"></i>
                <span class="btn-count"><?php echo htmlspecialchars($userStats['hearts']['current']); ?></span>
            </button>
            <div class="hover-popup hearts-popup">
                <div class="popup-title">Hearts</div>
                <div class="hearts-display">
                    <?php 
                    $maxHearts = 5; // Display max 5 hearts in UI
                    $currentHearts = $userStats['hearts']['current'];
                    for ($i = 0; $i < $maxHearts; $i++): 
                    ?>
                        <i class="fas fa-heart <?php echo ($i < $currentHearts) ? 'filled' : 'empty'; ?>"></i>
                    <?php endfor; ?>
                </div>
                <div class="next-heart">Next heart in <span class="heart-timer"><?php echo formatTimeRemaining($userStats['hearts']['next_heart_in_seconds']); ?></span></div>
                <div class="hearts-message">You still have hearts left! Keep on learning</div>
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="lesson-header">
            <button class="nav-arrow">
                <i class="fas fa-chevron-left"></i>
            </button>
            
            <div class="lesson-title-wrapper">
                <div class="lesson-subtitle">SECTION 1, UNIT 1</div>
                <div class="lesson-title">The HTML Forest</div>
            </div>
            
            <button class="nav-arrow">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <div class="roadmap-container">
            <div class="roadmap">
                <?php if (!empty($userProgress)): ?>
                    <?php foreach ($userProgress as $level): ?>
                        <div class="level-node <?php echo htmlspecialchars($level['status']); ?>" 
                             style="left: <?php echo htmlspecialchars($level['position']['left']); ?>px; top: <?php echo htmlspecialchars($level['position']['top']); ?>px;"
                             data-level-id="<?php echo htmlspecialchars($level['level_id']); ?>">
                            <i class="fas <?php echo getLevelIcon($level['type']); ?>"></i>
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