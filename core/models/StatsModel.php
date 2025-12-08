<?php

/**
 * StatsModel - Database-Driven Stats Model
 * 
 * Handles user statistics (hearts, streaks, courses) with database operations
 */
class StatsModel
{
    /**
     * Get user statistics by user ID
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @return array|false User stats array if found, false otherwise
     */
    public static function getStatsByUserId(PDO $pdo, int $userId): array|false
    {
        try {
            // Get user stats
            $sql = "SELECT * FROM user_stats WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$stats) {
                // Create default stats if not found
                self::createDefaultStats($pdo, $userId);
                return self::getStatsByUserId($pdo, $userId);
            }

            // Get all courses for display
            $sql = "SELECT id, title as name, description FROM courses";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $allCourses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculate streak reset time (based on last login)
            $lastLogin = $stats['last_login_date'] ? strtotime($stats['last_login_date'] . ' +1 day') : time();
            $resetIn = max(0, $lastLogin - time());

            // Generate weekly progress (simplified - all false for now)
            $weeklyProgress = array_fill(0, 7, false);

            return [
                "hearts" => [
                    "current" => 10,
                    "max" => 10,
                    "next_heart_in_seconds" => 0
                ],
                "streak" => [
                    "current_days" => (int) $stats['current_streak'],
                    "reset_in_seconds" => $resetIn,
                    "weekly_progress" => $weeklyProgress,
                    "target_days" => 30
                ],
                "courses" => $allCourses,
                "xp_points" => (int) $stats['xp_points'],
                "longest_streak" => (int) $stats['longest_streak']
            ];

        } catch (PDOException $e) {
            error_log("StatsModel::getStatsByUserId Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create default stats for a new user
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @return bool True on success
     */
    private static function createDefaultStats(PDO $pdo, int $userId): bool
    {
        try {
            $sql = "INSERT INTO user_stats (user_id, xp_points, current_streak, longest_streak, last_login_date) 
                    VALUES (:user_id, 0, 0, 0, CURDATE())";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("StatsModel::createDefaultStats Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user hearts count
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @param int $hearts New hearts count
     * @return bool True on success
     */
    public static function updateHearts(PDO $pdo, int $userId, int $hearts): bool
    {
        try {
            $sql = "UPDATE user_stats 
                    SET hearts_current = :hearts, last_heart_regen = NOW() 
                    WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':hearts' => max(0, $hearts),
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("StatsModel::updateHearts Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Decrease user hearts by one
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @return bool True on success
     */
    public static function decreaseHearts(PDO $pdo, int $userId): bool
    {
        try {
            $sql = "UPDATE user_stats 
                    SET hearts_current = GREATEST(hearts_current - 1, 0) 
                    WHERE user_id = :user_id AND hearts_current > 0";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("StatsModel::decreaseHearts Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user streak
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @param int $days New streak days count
     * @return bool True on success
     */
    public static function updateStreak(PDO $pdo, int $userId, int $days): bool
    {
        try {
            $sql = "UPDATE user_stats 
                    SET streak_days = :days, last_activity_date = CURDATE() 
                    WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':days' => max(0, $days),
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("StatsModel::updateStreak Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update weekly progress pattern
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User's ID
     * @param array $weeklyProgress Array of 7 booleans
     * @return bool True on success
     */
    public static function updateWeeklyProgress(PDO $pdo, int $userId, array $weeklyProgress): bool
    {
        try {
            if (count($weeklyProgress) !== 7) {
                return false;
            }

            // Convert boolean array to string (e.g., [true, false, true] => "101")
            $pattern = implode('', array_map(fn($v) => $v ? '1' : '0', $weeklyProgress));

            $sql = "UPDATE user_stats SET weekly_streak_pattern = :pattern WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                ':pattern' => $pattern,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("StatsModel::updateWeeklyProgress Error: " . $e->getMessage());
            return false;
        }
    }
}
