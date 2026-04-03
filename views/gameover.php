<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Over - WebQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/activity.css">
    <link rel="stylesheet" href="../assets/css/completion.css">
</head>
<body>
    <div class="main-content">
        <div class="completion-container">
            <!-- Wiza Image -->
            <img src="../assets/img/wiza/wiza-sad.png" alt="Wiza Sad" class="completion-image">

            <!-- Title -->
            <h1 class="completion-title error">Game Over</h1>

            <!-- Message -->
            <p class="completion-message">
                You've used up all your hearts. You can always try again!
            </p>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="dashboard.php" class="action-btn action-btn-secondary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="#" class="action-btn action-btn-primary" onclick="history.back(); return false;">
                    <i class="fas fa-undo"></i> Try Again
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/completion.js"></script>
</body>
</html>


