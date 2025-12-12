<?php
/**
 * AchievementService
 * 
 * Central service for checking and awarding achievements.
 * This is the "brain" that determines when achievements should be awarded.
 */

require_once __DIR__ . '/../models/AchievementModel.php';
require_once __DIR__ . '/../models/ProgressModel.php';

class AchievementService {
    
    /**
     * Award achievement to user (with duplicate check)
     * 
     * @param PDO $pdo
     * @param int $userId
     * @param int $achievementId
     * @return bool
     */
    public static function awardAchievement(PDO $pdo, int $userId, int $achievementId): bool {
        return AchievementModel::awardAchievement($pdo, $userId, $achievementId);
    }
    
    /**
     * Main function: Check and award achievements based on events
     * 
     * @param PDO $pdo
     * @param int $userId
     * @param string $event - Event trigger ('user_registered', 'email_verified', 'level_completed')
     * @param array $context - Additional data (e.g., ['level_id' => 2])
     * @return array - Array of achievement IDs that were awarded
     */
    public static function checkAchievementsOnEvent(PDO $pdo, int $userId, string $event, array $context = []): array {
        $awardedAchievements = [];
        
        switch ($event) {
            case 'user_registered':
                // Achievement #1: "Hello, World!" - Create account
                if (self::awardAchievement($pdo, $userId, 1)) {
                    $awardedAchievements[] = 1;
                }
                break;
                
            case 'email_verified':
                // Achievement #2: "Verified!" - Verify email
                if (self::awardAchievement($pdo, $userId, 2)) {
                    $awardedAchievements[] = 2;
                }
                break;
                
            case 'level_completed':
                $levelId = $context['level_id'] ?? null;
                
                if ($levelId === null) {
                    error_log("level_completed event triggered without level_id");
                    break;
                }
                
                // Check for level-based achievements
                $awarded = self::checkLevelAchievements($pdo, $userId, $levelId);
                $awardedAchievements = array_merge($awardedAchievements, $awarded);
                break;
                
            default:
                error_log("Unknown achievement event: {$event}");
        }
        
        return $awardedAchievements;
    }
    
    /**
     * Check achievements related to completing specific levels
     * 
     * @param PDO $pdo
     * @param int $userId
     * @param int $levelId
     * @return array - Achievement IDs awarded
     */
    private static function checkLevelAchievements(PDO $pdo, int $userId, int $levelId): array {
        $awarded = [];
        
        // Get level details
        $level = self::getLevelById($pdo, $levelId);
        if (!$level) {
            return $awarded;
        }
        
        // Achievement #3: "First Commit" - Complete first level (any level)
        $completedCount = ProgressModel::getCompletedLevelsCount($pdo, $userId);
        if ($completedCount === 1) {
            if (self::awardAchievement($pdo, $userId, 3)) {
                $awarded[] = 3;
            }
        }
        
        // HTML Course specific achievements (course_id = 1)
        if ($level['course_id'] == 1) {
            $htmlAchievements = self::checkHtmlCourseAchievements($pdo, $userId, $levelId, $level);
            $awarded = array_merge($awarded, $htmlAchievements);
        }
        
        return $awarded;
    }
    
    /**
     * Check HTML course-specific achievements
     * 
     * @param PDO $pdo
     * @param int $userId
     * @param int $levelId
     * @param array $level
     * @return array - Achievement IDs awarded
     */
    private static function checkHtmlCourseAchievements(PDO $pdo, int $userId, int $levelId, array $level): array {
        $awarded = [];
        
        // Achievement #6: "Multiple Victor" - Complete level 2 (first multiple-choice quiz)
        if ($levelId == 2 && $level['level_type'] === 'multiple-choice') {
            if (self::awardAchievement($pdo, $userId, 6)) {
                $awarded[] = 6;
            }
        }
        
        // Achievement #7: "Blank Slate" - Complete level 3 (first fill-blank)
        if ($levelId == 3 && $level['level_type'] === 'fill-blank') {
            if (self::awardAchievement($pdo, $userId, 7)) {
                $awarded[] = 7;
            }
        }
        
        // Achievement #8: "Syntax Seal of Approval" - Complete level 4 (first code-editor)
        if ($levelId == 4 && $level['level_type'] === 'code-editor') {
            if (self::awardAchievement($pdo, $userId, 8)) {
                $awarded[] = 8;
            }
        }
        
        // Achievement #9: "Heading in the Right Direction" - Complete level 5 (Headings lecture)
        if ($levelId == 5) {
            if (self::awardAchievement($pdo, $userId, 9)) {
                $awarded[] = 9;
            }
        }
        
        // Achievement #10: "Chain Link" - Complete level 9 (Links lecture)
        if ($levelId == 9) {
            if (self::awardAchievement($pdo, $userId, 10)) {
                $awarded[] = 10;
            }
        }
        
        // Achievement #11: "A Pretty Picture" - Complete level 13 (Images lecture)
        if ($levelId == 13) {
            if (self::awardAchievement($pdo, $userId, 11)) {
                $awarded[] = 11;
            }
        }
        
        // Achievement #12: "List-o-mania" - Complete level 17 (Lists lecture)
        if ($levelId == 17) {
            if (self::awardAchievement($pdo, $userId, 12)) {
                $awarded[] = 12;
            }
        }
        
        // Achievement #14: "HTML Foundation Master" - Complete all 20 levels of HTML course
        $htmlLevelsCompleted = self::getHtmlCourseLevelsCompleted($pdo, $userId);
        if ($htmlLevelsCompleted >= 20) {
            if (self::awardAchievement($pdo, $userId, 14)) {
                $awarded[] = 14;
            }
        }
        
        return $awarded;
    }
    
    /**
     * Get level details by ID
     * 
     * @param PDO $pdo
     * @param int $levelId
     * @return array|false
     */
    private static function getLevelById(PDO $pdo, int $levelId) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM levels WHERE id = :id");
            $stmt->execute([':id' => $levelId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to fetch level: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get count of completed HTML course levels for user
     * 
     * @param PDO $pdo
     * @param int $userId
     * @return int
     */
    private static function getHtmlCourseLevelsCompleted(PDO $pdo, int $userId): int {
        try {
            $sql = "SELECT COUNT(DISTINCT up.level_id) 
                    FROM user_progress up
                    INNER JOIN levels l ON up.level_id = l.id
                    WHERE up.user_id = :user_id AND l.course_id = 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Failed to count HTML levels: " . $e->getMessage());
            return 0;
        }
    }
}
