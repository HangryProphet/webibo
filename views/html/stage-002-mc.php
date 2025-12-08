<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$current_question = $_SESSION['current_question'] ?? 1;
$hearts = $_SESSION['hearts'] ?? 10;
$correct_answer = '<h1>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['answer'])) {
        $selected = $_POST['answer'];
        if ($selected === $correct_answer) {
            // Correct answer - move to next question
            $_SESSION['current_question'] = $current_question + 1;
            header("Location: stage-001-act.php");
            exit;
        } else {
            // Wrong answer - decrease hearts
            $_SESSION['hearts'] = max(0, $hearts - 1);
            $hearts = $_SESSION['hearts'];
            if ($hearts <= 0) {
                // Game over
                header("Location: ../gameover.php");
                exit;
            }
        }
    }
    
    if (isset($_POST['skip'])) {
        // Treat skip like an incorrect answer
        $_SESSION['hearts'] = max(0, $hearts - 1);
        $hearts = $_SESSION['hearts'];

        if ($hearts <= 0) {
            header("Location: ../gameover.php");
            exit;
        }

        $_SESSION['current_question'] = $current_question + 1;
        header("Location: stage-001-act.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The HTML Forest - Level 1 - WebQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/activity.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <button class="close-btn" onclick="showExitModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>
        <div class="hearts-container">
            <span class="heart">
                <i class="fas fa-heart"></i>
            </span>
            <span class="hearts-count" id="heartsCount"><?php echo $hearts; ?></span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="quiz-container">
            <!-- Question Section -->
            <div class="question-section fade-up">
                <h2 class="question-title fade-up">Which tag is used for the largest heading?</h2>
                <form method="POST" action="" id="quizForm">
                    <div class="options fade-up" id="optionsContainer">
                        <button type="button" class="option-btn" data-answer="<h1>" onclick="selectOption(this)">&lt;h1&gt;</button>
                        <button type="button" class="option-btn" data-answer="<h3>" onclick="selectOption(this)">&lt;h3&gt;</button>
                        <button type="button" class="option-btn" data-answer="<head>" onclick="selectOption(this)">&lt;head&gt;</button>
                    </div>
                    <input type="hidden" name="answer" id="selectedAnswer">
                </form>
            </div>

            <!-- Enemy Section -->
            <div class="enemy-section fade-up">
                <img src="../../assets/img/enemies/fox-happy.png" alt="Fox" class="enemy-image" id="enemyImage">
                <div class="enemy-info">
                    <div class="enemy-name">Fox</div>
                    <div class="enemy-hp-label" id="enemyHPLabel">HP: 10 / 10</div>
                    <div class="enemy-hp-bar">
                        <div class="enemy-hp-fill" id="enemyHPFill"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Buttons -->
    <div class="footer-buttons">
        <form method="POST" action="" style="display: inline;" id="skipForm">
            <button type="button" name="skip" class="skip-btn" id="skipBtn" onclick="skipQuestion()">SKIP</button>
        </form>
        <button type="button" class="check-btn" id="checkBtn" disabled onclick="submitAnswer()">CHECK</button>
    </div>

    <!-- Feedback Panel -->
    <div class="feedback-panel" id="feedbackPanel">
        <div class="feedback-icon" id="feedbackIcon"></div>
        <div class="feedback-content">
            <div class="feedback-title" id="feedbackTitle"></div>
            <div class="feedback-details" id="feedbackDetails"></div>
        </div>
        <div class="feedback-actions">
            <button type="button" class="continue-btn" id="continueBtn" onclick="continueToNext()">CONTINUE</button>
        </div>
    </div>

    <!-- Exit Confirmation Modal -->
    <div class="modal-overlay" id="exitModal">
        <div class="modal-content">
            <img src="../../assets/img/wiza/wiza-sad.png" alt="Wiza Sad" class="modal-image">
            <h2 class="modal-title">Hold on! Leaving now will reset your current progress.</h2>
            <p class="modal-message">Do you still want to end this session?</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-primary" onclick="closeModal()">KEEP LEARNING</button>
                <button class="modal-btn modal-btn-danger" onclick="exitToDashboard()">END SESSION</button>
            </div>
        </div>
    </div>

    <script>
        // Activity configuration
        window.activityConfig = {
            type: 'multiple-choice',
            correctAnswer: '<?php echo $correct_answer; ?>',
            currentHearts: <?php echo $hearts; ?>,
            currentProgress: <?php echo $current_question - 1; ?>, // Progress out of 10 (0-10)
            redirectUrl: 'stage-002-mc.php',
            enemyHP: 10,
            hasEnemy: true,
            correctTitle: 'Awesome!',
            correctDetails: '',
            wrongTitle: 'Correct answer:',
            wrongDetails: `
                    <div class="feedback-label">&lt;h1&gt;</div>
                    <div class="feedback-text" style="margin-top: 8px; font-weight: 700;">Meaning:</div>
                    <div class="feedback-text">The &lt;h1&gt; tag defines the most important heading. It represents the largest and highest-level heading in HTML.</div>
            `
        };
    </script>
    <script src="../../assets/js/activity.js"></script>
</body>
</html>

