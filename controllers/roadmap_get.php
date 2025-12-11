<?php
/**
 * Roadmap Get Controller (Simplified)
 * 
 * Returns ALL levels for a course in a single request (no pagination)
 * Used by dashboard.js to render the complete learning path
 */

// Set JSON response headers
header('Content-Type: application/json');

// Load helper functions
require_once __DIR__ . '/../core/functions.php';

// Require user to be logged in
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Load database connection
$pdo = require_once __DIR__ . '/../core/db_connect.php';

// Load required models
require_once __DIR__ . '/../core/models/LevelModel.php';
require_once __DIR__ . '/../core/models/ProgressModel.php';

// Get parameters
$courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 1; // Default to HTML course

// Get user ID
$userId = get_current_user_id();

try {
    // Fetch ALL levels for this course (no pagination)
    $levels = LevelModel::getLevelsByCourse($pdo, $courseId);
    
    // Get user's completed levels
    $completedLevels = ProgressModel::getCompletedLevelIds($pdo, $userId, $courseId);
    
    // Process each level and determine status
    $roadmapData = [];
    $horizontalSpacing = 180; // Horizontal spacing between nodes
    $verticalVariation = [280, 150, 80, 180, 300, 200, 120, 250, 100, 320]; // Variation pattern
    
    foreach ($levels as $index => $level) {
        $levelId = (int)$level['id'];
        $orderInCourse = (int)$level['order_in_course'];
        
        // Determine node status
        $status = determineNodeStatus($levelId, $level['parent_level_id'], $completedLevels);
        
        // Calculate position
        $absoluteIndex = $orderInCourse - 1;
        $leftPosition = $absoluteIndex * $horizontalSpacing;
        $topPosition = $verticalVariation[$absoluteIndex % count($verticalVariation)];
        
        // Determine icon based on level type
        $icon = getLevelIconClass($level['level_type']);
        
        // Build roadmap node data
        $roadmapData[] = [
            'id' => $levelId,
            'level_id' => $levelId,
            'course_id' => (int)$level['course_id'],
            'level_number' => $orderInCourse,
            'type' => $level['level_type'],
            'title' => $level['title'],
            'xp_reward' => (int)$level['xp_reward'],
            'status' => $status,
            'icon' => $icon,
            'position' => [
                'left' => $leftPosition,
                'top' => $topPosition
            ]
        ];
    }
    
    // Return the roadmap data array directly
    echo json_encode($roadmapData);
    
} catch (Exception $e) {
    error_log("Roadmap Get Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}

/**
 * Determine the status of a level node (completed, current, or locked)
 * 
 * @param int $levelId Current level ID
 * @param int|null $parentLevelId Parent level ID
 * @param array $completedLevels Array of completed level IDs
 * @return string Status: 'completed', 'current', or 'locked'
 */
function determineNodeStatus(int $levelId, ?int $parentLevelId, array $completedLevels): string
{
    // Check if this level is completed
    if (in_array($levelId, $completedLevels)) {
        return 'completed';
    }
    
    // Check if this is the first level (no parent)
    if ($parentLevelId === null) {
        return 'current'; // First level is always unlocked
    }
    
    // Check if parent level is completed
    if (in_array($parentLevelId, $completedLevels)) {
        return 'current'; // Parent completed, this level is unlocked
    }
    
    // Parent not completed, this level is locked
    return 'locked';
}

/**
 * Get Font Awesome icon class for level type
 * 
 * @param string $levelType Level type from database
 * @return string Font Awesome icon class
 */
function getLevelIconClass(string $levelType): string
{
    switch($levelType) {
        case 'lecture':
            return 'fa-book';
        case 'multiple-choice':
            return 'fa-tasks';
        case 'fill-blank':
            return 'fa-pen';
        case 'code-editor':
            return 'fa-code';
        default:
            return 'fa-circle';
    }
}
