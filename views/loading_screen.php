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
    <title>Loading - Webibo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/activity.css">
    <link rel="stylesheet" href="../assets/css/completion.css">
    <link rel="stylesheet" href="../assets/css/loading.css">
</head>
<body>
    <div class="main-content">
        <div class="loading-container">
            <!-- Wiza Image -->
            <img src="../assets/img/wiza/wiza-flying.webp" alt="Wiza Loading" class="loading-image">

            <!-- Progress Bar -->
            <div class="loading-progress-container">
                <div class="loading-progress-bar">
                    <div class="loading-progress-fill" id="loadingProgress"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Magic sound on load -->
    <audio id="magic-sound" src="../assets/sfx/magic-sound.mp3" preload="auto"></audio>

    <script src="../assets/js/loading.js"></script>
    <script>
        (function () {
            // Check volume setting from localStorage
            function getVolume() {
                const savedVolume = localStorage.getItem('webibo_sound_volume');
                return savedVolume !== null ? parseInt(savedVolume) / 100 : 0.7;
            }
            
            const currentVolume = getVolume();
            
            // Don't play if volume is 0
            if (currentVolume === 0) return;
            
            const audio = document.getElementById('magic-sound');
            if (!audio) return;
            
            // Set volume
            audio.volume = currentVolume;
            
            try {
                audio.currentTime = 0;
                const playPromise = audio.play();
                if (playPromise && typeof playPromise.then === 'function') {
                    playPromise.catch(() => {});
                }
            } catch (err) {
                // Autoplay may fail; ignore
            }
        })();
    </script>
</body>
</html>

