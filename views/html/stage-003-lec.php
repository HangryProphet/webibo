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
    <script>
        // Configure this lecture to be static (no typewriter animation)
        window.lectureConfig = {
            disableAnimation: true,
            stages: [
                {
                    text: `
                        <h1>HTML Basics</h1>
                        <p>HTML (HyperText Markup Language) is the standard language for building webpages. It uses elements to tell the browser how to structure and display content.</p>
                        <h2>Common Elements</h2>
                        <ul>
                            <li><code>&lt;h1&gt;...&lt;/h1&gt;</code> headings for page titles and section labels.</li>
                            <li><code>&lt;p&gt;...&lt;/p&gt;</code> paragraphs for text.</li>
                            <li><code>&lt;a href=""&gt;...&lt;/a&gt;</code> links to other pages or sections.</li>
                            <li><code>&lt;img src="" alt=""&gt;</code> images with alternate text for accessibility.</li>
                            <li><code>&lt;div&gt;</code> and <code>&lt;span&gt;</code> for grouping and styling content.</li>
                        </ul>
                        <h2>Basic Page Structure</h2>
                        <pre><code>&lt;!DOCTYPE html&gt;
&lt;html lang="en"&gt;
  &lt;head&gt;
    &lt;meta charset="UTF-8"&gt;
    &lt;title&gt;My First Page&lt;/title&gt;
  &lt;/head&gt;
  &lt;body&gt;
    &lt;h1&gt;Hello, Web!&lt;/h1&gt;
    &lt;p&gt;This is my first HTML page.&lt;/p&gt;
  &lt;/body&gt;
&lt;/html&gt;
                        </code></pre>
                        <p>Remember: keep your structure clear, use semantic tags when possible, and always include alt text for images. When you're ready, click Finish to continue.</p>
                    `
                }
            ]
        };
    </script>
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
                        <span id="typewriterText"></span>
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
            <button type="button" class="next-btn" id="nextBtn" onclick="nextStage()">FINISH</button>
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

