<?php
/**
 * Core Helper Functions
 * 
 * Common utility functions used throughout the application
 * Include this file in controllers to access these helper functions
 */

/**
 * Start or resume a session securely
 * 
 * Safely starts a new session or resumes an existing one
 * Prevents multiple session_start() calls
 * 
 * @return void
 */
function start_session_securely(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if a user is currently logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function is_logged_in(): bool
{
    start_session_securely();
    return isset($_SESSION['user_id']) && isset($_SESSION['user']);
}

/**
 * Require user to be logged in (gatekeeper function)
 * 
 * Redirects to login page if user is not authenticated
 * Call this at the start of protected controllers/pages
 * 
 * @param string $loginPath Path to login page (default: ../views/login.php)
 * @return void
 */
function require_login(string $loginPath = '../views/login.php'): void
{
    if (!is_logged_in()) {
        redirect($loginPath);
    }
}

/**
 * Redirect to a specific path and terminate script
 * 
 * @param string $path Relative or absolute path to redirect to
 * @return void
 */
function redirect(string $path): void
{
    // If it's an AJAX request, send a JSON response instead of a header redirect
    if (is_ajax_request()) {
        $error = $_SESSION['error'] ?? null;
        if ($error) unset($_SESSION['error']);
        
        $success = $_SESSION['success'] ?? null;
        if ($success) unset($_SESSION['success']);

        send_json_response([
            'success' => !$error,
            'redirect' => $path,
            'error' => $error,
            'message' => $success
        ]);
    }

    header("Location: $path");
    exit;
}

/**
 * Check if the current request is an AJAX request
 * 
 * @return bool True if AJAX, false otherwise
 */
function is_ajax_request(): bool
{
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
           (isset($_SERVER['HTTP_ACCEPT']) && 
            strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
}

/**
 * Send a JSON response and terminate the script
 * 
 * @param array $data Data to be encoded as JSON
 * @param int $statusCode HTTP status code (default: 200)
 * @return void
 */
function send_json_response(array $data, int $statusCode = 200): void
{
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

/**
 * Format seconds into human-readable time remaining
 * 
 * @param int $seconds Number of seconds
 * @return string Formatted time string (e.g., "2 hours", "30 minutes")
 */
function formatTimeRemaining(int $seconds): string
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    
    if ($hours > 0) {
        return $hours . ' hour' . ($hours > 1 ? 's' : '');
    } elseif ($minutes > 0) {
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    } else {
        return 'less than a minute';
    }
}

/**
 * Get Font Awesome icon class based on level type
 * 
 * @param string $type Level type (practice, lecture, boss, etc.)
 * @return string Font Awesome icon class
 */
function getLevelIcon(string $type): string
{
    $icons = [
        'practice' => 'fa-code',
        'lecture' => 'fa-chalkboard-teacher',
        'boss' => 'fa-dragon',
        'treasure' => 'fa-gem',
        'start' => 'fa-play-circle',
        'finish' => 'fa-flag-checkered'
    ];
    
    return $icons[$type] ?? 'fa-circle';
}

/**
 * Sanitize output for HTML display
 * 
 * @param string $string String to sanitize
 * @return string Sanitized string
 */
function sanitize_output(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get current user ID from session
 * 
 * @return int|null User ID or null if not logged in
 */
function get_current_user_id(): ?int
{
    start_session_securely();
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current username from session
 * 
 * @return string|null Username or null if not logged in
 */
function get_current_username(): ?string
{
    start_session_securely();
    return $_SESSION['user'] ?? null;
}

/**
 * Set session error message
 * 
 * @param string $message Error message to display
 * @return void
 */
function set_error(string $message): void
{
    start_session_securely();
    $_SESSION['error'] = $message;
}

/**
 * Set session success message
 * 
 * @param string $message Success message to display
 * @return void
 */
function set_success(string $message): void
{
    start_session_securely();
    $_SESSION['success'] = $message;
}

/**
 * Get and clear error message from session
 * 
 * @return string Error message or empty string
 */
function get_error(): string
{
    start_session_securely();
    $error = $_SESSION['error'] ?? '';
    unset($_SESSION['error']);
    return $error;
}

/**
 * Get and clear success message from session
 * 
 * @return string Success message or empty string
 */
function get_success(): string
{
    start_session_securely();
    $success = $_SESSION['success'] ?? '';
    unset($_SESSION['success']);
    return $success;
}

/**
 * Calculate user level from total XP
 * 
 * XP required per level increases: 100, 200, 300, 400, etc.
 * Level 1: 0-99 XP
 * Level 2: 100-299 XP
 * Level 3: 300-599 XP
 * 
 * @param int $xp Total XP points
 * @return int User level
 */
function calculateLevelFromXp(int $xp): int
{
    $level = 1;
    $xpNeeded = 0;
    $increment = 100;
    
    while ($xp >= $xpNeeded + $increment) {
        $xpNeeded += $increment;
        $level++;
        $increment += 100;
    }
    
    return $level;
}

/**
 * Calculate XP needed for next level
 * 
 * @param int $currentLevel Current user level
 * @return int XP needed to reach next level
 */
function calculateXpForLevel(int $currentLevel): int
{
    $xpNeeded = 0;
    $increment = 100;
    
    for ($i = 1; $i < $currentLevel; $i++) {
        $xpNeeded += $increment;
        $increment += 100;
    }
    
    return $xpNeeded;
}

/**
 * Calculate XP progress to next level
 * 
 * @param int $xp Total XP points
 * @return array ['level' => int, 'current_xp' => int, 'next_level_xp' => int, 'progress_percent' => int]
 */
function calculateLevelProgress(int $xp): array
{
    $level = calculateLevelFromXp($xp);
    $currentLevelXp = calculateXpForLevel($level);
    $nextLevelXp = calculateXpForLevel($level + 1);
    
    $xpInCurrentLevel = $xp - $currentLevelXp;
    $xpNeededForNextLevel = $nextLevelXp - $currentLevelXp;
    
    $progressPercent = ($xpNeededForNextLevel > 0) 
        ? round(($xpInCurrentLevel / $xpNeededForNextLevel) * 100)
        : 0;
    
    return [
        'level' => $level,
        'current_xp' => $xpInCurrentLevel,
        'next_level_xp' => $xpNeededForNextLevel,
        'progress_percent' => $progressPercent,
        'total_xp' => $xp
    ];
}
