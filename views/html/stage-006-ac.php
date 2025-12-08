<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$current_question = $_SESSION['current_question'] ?? 1;
$hearts = $_SESSION['hearts'] ?? 10;
$correct_code = '<h1>Hello World!</h1>';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['code'])) {
        $submitted_code = trim($_POST['code']);
        if ($submitted_code === $correct_code) {
            // Correct answer - move to next question
            $_SESSION['current_question'] = $current_question + 1;
            header("Location: stage-006-act.php");
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
        header("Location: stage-006-act.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The HTML Forest - Level 2 - WebQuest</title>
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
    <div class="main-content code-editor">
        <div class="quiz-container code-editor">
            <!-- Wiza Teacher Section -->
            <div class="teacher-section fade-up">
                <img src="../../assets/img/wiza/wiza-teach.png" alt="Wiza Teacher" class="teacher-image">
                <div class="teacher-bubble">
                    <div class="teacher-name">Wiza</div>
                    <div class="teacher-instruction">
                        Now, try typing <code>&lt;h1&gt;Hello World!&lt;/h1&gt;</code> and see the magic unfold!
                    </div>
                </div>
            </div>

            <!-- Code Editor Section -->
            <div class="editor-section fade-up">
                <h2 class="question-title fade-up">Write your first HTML heading</h2>
                
                <form method="POST" action="" id="quizForm">
                    <!-- Code Editor -->
                    <div class="editor-container">
                        <div class="editor-header">
                            <div class="editor-dots">
                                <div class="dot red"></div>
                                <div class="dot yellow"></div>
                                <div class="dot green"></div>
                            </div>
                            <div class="editor-title">Code Editor</div>
                        </div>
                        <div class="code-editor">
                            <textarea 
                                class="code-textarea" 
                                id="codeInput" 
                                placeholder="Type your code here..."
                                spellcheck="false"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Output Display -->
                    <br><div class="output-container" id="outputContainer">
                        <div class="output-header">
                            <div class="output-title">Output</div>
                        </div>
                        <div class="output-display" id="outputDisplay"></div>
                    </div>

                    <input type="hidden" name="code" id="submittedCode">
                </form>
            </div>
        </div>
    </div>

    <!-- Footer Buttons -->
    <div class="footer-buttons" id="footerButtons">
        <form method="POST" action="" style="display: inline;" id="skipForm">
            <button type="button" name="skip" class="skip-btn" id="skipBtn" onclick="skipQuestion()">SKIP</button>
        </form>
        <button type="button" class="check-btn" id="checkBtn" disabled onclick="checkCode()">CHECK</button>
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
            type: 'code-editor',
            correctAnswer: '<?php echo $correct_code; ?>',
            currentHearts: <?php echo $hearts; ?>,
            currentProgress: <?php echo $current_question - 1; ?>, // Progress out of 10 (0-10)
            redirectUrl: 'stage-006-ac.php',
            enemyHP: 10,
            hasEnemy: false,
            correctTitle: 'Excellent work!',
            correctDetails: '<div class="feedback-text">You\'ve successfully created your first HTML heading. The &lt;h1&gt; tag is used for main headings.</div>',
            wrongTitle: 'Not quite right',
            wrongDetails: `
                <div class="feedback-text">The correct code is:</div>
                <div class="feedback-code">&lt;h1&gt;Hello World!&lt;/h1&gt;</div>
                <div class="feedback-text" style="margin-top: 12px;">Make sure to include both opening and closing tags, and match the text exactly!</div>
            `
        };
    </script>
    <script src="../../assets/js/activity.js"></script>
</body>
</html>

