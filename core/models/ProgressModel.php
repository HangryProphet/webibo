<?php

/**
 * ProgressModel - Database-Driven Progress Model
 * ProgressModel - Database-Driven Progress Model
 * 
 * Handles user learning progress tracking with database operations
 * Handles user learning progress tracking with database operations
 */
class ProgressModel
{
/**
     * Get user learning progress by user ID
     * Returns mock roadmap data for dashboard compatibility
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @return array|false User progress array if found, false otherwise
     */
    public static function getProgressByUserId(PDO $pdo, int $userId): array|false
    {
        try {
            // Get completed level IDs for this user
            $sql = "SELECT level_id, completed_at
                    FROM user_progress 
                    WHERE user_id = :user_id 
                    ORDER BY completed_at ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $completedLevels = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $completedIds = array_map(fn($l) => (int)$l['level_id'], $completedLevels);

            // Create mock roadmap for dashboard (10 levels)
            // This maintains backward compatibility with the existing dashboard view
            $mockRoadmap = [];
            $horizontalSpacing = 180; // Increased from 130 to make nodes wider apart
            
            for ($i = 1; $i <= 10; $i++) {
                $isCompleted = in_array($i, $completedIds);
                $nextLevel = count($completedIds) + 1;
                
                // Determine status
                if ($isCompleted) {
                    $status = 'completed';
                } elseif ($i == $nextLevel) {
                    $status = 'current';
                } else {
                    $status = 'locked';
                }
                
                $mockRoadmap[] = [
                    'level_id' => $i,
                    'level_number' => $i,
                    'type' => ($i == 2 || $i == 6 || $i == 9) ? 'lecture' : 'practice',
                    'status' => $status,
                    'position' => [
                        'left' => ($i - 1) * $horizontalSpacing,
                        'top' => [280, 150, 80, 180, 300, 200, 120, 250, 100, 320][$i - 1]
                    ]
                ];
            }

            return $mockRoadmap;

        } catch (PDOException $e) {
            error_log("ProgressModel::getProgressByUserId Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark a level as completed
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @param int $levelId Level ID to complete
     * @return bool True on success
     */
    public static function completeLevel(PDO $pdo, int $userId, int $levelId): bool
    {
        try {
            $sql = "INSERT INTO user_progress (user_id, level_id, completed_at) 
                    VALUES (:user_id, :level_id, NOW())
                    ON DUPLICATE KEY UPDATE completed_at = NOW()";
            
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':user_id' => $userId,
                ':level_id' => $levelId
            ]);

        } catch (PDOException $e) {
            error_log("ProgressModel::completeLevel Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all completed levels for a user
     * 
     * @param PDO $pdo Database connection
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @return array Array of completed level data
     */
    public static function getCompletedLevels(PDO $pdo, int $userId): array
    {
        try {
            $sql = "SELECT up.level_id, up.completed_at, 
                           l.title, l.level_type, l.xp_reward
                    FROM user_progress up
                    LEFT JOIN levels l ON up.level_id = l.id
                    WHERE up.user_id = :user_id 
                    ORDER BY up.completed_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("ProgressModel::getCompletedLevels Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if a user has completed a specific level
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @param int $levelId Level ID to check
     * @return bool True if completed
     */
    public static function hasCompletedLevel(PDO $pdo, int $userId, int $levelId): bool
    {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM user_progress 
                    WHERE user_id = :user_id AND level_id = :level_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':level_id' => $levelId
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;

        } catch (PDOException $e) {
            error_log("ProgressModel::hasCompletedLevel Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get count of completed levels for a user
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @return int Count of completed levels
     */
    public static function getCompletedLevelsCount(PDO $pdo, int $userId): int
    {
        try {
            $sql = "SELECT COUNT(*) as count 
                    FROM user_progress 
                    WHERE user_id = :user_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['count'];

        } catch (PDOException $e) {
            error_log("ProgressModel::getCompletedLevelsCount Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Mark a level as completed (alias for completeLevel for backward compatibility)
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @param int $levelId Level ID to complete
     * @return bool True on success
     */
    public static function markLevelComplete(PDO $pdo, int $userId, int $levelId): bool
    {
        return self::completeLevel($pdo, $userId, $levelId);
    }

    /**
     * Check if a user has completed all levels in a course
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @param int $courseId Course ID to check
     * @return bool True if all levels completed
     */
    public static function hasCourseCompleted(PDO $pdo, int $userId, int $courseId): bool
    {
        try {
            // Get total levels in the course
            $sqlTotal = "SELECT COUNT(*) as total FROM levels WHERE course_id = :course_id";
            $stmtTotal = $pdo->prepare($sqlTotal);
            $stmtTotal->execute([':course_id' => $courseId]);
            $totalLevels = (int)$stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
            
            if ($totalLevels === 0) {
                return false; // No levels in course
            }
            
            // Get completed levels in this course
            $sqlCompleted = "SELECT COUNT(DISTINCT up.level_id) as completed
                            FROM user_progress up
                            JOIN levels l ON up.level_id = l.id
                            WHERE up.user_id = :user_id AND l.course_id = :course_id";
            $stmtCompleted = $pdo->prepare($sqlCompleted);
            $stmtCompleted->execute([':user_id' => $userId, ':course_id' => $courseId]);
            $completedLevels = (int)$stmtCompleted->fetch(PDO::FETCH_ASSOC)['completed'];
            
            return $completedLevels >= $totalLevels;
            
        } catch (PDOException $e) {
            error_log("ProgressModel::hasCourseCompleted Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Count total completed levels for a user
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @return int Count of completed levels
     */
    public static function countCompletedLevels(PDO $pdo, int $userId): int
    {
        return self::getCompletedLevelsCount($pdo, $userId);
    }

    /**
     * Get recent activity (completed levels) for a user
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @param int $limit Number of recent activities to return (default 5)
     * @return array Array of recent activity with level details
     */
    public static function getRecentActivity(PDO $pdo, int $userId, int $limit = 5): array
    {
        try {
            $sql = "SELECT 
                        up.level_id, 
                        up.completed_at,
                        l.title as level_title,
                        l.level_type,
                        l.xp_reward
                    FROM user_progress up
                    LEFT JOIN levels l ON up.level_id = l.id
                    WHERE up.user_id = :user_id 
                    ORDER BY up.completed_at DESC
                    LIMIT :limit";
            
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("ProgressModel::getRecentActivity Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get array of completed level IDs for a user in a specific course
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @param int|null $courseId Optional course ID to filter by
     * @return array Array of completed level IDs
     */
    public static function getCompletedLevelIds(PDO $pdo, int $userId, ?int $courseId = null): array
    {
        try {
            if ($courseId !== null) {
                $sql = "SELECT up.level_id 
                        FROM user_progress up
                        JOIN levels l ON up.level_id = l.id
                        WHERE up.user_id = :user_id AND l.course_id = :course_id
                        ORDER BY up.completed_at ASC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':user_id' => $userId,
                    ':course_id' => $courseId
                ]);
            } else {
                $sql = "SELECT level_id 
                        FROM user_progress 
                        WHERE user_id = :user_id 
                        ORDER BY completed_at ASC";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':user_id' => $userId]);
            }
            
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return array_map('intval', $results);

        } catch (PDOException $e) {
            error_log("ProgressModel::getCompletedLevelIds Error: " . $e->getMessage());
            return [];
        }
    }
}
