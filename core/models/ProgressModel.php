<?php

/**
 * ProgressModel - Database-Driven Progress Model
 * 
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

            // Create mock roadmap for dashboard (7 levels)
            // This maintains backward compatibility with the existing dashboard view
            $mockRoadmap = [];
            for ($i = 1; $i <= 7; $i++) {
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
                    'type' => ($i == 2 || $i == 6) ? 'lecture' : 'practice',
                    'status' => $status,
                    'position' => [
                        'left' => ($i - 1) * 180,
                        'top' => [280, 150, 80, 180, 300, 200, 120][$i - 1]
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
}
