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
    let isCorrect = false;
    let enemyHP = config.enemyHP || 10;
    const maxEnemyHP = config.maxEnemyHP || enemyHP;
    let correctAnswer = config.correctAnswer || '';
    const redirectUrl = config.redirectUrl || 'dashboard.php';
    
    // Multi-question support
    const isMultiQuestion = config.isMultiQuestion || false;
    const allQuestions = config.allQuestions || [];
    const totalQuestions = config.totalQuestions || 1;
    let currentQuestionIndex = config.currentQuestionIndex || 0;
    let questionsAnswered = 0;
    let correctAnswersCount = 0;
    // Paths are relative to views/* pages that include this script with ../assets/...
    const correctSound = (typeof Audio !== 'undefined') ? new Audio('../assets/sfx/correct.mp3') : null;
    const wrongSound = (typeof Audio !== 'undefined') ? new Audio('../assets/sfx/wrong.mp3') : null;
    const enemyDamageSound = (typeof Audio !== 'undefined') ? new Audio('../assets/sfx/enemy-damage.mp3') : null;
    const playerDamageSound = (typeof Audio !== 'undefined') ? new Audio('../assets/sfx/player-damage.mp3') : null;
    const victorySound = (typeof Audio !== 'undefined') ? new Audio('../assets/sfx/victory.mp3') : null;
    const gameOverSound = (typeof Audio !== 'undefined') ? new Audio('../assets/sfx/gameover.mp3') : null;
    let damageFlashEl = null;
    let successFlashEl = null;
    
    // Initialize volume from localStorage (default 70%)
    function getVolume() {
        const savedVolume = localStorage.getItem('webibo_sound_volume');
        return savedVolume !== null ? parseInt(savedVolume) / 100 : 0.7;
    }

    function triggerSuccessFlash() {
        if (!successFlashEl) {
            successFlashEl = document.createElement('div');
            successFlashEl.id = 'successFlash';
            successFlashEl.className = 'success-flash';
            document.body.appendChild(successFlashEl);
        }

        successFlashEl.classList.remove('active');
        void successFlashEl.offsetWidth;
        successFlashEl.classList.add('active');
    }
    
    function setSoundVolume(sound) {
        if (sound) sound.volume = getVolume();
    }

    // Set initial volume
    setSoundVolume(correctSound);
    setSoundVolume(wrongSound);
    setSoundVolume(enemyDamageSound);
    setSoundVolume(playerDamageSound);
    setSoundVolume(victorySound);
    setSoundVolume(gameOverSound);
    
    // Listen for volume changes from settings page
    window.addEventListener('volumeChange', function(e) {
        const volume = e.detail.volume;
        if (correctSound) correctSound.volume = volume;
        if (wrongSound) wrongSound.volume = volume;
        if (enemyDamageSound) enemyDamageSound.volume = volume;
        if (playerDamageSound) playerDamageSound.volume = volume;
        if (victorySound) victorySound.volume = volume;
        if (gameOverSound) gameOverSound.volume = volume;
    });

    // Helper function to play any sound with volume check
    function playSoundSafe(sound) {
        if (!sound) return;
        
        // Don't play if volume is 0
        const currentVolume = getVolume();
        if (currentVolume === 0) return;
        
        try {
            sound.currentTime = 0;
            const playPromise = sound.play();
            if (playPromise && typeof playPromise.catch === 'function') {
                playPromise.catch(err => console.warn('Audio playback blocked:', err));
            }
        } catch (e) {
            console.error('Error playing sound:', e);
        }
    }

    function triggerDamageFlash() {
        if (!damageFlashEl) {
            damageFlashEl = document.createElement('div');
            damageFlashEl.id = 'damageFlash';
            damageFlashEl.className = 'damage-flash';
            document.body.appendChild(damageFlashEl);
        }

        // Restart animation
        damageFlashEl.classList.remove('active');
        // Force reflow
        void damageFlashEl.offsetWidth;
        damageFlashEl.classList.add('active');
    }

    // Load next question in multi-question activities
    function loadNextQuestion() {
        if (!isMultiQuestion || currentQuestionIndex >= allQuestions.length) return;
        
        const nextQuestion = allQuestions[currentQuestionIndex];
        
        // Update current question data
        correctAnswer = nextQuestion.correct_answer || nextQuestion.correct_code || '';
        config.correctTitle = nextQuestion.feedback?.correct?.title || 'Correct! 🎉';
        config.correctDetails = nextQuestion.feedback?.correct?.details || '';
        config.wrongTitle = nextQuestion.feedback?.wrong?.title || 'Not quite! 🤔';
        config.wrongDetails = nextQuestion.feedback?.wrong?.details || '';
        
        // Hide feedback panel
        const panel = document.getElementById('feedbackPanel');
        if (panel) {
            panel.classList.remove('active');
        }
        
        // Reset state
        selectedAnswer = null;
        isCorrect = false;
        
        // Update UI based on activity type
        if (activityType === 'multiple-choice') {
            // Update question text
            const questionTitle = document.querySelector('.question-title');
            if (questionTitle) {
                questionTitle.textContent = nextQuestion.question;
            }
            
            // Update options
            const optionsContainer = document.getElementById('optionsContainer');
            if (optionsContainer && nextQuestion.options) {
                optionsContainer.innerHTML = '';
                nextQuestion.options.forEach(option => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'option-btn';
                    btn.setAttribute('data-answer', option);
                    btn.textContent = option;
                    btn.onclick = function() { selectOption(this); };
                    optionsContainer.appendChild(btn);
                });
            }
        } else if (activityType === 'fill-blank') {
            // Update question text
            const questionTitle = document.querySelector('.question-title');
            if (questionTitle) {
                questionTitle.textContent = nextQuestion.question;
            }
            
            // Clear input
            const answerInput = document.getElementById('answerInput');
            if (answerInput) {
                answerInput.value = '';
                answerInput.disabled = false;
                answerInput.classList.remove('correct', 'wrong');
                answerInput.focus();
            }
        }
        
        // Re-enable buttons
        const checkBtn = document.getElementById('checkBtn');
        if (checkBtn) {
            checkBtn.disabled = true;
        }
        
        // Progress will be updated after user answers the next question
    }
    
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
        
        // Setup Enter key handler for continue button
        setupContinueKeyHandler();
    }

    // Initialize progress bar on page load
    function initProgressBar() {
        const progressFill = document.getElementById('progressFill');
        if (progressFill) {
            // For multi-question activities, start at 0%
            // For single question, use the backend progress value
            if (isMultiQuestion) {
                progressFill.style.width = '0%';
            } else {
                progressFill.style.width = (currentProgress / 10 * 100) + '%';
            }
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
            if (!answerInput || !answerInput.value.trim()) {
                // Don't submit empty answer - just return without doing anything
                return;
            }
            selectedAnswer = answerInput.value.trim();
        }

        if (!selectedAnswer) {
            // Don't submit if no answer selected
            return;
        }

        // Disable inputs and buttons
        disableInputs();

        // Store answer for form submission
        if (activityType === 'fill-blank') {
            const hiddenInput = document.getElementById('selectedAnswer');
            if (hiddenInput) {
                hiddenInput.value = selectedAnswer;
            }
        }

        // Update progress bar (tracks question progression, not correctness)
        updateProgress();

        // Case-insensitive comparison for fill-blank
        let isCorrect = false;
        if (activityType === 'fill-blank') {
            isCorrect = (selectedAnswer.toLowerCase() === correctAnswer.toLowerCase());
        } else {
            isCorrect = (selectedAnswer === correctAnswer);
        }

        if (isCorrect) {
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
    }

    // Update progress bar (progress is out of 10)
    function updateProgress() {
        const progressFill = document.getElementById('progressFill');
        if (!progressFill) return;
        
        if (isMultiQuestion) {
            // For multi-question: calculate based on questions answered
            const progressPercentage = ((currentQuestionIndex + 1) / totalQuestions) * 100;
            progressFill.style.width = progressPercentage + '%';
        } else {
            // For single question: use traditional progress (out of 10)
            currentProgress += 1;
            if (currentProgress > 10) currentProgress = 10;
            progressFill.style.width = (currentProgress / 10 * 100) + '%';
        }
    }

    // Handle correct answer
    function handleCorrectAnswer() {
        triggerSuccessFlash();

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
            correctAnswersCount++;
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
            correctAnswersCount++;
        } else if (activityType === 'code-editor') {
            // Code editor: show victory modal immediately on correct answer
            playFeedbackSound(true);
            setTimeout(() => {
                showVictoryModal();
            }, 500);
            return; // Don't show regular feedback
        }

        playFeedbackSound(true);
        showFeedback(true);
    }

    // Handle wrong answer
    function handleWrongAnswer() {
        triggerDamageFlash();

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
        } else if (activityType === 'code-editor') {
            // Code editor: show failure state immediately
            // No hearts decrease, just show failure modal
            playFeedbackSound(false);
            showCodeEditorFailureModal();
            return; // Don't show regular feedback
        }

        // Show happy/neutral enemy state on wrong answer
        if (config.hasEnemy !== false) {
            const enemyImage = document.getElementById('enemyImage');
            if (enemyImage) {
                const src = enemyImage.src;
                const match = src.match(/(.+?)(-angry|-happy|-neutral)?(\.[a-z]+)$/i);
                if (match) {
                    const [, base, , ext] = match;
                    enemyImage.src = `${base}-happy${ext}`;
                } else {
                    enemyImage.src = `${src}-happy`;
                }
            }
        }

        // Decrease player hearts (not for code editor)
        if (activityType !== 'code-editor') {
            decreaseHearts();
        }

        playFeedbackSound(false);
        showFeedback(false);
    }

    // Track game over state
    let isGameOver = false;
    
    // Decrease hearts
    function decreaseHearts() {
        currentHearts = Math.max(0, currentHearts - 1);
        const heartsCount = document.getElementById('heartsCount');
        if (heartsCount) {
            heartsCount.textContent = currentHearts;
        }
        
        // Check for game over
        if (currentHearts <= 0) {
            isGameOver = true;
            
            // Disable continue button
            const continueBtn = document.getElementById('continueBtn');
            if (continueBtn) {
                continueBtn.disabled = true;
                continueBtn.style.opacity = '0.5';
                continueBtn.style.cursor = 'not-allowed';
            }
            
            // Hide feedback panel and show game over after delay
            setTimeout(() => {
                const feedbackPanel = document.getElementById('feedbackPanel');
                if (feedbackPanel) {
                    feedbackPanel.classList.remove('active');
                }
                showGameOverModal();
            }, 1500);
        }
    }

    // Update enemy HP
    function updateEnemyHP() {
        const percentage = (enemyHP / maxEnemyHP) * 100;
        const hpFill = document.getElementById('enemyHPFill');
        const hpLabel = document.getElementById('enemyHPLabel');
        
        if (hpFill) {
            hpFill.style.width = Math.max(0, percentage) + '%';
        }
        if (hpLabel) {
            hpLabel.textContent = `HP: ${Math.max(0, enemyHP)} / ${maxEnemyHP}`;
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
            
            // Update button text based on whether there are more questions
            if (isMultiQuestion && currentQuestionIndex < totalQuestions - 1) {
                continueBtn.textContent = 'NEXT QUESTION';
            } else {
                // Last question or single question
                continueBtn.textContent = 'COMPLETE';
            }
        } else {
            icon.className = 'feedback-icon wrong';
            icon.innerHTML = '<i class="fas fa-times"></i>';
            title.className = 'feedback-title wrong';
            title.textContent = config.wrongTitle || 'Correct answer:';
            details.innerHTML = config.wrongDetails || '';
            continueBtn.className = 'continue-btn wrong';
            continueBtn.textContent = 'CONTINUE';
        }
    }

    // Play sound for correct/wrong feedback
    function playFeedbackSound(isCorrect) {
        // Don't play if volume is 0
        const currentVolume = getVolume();
        if (currentVolume === 0) return;
        
        const soundsToPlay = [];
        if (isCorrect) {
            if (correctSound) soundsToPlay.push(correctSound);
            if (enemyDamageSound) soundsToPlay.push(enemyDamageSound);
        } else {
            if (wrongSound) soundsToPlay.push(wrongSound);
            if (playerDamageSound) soundsToPlay.push(playerDamageSound);
        }

        soundsToPlay.forEach(sound => {
            try {
                sound.currentTime = 0;
                const playPromise = sound.play();
                if (playPromise && typeof playPromise.catch === 'function') {
                    playPromise.catch(err => console.warn('Audio playback blocked:', err));
                }
            } catch (e) {
                console.error('Error playing feedback sound:', e);
            }
        });
    }

    // Continue to next question
    window.continueToNext = function() {
        // Don't allow continue if game is over
        if (isGameOver) {
            return;
        }
        
        // Hide feedback panel
        const feedbackPanel = document.getElementById('feedbackPanel');
        if (feedbackPanel) {
            feedbackPanel.classList.remove('active');
        }
        
        // Check if this is a multi-question activity and there are more questions
        if (isMultiQuestion && currentQuestionIndex < totalQuestions - 1) {
            // Move to next question
            currentQuestionIndex++;
            loadNextQuestion();
            return;
        }
        
        // Last question - show victory modal (only if not game over)
        if (!isGameOver) {
            showVictoryModal();
        }
        return;
    };

    // Show game over modal
    function showGameOverModal() {
        const gameOverModal = document.getElementById('gameOverModal');
        if (gameOverModal) {
            gameOverModal.classList.add('active');
        }
        
        // Don't play if volume is 0
        const currentVolume = getVolume();
        if (gameOverSound && currentVolume > 0) {
            try {
                gameOverSound.currentTime = 0;
                gameOverSound.play();
            } catch (e) {
                console.error('Error playing game over sound:', e);
            }
        }
    }
    
    // Retry level from game over (no progress saved)
    window.retryLevel = function() {
        // Simply reload the page - no progress saved
        window.location.reload();
    };
    
    // Show code editor failure modal
    function showCodeEditorFailureModal() {
        const failureModal = document.getElementById('codeEditorFailureModal');
        if (failureModal) {
            failureModal.classList.add('active');
        }
    }
    
    // Retry code editor from failure
    window.retryCodeEditor = function() {
        window.location.reload();
    };
    
    // Show victory modal
    function showVictoryModal() {
        // Show the victory modal first - progress will be saved when user clicks a button
        const victoryModal = document.getElementById('victoryModal');
        const victoryMessage = document.getElementById('victoryMessage');
        const nextLevelBtn = document.getElementById('nextLevelBtn');
        
        if (victoryModal) {
            // Build score summary
            let messageText = '';
            
            // Skip score display for code-editor activities (single-attempt challenges)
            if (activityType === 'code-editor') {
                messageText = 'Amazing work! You\'ve mastered this challenge! ';
            } else if (config.hasEnemy !== false) {
                const totalQs = totalQuestions;
                const isPerfectScore = enemyHP === 0;
                
                if (isPerfectScore) {
                    messageText = `
                        <div style="
                            text-align: center;
                            font-family: 'Poppins', sans-serif;
                            line-height: 1.6;
                        ">
                            <h2 style="color:#4CAF50; margin-bottom:6px;">🎯 Perfect Score!</h2>
                            <p style="font-size:16px; margin:0;">
                                <strong>Enemy Defeated!</strong><br>
                                All <strong>${totalQs}</strong> questions correct!
                            </p>
                            <p style="color:#888; margin-top:10px;">
                                <em>Flawless victory!</em>
                            </p>
                        </div>
                    `;

                } else {
                    const wrongAnswers = totalQs - correctAnswersCount;
                    messageText = `
                        <div style="
                            text-align:center;
                            line-height:1.6;
                        ">
                            <p style="font-size:16px; margin:0;">
                                <strong>Score:</strong> ${correctAnswersCount}/${totalQs}<br>
                                <strong>Enemy HP:</strong> ${enemyHP}/${maxEnemyHP}
                            </p>

                            <p style="color:#888; margin-top:12px;">
                                ${wrongAnswers === 1 
                                    ? 'Just <strong>one mistake</strong> — great job!' 
                                    : `<strong>${wrongAnswers}</strong> mistakes — you’re getting better!`
                                }
                            </p>
                        </div>
                    `;

                }
            } else {
                messageText = 'Amazing work! You\'ve mastered this challenge! ';
            }
            
            // Customize message and button based on next level availability
            if (config.hasNextLevel) {
                if (victoryMessage) {
                    victoryMessage.innerHTML = messageText + 'Ready for the next one?';
                }
                if (nextLevelBtn) {
                    nextLevelBtn.textContent = 'CONTINUE TO NEXT LEVEL';
                    nextLevelBtn.style.display = 'block';
                }
            } else {
                if (victoryMessage) {
                    victoryMessage.innerHTML = messageText + `You've completed all levels in this course! 🎓`;
                }
                if (nextLevelBtn) {
                    nextLevelBtn.style.display = 'none';
                }
            }
            
            // Set the complete_level flag now, before showing modal
            const completeLevelInput = document.getElementById('completeLevelInput');
            if (completeLevelInput) {
                completeLevelInput.value = '1';
            }
            
            victoryModal.classList.add('active');
            
            // Don't play if volume is 0
            const currentVolume = getVolume();
            if (victorySound && currentVolume > 0) {
                try {
                    victorySound.currentTime = 0;
                    victorySound.play();
                } catch (e) {
                    console.error('Error playing victory sound:', e);
                }
            }
        }
    }
    
    // Go to next level from victory modal
    window.goToNextLevel = function() {
        // Mark level as complete before submitting
        const completeLevelInput = document.getElementById('completeLevelInput');
        const redirectToInput = document.getElementById('redirectToInput');
        
        if (completeLevelInput) {
            completeLevelInput.value = '1';
        }
        
        // Ensure redirect goes to next level
        if (redirectToInput) {
            redirectToInput.value = 'next';
        }
        
        // Submit the completion form to trigger backend logic
        const quizForm = document.getElementById('quizForm');
        if (quizForm) {
            // Submit the form
            quizForm.submit();
        } else if (config.nextLevelUrl) {
            // Direct navigation to next level
            window.location.href = config.nextLevelUrl;
        } else {
            // Fallback: redirect to dashboard
            window.location.href = 'dashboard.php';
        }
    };
    
    // Exit modal functions
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
        // Check if we're in victory modal (level complete) - if so, save progress first
        const victoryModal = document.getElementById('victoryModal');
        const gameOverModal = document.getElementById('gameOverModal');
        const completeLevelInput = document.getElementById('completeLevelInput');
        const redirectToInput = document.getElementById('redirectToInput');
        const quizForm = document.getElementById('quizForm');
        
        // If game over modal is active, don't save progress - just go to dashboard
        if (gameOverModal && gameOverModal.classList.contains('active')) {
            console.log('DEBUG: Exiting from game over - no progress saved');
            window.location.href = 'dashboard.php';
            return;
        }
        
        // If victory modal is active and level is complete, save progress
        if (victoryModal && victoryModal.classList.contains('active') && completeLevelInput && completeLevelInput.value === '1' && quizForm) {
            // Set redirect to dashboard
            if (redirectToInput) {
                redirectToInput.value = 'dashboard';
            }
            // Submit form to save progress, which will redirect to dashboard
            quizForm.submit();
        } else {
            // Normal exit without completion (from exit modal)
            window.location.href = 'dashboard.php';
        }
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
    
    // Setup Enter key handler for check and continue buttons
    function setupContinueKeyHandler() {
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                // Don't trigger if user is typing in an input field or textarea
                // (except for fill-blank activities where we want Enter to submit)
                const isInInput = e.target.tagName === 'INPUT' && activityType !== 'fill-blank';
                const isInTextarea = e.target.tagName === 'TEXTAREA';
                
                if (isInInput || isInTextarea) {
                    return;
                }
                
                // Check if feedback panel is active (continue button visible)
                const feedbackPanel = document.getElementById('feedbackPanel');
                const continueBtn = document.getElementById('continueBtn');
                const checkBtn = document.getElementById('checkBtn');
                
                // Priority 1: If feedback panel is active, trigger continue button
                if (feedbackPanel && feedbackPanel.classList.contains('active')) {
                    if (continueBtn && !continueBtn.disabled) {
                        e.preventDefault();
                        window.continueToNext();
                    }
                }
                // Priority 2: If check button is enabled, trigger it
                else if (checkBtn && !checkBtn.disabled) {
                    e.preventDefault();
                    checkBtn.click();
                }
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

