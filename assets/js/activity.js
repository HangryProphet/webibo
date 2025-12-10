// Activity JavaScript - Shared functionality for all activity pages
// Configuration should be set before this script loads:
// window.activityConfig = {
//     type: 'multiple-choice' | 'fill-blank' | 'code-editor',
//     correctAnswer: '...',
//     currentHearts: 10,
//     currentProgress: 30,
//     redirectUrl: 'activity.php',
//     enemyHP: 8,
//     hasEnemy: true/false
// }

(function() {
    'use strict';

    // Get configuration from window object (set by PHP)
    const config = window.activityConfig || {};
    const activityType = config.type || 'multiple-choice';
    
    let selectedAnswer = null;
    let currentHearts = config.currentHearts || 10;
    let currentProgress = config.currentProgress || 0; // Progress out of 10 (0-10)
    let pendingSkip = false;
    let isCorrect = false;
    let enemyHP = config.enemyHP || 10; // Enemy HP out of 10
    const correctAnswer = config.correctAnswer || '';
    const redirectUrl = config.redirectUrl || 'activity.php';
    // Paths are relative to views/* pages that include this script with ../assets/...
    const correctSound = (typeof Audio !== 'undefined') ? new Audio('../assets/sfx/correct.mp3') : null;
    const wrongSound = (typeof Audio !== 'undefined') ? new Audio('../assets/sfx/wrong.mp3') : null;

    // Initialize based on activity type
    function init() {
        // Initialize progress bar
        initProgressBar();
        
        if (activityType === 'fill-blank') {
            initFillBlank();
        } else if (activityType === 'code-editor') {
            initCodeEditor();
        } else {
            initMultipleChoice();
        }

        // Setup exit modal handlers
        setupExitModal();
    }

    // Initialize progress bar on page load
    function initProgressBar() {
        const progressFill = document.getElementById('progressFill');
        if (progressFill) {
            // Convert progress (0-10) to percentage
            progressFill.style.width = (currentProgress / 10 * 100) + '%';
        }
        
        // Initialize enemy HP display
        updateEnemyHP();
    }

    // Initialize multiple choice activity
    function initMultipleChoice() {
        // Option selection is handled by onclick in HTML
    }

    // Initialize fill-in-the-blank activity
    function initFillBlank() {
        const answerInput = document.getElementById('answerInput');
        if (!answerInput) return;

        // Enable check button when user types
        answerInput.addEventListener('input', function() {
            const value = this.value.trim();
            const checkBtn = document.getElementById('checkBtn');
            if (checkBtn) {
                checkBtn.disabled = value === '';
            }
            selectedAnswer = value;
        });

        // Allow Enter key to submit
        answerInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && this.value.trim() !== '') {
                e.preventDefault();
                submitAnswer();
            }
        });

        // Focus on input when page loads
        window.addEventListener('load', function() {
            answerInput.focus();
        });
    }

    // Initialize code editor activity
    function initCodeEditor() {
        const codeInput = document.getElementById('codeInput');
        const livePreviewFrame = document.getElementById('livePreviewFrame');
        
        if (!codeInput) return;

        // Setup live preview functionality
        if (livePreviewFrame) {
            codeInput.addEventListener('input', function() {
                const userCode = codeInput.value;
                const checkBtn = document.getElementById('checkBtn');
                
                // Enable/disable check button
                if (checkBtn) {
                    checkBtn.disabled = userCode.trim() === '';
                }
                
                // Update live preview in iframe
                try {
                    const previewDocument = livePreviewFrame.contentDocument || livePreviewFrame.contentWindow.document;
                    
                    // Write user code directly to iframe
                    previewDocument.open();
                    previewDocument.write(userCode);
                    previewDocument.close();
                } catch (e) {
                    console.error('Error updating live preview:', e);
                }
            });
        } else {
            // Fallback if no iframe (old code)
            codeInput.addEventListener('input', function() {
                const value = this.value.trim();
                const checkBtn = document.getElementById('checkBtn');
                if (checkBtn) {
                    checkBtn.disabled = value === '';
                }
            });
        }

        // Focus on textarea when page loads
        window.addEventListener('load', function() {
            codeInput.focus();
        });
    }

    // Select option (for multiple choice)
    window.selectOption = function(button) {
        // Remove selected class from all options
        document.querySelectorAll('.option-btn').forEach(btn => {
            btn.classList.remove('selected');
        });
        
        // Add selected class to clicked option
        button.classList.add('selected');
        
        // Store the selected answer
        selectedAnswer = button.getAttribute('data-answer');
        const hiddenInput = document.getElementById('selectedAnswer');
        if (hiddenInput) {
            hiddenInput.value = selectedAnswer;
        }
        
        // Enable check button
        const checkBtn = document.getElementById('checkBtn');
        if (checkBtn) {
            checkBtn.disabled = false;
        }
    };

    // Submit answer (for multiple choice and fill-blank)
    window.submitAnswer = function() {
        if (activityType === 'fill-blank') {
            const answerInput = document.getElementById('answerInput');
            if (!answerInput || !answerInput.value.trim()) return;
            selectedAnswer = answerInput.value.trim();
        }

        if (!selectedAnswer) return;

        pendingSkip = false;

        // Disable inputs and buttons
        disableInputs();

        // Store answer for form submission
        if (activityType === 'fill-blank') {
            const hiddenInput = document.getElementById('selectedAnswer');
            if (hiddenInput) {
                hiddenInput.value = selectedAnswer;
            }
        }

        // Update progress bar
        updateProgress();

        if (selectedAnswer === correctAnswer) {
            handleCorrectAnswer();
        } else {
            handleWrongAnswer();
        }
    };

    // Check code (for code editor)
    window.checkCode = function() {
        const codeInput = document.getElementById('codeInput');
        if (!codeInput) return;

        const userCode = codeInput.value.trim();
        if (!userCode) return;

        pendingSkip = false;
        selectedAnswer = userCode;

        // Disable textarea and check button
        codeInput.disabled = true;
        const checkBtn = document.getElementById('checkBtn');
        if (checkBtn) {
            checkBtn.disabled = true;
        }

        // Store code for form submission
        const submittedCode = document.getElementById('submittedCode');
        if (submittedCode) {
            submittedCode.value = userCode;
        }

        // Update progress bar
        updateProgress();

        // Validate the code
        let codeToCheck = userCode;
        let expectedCode = correctAnswer;

        // Apply validation rules if they exist
        if (config.validation) {
            if (config.validation.ignore_whitespace) {
                codeToCheck = codeToCheck.replace(/\s+/g, '');
                expectedCode = expectedCode.replace(/\s+/g, '');
            }
            if (config.validation.case_sensitive === false) {
                codeToCheck = codeToCheck.toLowerCase();
                expectedCode = expectedCode.toLowerCase();
            }
        }

        // Check if code is correct
        if (codeToCheck === expectedCode) {
            isCorrect = true;
            handleCorrectAnswer();
        } else {
            isCorrect = false;
            handleWrongAnswer();
        }

        // Hide skip button
        const skipBtn = document.getElementById('skipBtn');
        if (skipBtn) {
            skipBtn.style.display = 'none';
        }
    };

    // Skip question
    window.skipQuestion = function() {
        pendingSkip = true;

        // Disable inputs and buttons
        disableInputs();

        if (activityType === 'fill-blank') {
            const answerInput = document.getElementById('answerInput');
            if (answerInput) {
                answerInput.value = correctAnswer;
                answerInput.classList.add('correct');
            }
        } else if (activityType === 'code-editor') {
            const codeInput = document.getElementById('codeInput');
            if (codeInput) {
                codeInput.value = correctAnswer;
            }

            // Show correct output
            const outputContainer = document.getElementById('outputContainer');
            const outputDisplay = document.getElementById('outputDisplay');
            if (outputContainer && outputDisplay) {
                outputContainer.classList.add('active');
                outputDisplay.className = 'output-display';
                outputDisplay.innerHTML = correctAnswer;
            }
        } else {
            // Multiple choice - reveal correct answer
            const correctBtn = document.querySelector(`[data-answer="${correctAnswer}"]`);
            if (correctBtn) {
                correctBtn.classList.add('correct');
            }
        }

        // Update progress
        updateProgress();

        // Decrease hearts
        decreaseHearts();

        showFeedback(false);
    };

    // Disable all inputs
    function disableInputs() {
        if (activityType === 'fill-blank') {
            const answerInput = document.getElementById('answerInput');
            if (answerInput) {
                answerInput.disabled = true;
            }
        } else if (activityType === 'code-editor') {
            const codeInput = document.getElementById('codeInput');
            if (codeInput) {
                codeInput.disabled = true;
            }
        } else {
            // Multiple choice
            document.querySelectorAll('.option-btn').forEach(btn => {
                btn.disabled = true;
                btn.style.cursor = 'not-allowed';
            });
        }

        const checkBtn = document.getElementById('checkBtn');
        if (checkBtn) {
            checkBtn.disabled = true;
        }

        const skipBtn = document.getElementById('skipBtn');
        if (skipBtn) {
            skipBtn.disabled = true;
        }
    }

    // Update progress bar (progress is out of 10)
    function updateProgress() {
        currentProgress += 1; // Increment by 1 (out of 10)
        if (currentProgress > 10) currentProgress = 10;
        const progressFill = document.getElementById('progressFill');
        if (progressFill) {
            // Convert to percentage: (currentProgress / 10) * 100
            progressFill.style.width = (currentProgress / 10 * 100) + '%';
        }
    }

    // Handle correct answer
    function handleCorrectAnswer() {
        if (activityType === 'multiple-choice') {
            const selectedBtn = document.querySelector('.option-btn.selected');
            if (selectedBtn) {
                selectedBtn.classList.add('correct');
            }

            // Change enemy to angry if enemy exists
            if (config.hasEnemy !== false) {
                const enemyImage = document.getElementById('enemyImage');
                if (enemyImage) {
                    // Keep same base path and swap -happy with -angry so relative URLs stay valid
                    const angrySrc = enemyImage.src.replace('-happy', '-angry');
                    enemyImage.src = angrySrc;
                }

                // Decrease enemy HP
                enemyHP = Math.max(0, enemyHP - 2);
                updateEnemyHP();
            }
        } else if (activityType === 'fill-blank') {
            const answerInput = document.getElementById('answerInput');
            if (answerInput) {
                answerInput.classList.add('correct');
            }

            // Change enemy to angry if enemy exists
            if (config.hasEnemy !== false) {
                const enemyImage = document.getElementById('enemyImage');
                if (enemyImage) {
                    // Keep same base path and swap -happy with -angry so relative URLs stay valid
                    const angrySrc = enemyImage.src.replace('-happy', '-angry');
                    enemyImage.src = angrySrc;
                }

                // Decrease enemy HP
                enemyHP = Math.max(0, enemyHP - 2);
                updateEnemyHP();
            }
        }

        // Hide skip button
        const skipBtn = document.getElementById('skipBtn');
        if (skipBtn) {
            skipBtn.style.display = 'none';
        }

        playFeedbackSound(true);
        showFeedback(true);
    }

    // Handle wrong answer
    function handleWrongAnswer() {
        if (activityType === 'multiple-choice') {
            const selectedBtn = document.querySelector('.option-btn.selected');
            const correctBtn = document.querySelector(`[data-answer="${correctAnswer}"]`);
            
            if (selectedBtn) {
                selectedBtn.classList.add('wrong');
            }
            if (correctBtn) {
                correctBtn.classList.add('correct');
            }
        } else if (activityType === 'fill-blank') {
            const answerInput = document.getElementById('answerInput');
            if (answerInput) {
                answerInput.classList.add('wrong');
            }
        }

        // Decrease player hearts
        decreaseHearts();

        playFeedbackSound(false);
        showFeedback(false);
    }

    // Decrease hearts
    function decreaseHearts() {
        currentHearts = Math.max(0, currentHearts - 1);
        const heartsCount = document.getElementById('heartsCount');
        if (heartsCount) {
            heartsCount.textContent = currentHearts;
        }
    }

    // Update enemy HP
    function updateEnemyHP() {
        const percentage = (enemyHP / 10) * 100;
        const hpFill = document.getElementById('enemyHPFill');
        const hpLabel = document.getElementById('enemyHPLabel');
        
        if (hpFill) {
            hpFill.style.width = percentage + '%';
        }
        if (hpLabel) {
            hpLabel.textContent = `HP: ${enemyHP} / 10`;
        }
    }

    // Show feedback
    function showFeedback(isCorrect) {
        const panel = document.getElementById('feedbackPanel');
        const icon = document.getElementById('feedbackIcon');
        const title = document.getElementById('feedbackTitle');
        const details = document.getElementById('feedbackDetails');
        const continueBtn = document.getElementById('continueBtn');

        if (!panel || !icon || !title || !details || !continueBtn) return;

        panel.classList.add('active');

        if (isCorrect) {
            icon.className = 'feedback-icon correct';
            icon.innerHTML = '<i class="fas fa-check"></i>';
            title.className = 'feedback-title correct';
            title.textContent = config.correctTitle || 'Awesome!';
            details.innerHTML = config.correctDetails || '';
            continueBtn.className = 'continue-btn correct';
        } else {
            icon.className = 'feedback-icon wrong';
            icon.innerHTML = '<i class="fas fa-times"></i>';
            title.className = 'feedback-title wrong';
            title.textContent = config.wrongTitle || 'Correct answer:';
            details.innerHTML = config.wrongDetails || '';
            continueBtn.className = 'continue-btn wrong';
        }
    }

    // Play sound for correct/wrong feedback
    function playFeedbackSound(isCorrect) {
        const sound = isCorrect ? correctSound : wrongSound;
        if (!sound) return;

        try {
            sound.currentTime = 0;
            const playPromise = sound.play();
            if (playPromise && typeof playPromise.catch === 'function') {
                playPromise.catch(err => console.warn('Audio playback blocked:', err));
            }
        } catch (e) {
            console.error('Error playing feedback sound:', e);
        }
    }

    // Continue to next question
    window.continueToNext = function() {
        if (pendingSkip) {
            const skipForm = document.getElementById('skipForm');
            if (skipForm) {
                skipForm.submit();
                return;
            }
        }

        if (activityType === 'code-editor' && isCorrect) {
            const quizForm = document.getElementById('quizForm');
            if (quizForm) {
                quizForm.submit();
                return;
            }
        }

        // Reload or go to next question
        window.location.href = redirectUrl;
    };

    // Exit Modal Functions
    window.showExitModal = function() {
        const exitModal = document.getElementById('exitModal');
        if (exitModal) {
            exitModal.classList.add('active');
        }
    };

    window.closeModal = function() {
        const exitModal = document.getElementById('exitModal');
        if (exitModal) {
            exitModal.classList.remove('active');
        }
    };

    window.exitToDashboard = function() {
        // Simple redirect to dashboard
        window.location.href = 'dashboard.php';
    };

    // Setup exit modal handlers
    function setupExitModal() {
        const exitModal = document.getElementById('exitModal');
        if (exitModal) {
            // Close modal when clicking outside
            exitModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    window.closeModal();
                }
            });
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeModal();
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

