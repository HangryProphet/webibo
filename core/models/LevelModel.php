<?php

/**
 * LevelModel - Database-Driven Level Model
 * 
 * Handles all level-related database operations
 */
class LevelModel
{
    /**
     * Get level by ID with course information
     * 
     * @param PDO $pdo Database connection
     * @param int $levelId Level ID
     * @return array|false Level data on success, false on failure
     */
    public static function getLevelById(PDO $pdo, int $levelId): array|false
    {
        try {
            $sql = "SELECT l.*, c.title as course_title, c.description as course_description
                    FROM levels l
                    JOIN courses c ON l.course_id = c.id
                    WHERE l.id = :level_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['level_id' => $levelId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: false;
        } catch (PDOException $e) {
            error_log("LevelModel::getLevelById() - Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all levels for a course
     * 
     * @param PDO $pdo Database connection
     * @param int $courseId Course ID
     * @return array Array of levels ordered by order_in_course
     */
    public static function getLevelsByCourse(PDO $pdo, int $courseId): array
    {
        try {
            $sql = "SELECT * FROM levels 
                    WHERE course_id = :course_id 
                    ORDER BY order_in_course ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['course_id' => $courseId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("LevelModel::getLevelsByCourse() - Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if level exists and matches expected type
     * 
     * @param PDO $pdo Database connection
     * @param int $levelId Level ID
     * @param string $expectedType Expected level type (lecture, multiple-choice, etc.)
     * @return bool True if level exists and type matches
     */
    public static function validateLevelType(PDO $pdo, int $levelId, string $expectedType): bool
    {
        $level = self::getLevelById($pdo, $levelId);
        
        if (!$level) {
            return false;
        }
        
        return $level['level_type'] === $expectedType;
    }

    /**
     * Get next level in course sequence
     * 
     * @param PDO $pdo Database connection
     * @param int $currentLevelId Current level ID
     * @return array|false Next level data or false if none
     */
    public static function getNextLevel(PDO $pdo, int $currentLevelId): array|false
    {
        try {
            // Get current level's order and course
            $currentLevel = self::getLevelById($pdo, $currentLevelId);
            if (!$currentLevel) {
                return false;
            }

            // Get next level in sequence
            $sql = "SELECT * FROM levels 
                    WHERE course_id = :course_id 
                    AND order_in_course > :current_order 
                    ORDER BY order_in_course ASC 
                    LIMIT 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'course_id' => $currentLevel['course_id'],
                'current_order' => $currentLevel['order_in_course']
            ]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: false;
        } catch (PDOException $e) {
            error_log("LevelModel::getNextLevel() - Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get content file path for a level
     * 
     * @param int $levelId Level ID
     * @param string $levelType Level type (lecture, multiple-choice, etc.)
     * @return string File path relative to project root
     */
    public static function getContentFilePath(int $levelId, string $levelType): string
    {
        $extension = ($levelType === 'lecture') ? 'html' : 'json';
        return __DIR__ . "/../../data/html/{$levelId}.{$extension}";
    }

    /**
     * Read level content file
     * 
     * @param int $levelId Level ID
     * @param string $levelType Level type
     * @param bool $decode If true and JSON file, decode to array
     * @return string|array|false File contents or false on failure
     */
    public static function readContentFile(int $levelId, string $levelType, bool $decode = false): string|array|false
    {
        $filePath = self::getContentFilePath($levelId, $levelType);
        
        if (!file_exists($filePath)) {
            error_log("LevelModel::readContentFile() - File not found: {$filePath}");
            return false;
        }
        
        $contents = file_get_contents($filePath);
        
        if ($contents === false) {
            error_log("LevelModel::readContentFile() - Failed to read: {$filePath}");
            return false;
        }
        
        // Decode JSON files if requested
        if ($decode && $levelType !== 'lecture') {
            $decoded = json_decode($contents, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("LevelModel::readContentFile() - JSON decode error: " . json_last_error_msg());
                return false;
            }
            
            return $decoded;
        }
        
        return $contents;
    }
}
