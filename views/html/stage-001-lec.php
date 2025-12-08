<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTML Basics - Lecture - WebQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/activity.css">
    <link rel="stylesheet" href="../../assets/css/lecture.css">
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
            <span class="hearts-count" id="heartsCount"><?php echo $_SESSION['hearts'] ?? 10; ?></span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="lecture-container">
            <!-- Wiza Teacher Section -->
            <div class="teacher-section-lecture fade-up">
                <img src="../../assets/img/wiza/wiza-teach.png" alt="Wiza Teacher" class="teacher-image-lecture" id="wizaImage">
                <div class="teacher-bubble-lecture">
                    <div class="teacher-name-lecture">Wiza</div>
                    <div class="teacher-instruction-lecture" id="lectureText">
                        <span id="typewriterText"></span><span class="typewriter-cursor" id="typewriterCursor"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Buttons -->
    <div class="lecture-footer">
        <div class="skip-btn-container">
            <button type="button" class="skip-btn" id="skipBtn" onclick="skipLecture()">SKIP LECTURE</button>
        </div>
        <div class="nav-buttons-container">
            <button type="button" class="back-btn" id="backBtn" onclick="previousStage()">BACK</button>
            <button type="button" class="next-btn" id="nextBtn" onclick="nextStage()">NEXT</button>
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

    <script src="../../assets/js/lecture.js"></script>
</body>
</html>

