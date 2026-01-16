<?php
require_once '../core/functions.php';
start_session_securely();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide - Webibo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Reuse real app styles so the previews match the dashboard and activity -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/activity.css">
    <link rel="stylesheet" href="../assets/css/guide.css">
</head>
<body>
    <div class="page-header fade-up">
        <div class="brand-link brand-static">
            <img src="../assets/img/webibo/webibo-logo.png" alt="Webibo logo" class="brand-logo">
            <div class="brand-name">Webibo</div>
        </div>
        <button class="exit-btn" type="button" aria-label="Exit guide">
            <i class="fas fa-arrow-left"></i>
            <span class="exit-text">Exit</span>
        </button>
    </div>

    <div class="content-card fade-up">
        <div class="pill">Webibo Guide</div>
        <h1 class="page-title">Welcome to Webibo!</h1>
        <p class="page-subtitle">Webibo is your interactive coding quest—think guided lessons, quick challenges, and a playful map to keep you moving forward. At the end, below are the buttons, nodes, hearts, enemies, and stats you’ll see while playing.</p>

        <section class="section">
            <h2>Course Switcher</h2>
            <p>Pick your adventure from the course picker. The map refreshes to that track.</p>
            <div class="course-switcher-preview">
                <div class="course-module-wrapper">
                    <button class="course-module-btn" type="button">
                        <div class="course-icon-section">
                            <i class="fab fa-html5"></i>
                        </div>
                        <div class="course-text-section">
                            <div class="course-label">Module 1</div>
                            <div class="course-title">Introduction to HTML</div>
                        </div>
                    </button>
                </div>
                <div class="course-icons-list">
                    <div class="course-icon-item">
                        <div class="course-icon-circle html-icon"><i class="fab fa-html5"></i></div>
                        <div class="course-icon-label">HTML</div>
                    </div>
                    <div class="course-icon-item">
                        <div class="course-icon-circle css-icon"><i class="fab fa-css3-alt"></i></div>
                        <div class="course-icon-label">CSS</div>
                    </div>
                    <div class="course-icon-item">
                        <div class="course-icon-circle js-icon"><i class="fab fa-js"></i></div>
                        <div class="course-icon-label">JavaScript</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <h2>Map Levels</h2>
            <p>These are the exact level nodes from the game map. Clicking opens a lecture or challenge.</p>
            <div class="roadmap-preview">
                <div class="node-group">
                    <button class="level-node lecture-node completed" type="button">
                        <i class="fas fa-book"></i>
                        <div class="node-popup">
                            <div class="node-popup-title">Lecture</div>
                            <div class="node-popup-text">Takes you to a lesson page.</div>
                        </div>
                    </button>
                    <span class="node-label">Lecture</span>
                </div>

                <div class="node-group">
                    <button class="level-node current" type="button">
                        <i class="fas fa-code"></i>
                        <div class="node-popup">
                            <div class="node-popup-title">Challenge</div>
                            <div class="node-popup-text">Quizzes, fills, or code practice.</div>
                        </div>
                    </button>
                    <span class="node-label">Challenge</span>
                </div>

                <div class="node-group">
                    <button class="level-node locked" type="button" aria-disabled="true">
                        <i class="fas fa-lock"></i>
                        <div class="node-popup">
                            <div class="node-popup-title">Locked</div>
                            <div class="node-popup-text">Unlock by clearing the previous node.</div>
                        </div>
                    </button>
                    <span class="node-label">Locked</span>
                </div>
            </div>
        </section>

        <section class="section">
            <h2>Top Stats Buttons</h2>
            <p>These are the number of your total XP points, streaks, and badges.</p>
            <div class="top-right-buttons guide-top-buttons">
                <div class="top-btn-wrapper">
                    <button class="top-btn xp-btn" type="button">
                        <i class="fas fa-star"></i>
                        <span class="btn-count">200</span>
                    </button>
                </div>
                <div class="top-btn-wrapper">
                    <button class="top-btn streak-btn" type="button">
                        <i class="fas fa-fire"></i>
                        <span class="btn-count">7</span>
                    </button>
                </div>
                <div class="top-btn-wrapper">
                    <button class="top-btn badges-btn" type="button">
                        <i class="fas fa-trophy"></i>
                        <span class="btn-count">9</span>
                    </button>
                </div>
            </div>
        </section>

        <section class="section">
            <h2>Hearts & Enemies</h2>
            <p>Challenges have progress bars and use hearts as lives. You will also encounter your enemies per challenge.</p>
            <div class="activity-preview">
                <div class="header">
                    <button class="close-btn" type="button"><i class="fas fa-times"></i></button>
                    <div class="progress-container">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 55%;"></div>
                        </div>
                    </div>
                    <div class="hearts-container">
                        <span class="heart"><i class="fas fa-heart"></i></span>
                        <span class="hearts-count">5</span>
                    </div>
                </div>
                <div class="enemy-row">
                    <div class="enemy-section fade-up">
                        <img src="../assets/img/enemies/fox-angry.png" alt="Enemy angry" class="enemy-image">
                        <div class="enemy-info">
                            <div class="enemy-name">Enemy (Angry)</div>
                            <div class="enemy-hp-label">HP: 10 / 20</div>
                            <div class="enemy-hp-bar">
                                <div class="enemy-hp-fill" style="width: 50%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="enemy-section fade-up">
                        <img src="../assets/img/enemies/fox-happy.png" alt="Enemy happy" class="enemy-image">
                        <div class="enemy-info">
                            <div class="enemy-name">Enemy (Happy)</div>
                            <div class="enemy-hp-label">HP: 20 / 20</div>
                            <div class="enemy-hp-bar">
                                <div class="enemy-hp-fill" style="width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Course Switcher section moved above -->
    </div>

    <script>
        document.querySelector('.exit-btn')?.addEventListener('click', () => {
            window.history.back();
        });
    </script>
</body>
</html>
