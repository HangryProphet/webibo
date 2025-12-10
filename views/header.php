<?php
/**
 * Header Component
 * 
 * Reusable header with system title on left and navigation menus on top right
 * Includes top-right buttons (Lessons, Streak, Hearts) and navigation menu
 */

// Load helper functions if not already loaded
if (!function_exists('formatTimeRemaining')) {
    require_once __DIR__ . '/../core/functions.php';
}

// Ensure session is started
start_session_securely();

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Determine current page for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF']);
$isDashboard = ($currentPage === 'dashboard.php');
$isAchievements = ($currentPage === 'achievements.php');
$isProfile = ($currentPage === 'profile.php');
?>

<header class="main-header">
    <button class="mobile-menu-toggle" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </button>

    <!-- System Title on Left -->
    <div class="header-logo">
        <a href="dashboard.php" class="logo-link">
            <img src="../assets/img/webibo/webibo-logo.png" alt="Webibo logo" class="logo-img">
            <div class="logo-text">Webibo</div>
        </a>
    </div>

    <!-- Navigation Menus on Top Right -->
    <nav class="header-nav">
        <a href="dashboard.php" class="nav-item <?php echo $isDashboard ? 'active' : ''; ?>">
            <i class="fas fa-map"></i>
            <span class="nav-text">Adventure</span>
        </a>
        
        <a href="achievements.php" class="nav-item <?php echo $isAchievements ? 'active' : ''; ?>">
            <i class="fas fa-trophy"></i>
            <span class="nav-text">Achievements</span>
        </a>

        <a href="profile.php" class="nav-item <?php echo $isProfile ? 'active' : ''; ?>">
            <i class="fas fa-user"></i>
            <span class="nav-text">Profile</span>
        </a>

        <!-- More menu with hover popup -->
        <div class="nav-item-wrapper">
            <a href="#" class="nav-item">
                <i class="fas fa-ellipsis-h"></i>
                <span class="nav-text">More</span>
            </a>
            <div class="header-hover-popup more-popup">
                <div class="popup-header">MORE OPTIONS</div>
                <div class="header-menu-list">
                    <a href="#" class="header-menu-item">
                        <div class="header-menu-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <span class="header-menu-text">Settings</span>
                    </a>
                    <a href="login.php?logout=1" class="header-menu-item">
                        <div class="header-menu-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <span class="header-menu-text">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>

<link rel="stylesheet" href="../assets/css/header.css">
<script src="../assets/js/header.js"></script>

