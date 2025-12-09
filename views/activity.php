<?php require_once __DIR__ . '/../controllers/activity_handler.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($level['title']); ?> - Webibo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/activity.css">
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
            <span class="hearts-count" id="heartsCount"><?php echo $activity_data['currentHearts']; ?></span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content<?php echo $activity_data['type'] === 'code-editor' ? ' code-editor' : ''; ?>">
        <div class="quiz-container<?php echo $activity_data['type'] === 'code-editor' ? ' code-editor' : ''; ?>">
            
            <?php if ($activity_data['type'] === 'multiple-choice'): ?>
                <!-- ===== MULTIPLE CHOICE ACTIVITY ===== -->
                <!-- Question Section -->
                <div class="question-section fade-up">
                    <h2 class="question-title fade-up"><?php echo htmlspecialchars($question); ?></h2>
                    <form method="POST" action="" id="quizForm">
                        <div class="options fade-up" id="optionsContainer">
                            <?php foreach ($options as $option): ?>
                                <button type="button" 
                                        class="option-btn" 
                                        data-answer="<?php echo htmlspecialchars($option); ?>" 
                                        onclick="selectOption(this)">
                                    <?php echo htmlspecialchars($option); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="answer" id="selectedAnswer">
                    </form>
                </div>

                <!-- Enemy Section -->
                <?php if ($activity_data['hasEnemy'] && $enemy): ?>
                <div class="enemy-section fade-up">
                    <img src="../<?php echo htmlspecialchars($enemy['image']); ?>" 
                         alt="<?php echo htmlspecialchars($enemy['name']); ?>" 
                         class="enemy-image" 
                         id="enemyImage">
                    <div class="enemy-info">
                        <div class="enemy-name"><?php echo htmlspecialchars($enemy['name']); ?></div>
                        <div class="enemy-hp-label" id="enemyHPLabel">HP: <?php echo $activity_data['enemyHP']; ?> / <?php echo $activity_data['enemyHP']; ?></div>
                        <div class="enemy-hp-bar">
                            <div class="enemy-hp-fill" id="enemyHPFill"></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            <?php elseif ($activity_data['type'] === 'fill-blank'): ?>
                <!-- ===== FILL-IN-THE-BLANK ACTIVITY ===== -->
                <!-- Question Section -->
                <div class="question-section fade-up">
                    <h2 class="question-title fade-up"><?php echo htmlspecialchars($question); ?></h2>
                    <form method="POST" action="" id="quizForm">
                        <div class="code-container fade-up">
                            <?php 
                            // Parse code template and create code lines
                            $codeLines = explode("\n", $codeTemplate);
                            foreach ($codeLines as $line):
                                // Replace ___ with input field
                                if (strpos($line, '___') !== false):
                                    $parts = explode('___', $line);
                            ?>
                                <div class="code-line">
                                    <span class="code-keyword"><?php echo htmlspecialchars($parts[0]); ?></span>
                                    <input type="text" class="blank-input" id="answerInput" autocomplete="off" maxlength="10">
                                    <span class="code-value"><?php echo htmlspecialchars($parts[1] ?? ''); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="code-line"><?php echo htmlspecialchars($line); ?></div>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                        <input type="hidden" name="answer" id="selectedAnswer">
                    </form>
                </div>

                <!-- Enemy Section -->
                <?php if ($activity_data['hasEnemy'] && $enemy): ?>
                <div class="enemy-section fade-up">
                    <img src="../<?php echo htmlspecialchars($enemy['image']); ?>" 
                         alt="<?php echo htmlspecialchars($enemy['name']); ?>" 
                         class="enemy-image" 
                         id="enemyImage">
                    <div class="enemy-info">
                        <div class="enemy-name"><?php echo htmlspecialchars($enemy['name']); ?></div>
                        <div class="enemy-hp-label" id="enemyHPLabel">HP: <?php echo $activity_data['enemyHP']; ?> / <?php echo $activity_data['enemyHP']; ?></div>
                        <div class="enemy-hp-bar">
                            <div class="enemy-hp-fill" id="enemyHPFill"></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            <?php elseif ($activity_data['type'] === 'code-editor'): ?>
                <!-- ===== CODE EDITOR ACTIVITY ===== -->
                <!-- Wiza Teacher Section -->
                <div class="teacher-section fade-up">
                    <img src="../assets/img/wiza/wiza-teach.png" alt="Wiza Teacher" class="teacher-image">
                    <div class="teacher-bubble">
                        <div class="teacher-name">Wiza</div>
                        <div class="teacher-instruction">
                            <?php echo $question; ?>
                        </div>
                    </div>
                </div>

                <!-- Code Editor Section -->
                <div class="editor-section fade-up">
                    <h2 class="question-title fade-up"><?php echo htmlspecialchars($level['title']); ?></h2>
                    
                    <form method="POST" action="" id="quizForm">
                        <div class="editor-panel-container">
                            <!-- Left Panel: Code Editor -->
                            <div class="editor-panel">
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
                            </div>

                            <!-- Right Panel: Live Preview -->
                            <div class="output-panel">
                                <div class="output-container">
                                    <div class="output-header">
                                        <div class="output-title"><i class="fas fa-eye"></i> Live Preview</div>
                                    </div>
                                    <iframe id="livePreviewFrame" class="live-preview-frame" sandbox="allow-same-origin"></iframe>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="code" id="submittedCode">
                    </form>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Footer Buttons -->
    <div class="footer-buttons">
        <form method="POST" action="" style="display: inline;" id="skipForm">
            <button type="button" name="skip" class="skip-btn" id="skipBtn" onclick="skipQuestion()">SKIP</button>
        </form>
        <button type="button" class="check-btn" id="checkBtn" disabled onclick="<?php echo $activity_data['type'] === 'code-editor' ? 'checkCode()' : 'submitAnswer()'; ?>">CHECK</button>
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
            <img src="../assets/img/wiza/wiza-sad.png" alt="Wiza Sad" class="modal-image">
            <h2 class="modal-title">Hold on! Leaving now will reset your current progress.</h2>
            <p class="modal-message">Do you still want to end this session?</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-primary" onclick="closeModal()">KEEP LEARNING</button>
                <button class="modal-btn modal-btn-danger" onclick="exitToDashboard()">END SESSION</button>
            </div>
        </div>
    </div>

    <!-- Activity Configuration -->
    <script>
        window.activityConfig = <?php echo json_encode($activity_data, JSON_HEX_TAG | JSON_HEX_QUOT); ?>;
    </script>
    <script src="../assets/js/activity.js"></script>
</body>
</html>
