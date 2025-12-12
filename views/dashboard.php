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

    <!-- Course Module Card (Top Left) -->
    <div class="course-module-wrapper">
        <button class="course-module-btn">
            <div class="course-icon-section">
                <i class="fab fa-html5"></i>
            </div>
            <div class="course-text-section">
                <div class="course-label">Module 1</div>
                <div class="course-title">Introduction to HTML</div>
            </div>
        </button>
        <div class="course-popup">
            <div class="popup-header">SELECT COURSE</div>
            <div class="course-icons-list">
                <div class="course-icon-item" data-course-id="1">
                    <div class="course-icon-circle html-icon">
                        <i class="fab fa-html5"></i>
                    </div>
                    <div class="course-icon-label">HTML</div>
                </div>
                <div class="course-icon-item<?php echo $courseLockStatus[2] ? ' locked' : ''; ?>" data-course-id="2" data-locked="<?php echo $courseLockStatus[2] ? 'true' : 'false'; ?>">
                    <div class="course-icon-circle css-icon">
                        <i class="fab fa-css3-alt"></i>
                        <?php if ($courseLockStatus[2]): ?><i class="fas fa-lock lock-icon"></i><?php endif; ?>
                    </div>
                    <div class="course-icon-label">CSS</div>
                </div>
                <div class="course-icon-item<?php echo $courseLockStatus[3] ? ' locked' : ''; ?>" data-course-id="3" data-locked="<?php echo $courseLockStatus[3] ? 'true' : 'false'; ?>">
                    <div class="course-icon-circle js-icon">
                        <i class="fab fa-js"></i>
                        <?php if ($courseLockStatus[3]): ?><i class="fas fa-lock lock-icon"></i><?php endif; ?>
                    </div>
                    <div class="course-icon-label">JavaScript</div>
                </div>
            </div>
        </div>
    </div>

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

    <!-- Bottom Left Idea Button -->
    <button class="idea-node-btn" type="button" aria-label="Open guide" onclick="window.location.href='guide.php'">
        <i class="fas fa-lightbulb"></i>
    </button>

    <div class="main-content">
        <div class="roadmap-container">
            <div class="roadmap">
                <svg class="roadmap-trail" preserveAspectRatio="none">
                    <path class="trail-path" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                <!-- Level nodes will be dynamically loaded here by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Course Locked Modal -->
    <div class="modal-overlay" id="courseLockedModal">
        <div class="modal-content">
            <img src="../assets/img/wiza/wiza-sad.png" alt="Wiza Locked" class="modal-image">
            <h2 class="modal-title" style="color: #ff4b4b;">🔒 Course Locked!</h2>
            <p class="modal-message" id="courseLockedMessage">Complete all HTML levels to unlock this course.</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-primary" onclick="closeCourseLockedModal()">GOT IT</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>