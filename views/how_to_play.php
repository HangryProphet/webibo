<?php
require_once '../core/functions.php';
start_session_securely();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Play - Webibo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/how_to_play.css">
</head>
<body>
    <header class="page-header">
        <button class="back-btn" type="button" aria-label="Go back">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
        </button>
        <a href="../index.php" class="brand-link">
            <img src="../assets/img/webibo/webibo-logo.png" alt="Webibo logo" class="brand-logo">
            <span class="brand-name">Webibo</span>
        </a>
    </header>

    <div class="content-card">
        <div class="pill">How it works</div>
        <h1 class="page-title">How to Play Webibo</h1>
        <p class="page-subtitle">What happens from login to level-up, based on the actual in-app flow.</p>

        <section id="start" class="section">
            <h2>1. Land on the Dashboard</h2>
            <p>After signing in, you go straight to the dashboard map. The roadmap fills in with the levels you’ve unlocked and your current path.</p>
            <div class="highlight-box">
                The buttons up top show your XP total, current streak timer, and badge count pulled from your profile stats.
            </div>
        </section>

        <section id="courses" class="section">
            <h2>2. Pick a Course</h2>
            <p>The course selector in the top-left pops up three tracks. Picking one refreshes the map with its levels.</p>
            <ul>
                <li><strong>HTML Course</strong></li>
                <li><strong>CSS Course</strong></li>
                <li><strong>JavaScript Course</strong></li>
            </ul>
        </section>

        <section id="nodes" class="section">
            <h2>3. Read the Map Nodes</h2>
            <p>Each level is a node on the path. Locked nodes show “???”; unlocked nodes open directly.</p>
            <ul>
                <li>Lecture nodes open the lesson for that stop.</li>
                <li>Challenge nodes open the activities (quizzes, practice fills, and the code editor).</li>
                <li>Hover popups show the title and XP reward for unlocked nodes.</li>
            </ul>
        </section>

        <section id="activity-layout" class="section">
            <h2>4. Know the Activity Layout</h2>
            <p>Every challenge page shares the same frame: a progress bar, heart counter, and skip or check buttons. Feedback appears after you check answers, and you’ll see exit, game over, or completion messages as you finish.</p>
            <ul>
                <li><strong>Progress bar</strong> fills per question (0–10 or per item in multi-question sets).</li>
                <li><strong>Hearts</strong> drop on wrong answers or when you skip.</li>
                <li><strong>Continue</strong> moves to the next question or finishes the level.</li>
            </ul>
        </section>

        <section id="activity-types" class="section">
            <h2>5. Activity Types</h2>
            <ul>
                <li><strong>Multiple-choice:</strong> Pick an option; Check unlocks once you select.</li>
                <li><strong>Fill in the blank:</strong> Type the missing code; Enter submits; Check enables when text exists.</li>
                <li><strong>Code editor:</strong> Type code and see a live frame preview; Check validates against the expected answer (with optional whitespace/case rules).</li>
            </ul>
        </section>

        <section id="hearts" class="section">
            <h2>6. Hearts and Skips</h2>
            <p>Wrong answers and skips both consume one heart. When hearts reach zero, the game-over modal lets you retry the level or return to the map.</p>
            <ul>
                <li>Skip reveals the correct answer (or fills it in) and marks the attempt wrong.</li>
                <li>The Check button hides when you skip; Continue advances after feedback.</li>
            </ul>
        </section>

        <section id="enemies" class="section">
            <h2>7. Optional Enemies</h2>
            <p>Some challenges load an enemy card. Correct answers damage its health and reduce the HP bar.</p>
            <div class="highlight-box">
                Enemy HP is cosmetic; your only fail condition is running out of hearts.
            </div>
        </section>

        <section id="finish" class="section">
            <h2>8. Finishing a Level</h2>
            <p>After the last question, a victory message appears. Continuing adds XP to your profile and unlocks the next node.</p>
        </section>

        <section id="navigation" class="section">
            <h2>9. Navigation Controls</h2>
            <ul>
                <li>Use the exit button in an activity to confirm leaving back to the dashboard.</li>
                <li>Victory modal offers next-level (when available) or back-to-map.</li>
                <li>Game over offers retry or back-to-map.</li>
            </ul>
        </section>

        <section id="tips" class="section">
            <h2>10. Quick Tips</h2>
            <ul>
                <li>Select or type something to enable Check; otherwise it stays disabled.</li>
                <li>Use Skip only if you are comfortable losing a heart.</li>
                <li>Reopen a completed node anytime to review the lecture or redo the challenge.</li>
            </ul>
        </section>

        <section id="help-link" class="section">
            <h2>Need more help?</h2>
            <p>Have any concerns? Visit our Help page for quick answers.</p>
            <a class="login-btn" href="help.php">GO TO HELP</a>
        </section>
    </div>

    <script>
        document.querySelector('.back-btn')?.addEventListener('click', () => {
            window.history.back();
        });
    </script>
</body>
</html>
