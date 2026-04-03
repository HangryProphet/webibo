<?php
/**
 * AchievementModel
 * 
 * Handles database operations for achievements
 */

class AchievementModel {
    
    /**
     * Get all achievements
     * 
     * @param PDO $pdo
     * @return array|false
     */
    public static function getAllAchievements(PDO $pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM achievements ORDER BY id ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch achievements: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get achievement by ID
     * 
     * @param PDO $pdo
     * @param int $achievementId
     * @return array|false
     */
    public static function getAchievementById(PDO $pdo, int $achievementId) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM achievements WHERE id = :id");
            $stmt->execute([':id' => $achievementId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch achievement: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all achievements earned by a user
     * 
     * @param PDO $pdo
     * @param int $userId
     * @return array|false
     */
    public static function getUserAchievements(PDO $pdo, int $userId) {
        try {
            $sql = "SELECT a.*, ua.earned_at 
                    FROM achievements a
                    INNER JOIN user_achievements ua ON a.id = ua.achievement_id
                    WHERE ua.user_id = :user_id
                    ORDER BY ua.earned_at DESC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch user achievements: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if user has specific achievement
     * 
     * @param PDO $pdo
     * @param int $userId
     * @param int $achievementId
     * @return bool
     */
    public static function hasAchievement(PDO $pdo, int $userId, int $achievementId): bool {
        try {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM user_achievements 
                 WHERE user_id = :user_id AND achievement_id = :achievement_id"
            );
            $stmt->execute([
                ':user_id' => $userId,
                ':achievement_id' => $achievementId
            ]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Failed to check achievement: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Award achievement to user
     * 
     * @param PDO $pdo
     * @param int $userId
     * @param int $achievementId
     * @return bool
     */
    public static function awardAchievement(PDO $pdo, int $userId, int $achievementId): bool {
        try {
            // Check if already awarded
            if (self::hasAchievement($pdo, $userId, $achievementId)) {
                return true; // Already has it, consider it a success
            }
            
            $stmt = $pdo->prepare(
                "INSERT INTO user_achievements (user_id, achievement_id) 
                 VALUES (:user_id, :achievement_id)"
            );
            
            return $stmt->execute([
                ':user_id' => $userId,
                ':achievement_id' => $achievementId
            ]);
        } catch (PDOException $e) {
            error_log("Failed to award achievement: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get achievement count for user
     * 
     * @param PDO $pdo
     * @param int $userId
     * @return int
     */
    public static function getAchievementCount(PDO $pdo, int $userId): int {
        try {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*) FROM user_achievements WHERE user_id = :user_id"
            );
            $stmt->execute([':user_id' => $userId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Failed to count achievements: " . $e->getMessage());
            return 0;
        }
    }
}
