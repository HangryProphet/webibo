<?php
// Settings UI only; no backend functionality is wired here.
require_once 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/settings.css">
    <script src="../assets/js/settings.js" defer></script>
    <title>Settings</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container fade-up">
        <br><div class="page-header">
            <h1 class="title">Settings</h1>
            <p class="subtitle">Adjust your preferences for this Webibo session.</p>
        </div>

        <div class="card">
            <p class="section-title">Sound</p>
            <div class="row">
                <div class="label-block">
                    <span class="label">Sound effects volume</span>
                    <span class="hint">Fine-tune how loud sound effects play.</span>
                </div>
                <div class="volume-control">
                    <input type="range" name="sound_volume" min="0" max="100" value="70" aria-label="Sound effects volume">
                    <span class="hint" aria-hidden="true">70%</span>
                </div>
            </div>
        </div>

        <div class="card">
            <p class="section-title">Guides</p>
            <div class="row" style="border-bottom: none; padding-bottom: 6px;">
                <div class="label-block">
                    <span class="label">Tutorial</span>
                    <span class="hint">Quick overview of how Webibo works.</span>
                </div>
                <a class="action-btn" href="how_to_play.php">Open</a>
            </div>
            <div class="row" style="border-bottom: none; padding-top: 0;">
                <div class="label-block">
                    <span class="label">Help</span>
                    <span class="hint">Need assistance? Jump to the help page.</span>
                </div>
                <a class="action-btn" href="help.php">Go</a>
            </div>
        </div>

        <div class="card" style="margin-top: 8px;">
            <div class="row" style="border-bottom: none;">
                <div class="label-block">
                    <span class="label">App version</span>
                    <span class="hint">You are running the current build.</span>
                </div>
                <span class="label" style="font-size: 14px; color: #cbd5e1;">v1.0.0</span>
            </div>
        </div>

        <div class="footer">
            <a href="/views/terms.php">Terms of Service</a> |
            <a href="/views/privacy.php">Privacy Policy</a>
        </div>
    </div>
</body>
</html>

