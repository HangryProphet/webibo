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
    <title>Loading - WebQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/activity.css">
    <link rel="stylesheet" href="../assets/css/completion.css">
    <link rel="stylesheet" href="../assets/css/loading.css">
</head>
<body>
    <div class="main-content">
        <div class="loading-container">
            <!-- Wiza Image -->
            <img src="../assets/img/wiza/wiza-heart-eyes.png" alt="Wiza Loading" class="loading-image">

            <!-- Loading Text -->
            <h2 class="loading-text">Loading...</h2>

            <!-- Progress Bar -->
            <div class="loading-progress-container">
                <div class="loading-progress-bar">
                    <div class="loading-progress-fill" id="loadingProgress"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/loading.js"></script>
</body>
</html>

