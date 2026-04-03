<?php
/**
 * Achievement Seeder Script
 * 
 * Reads achievements from /data/achievements.json and populates the database.
 * Safe to run multiple times - uses INSERT ... ON DUPLICATE KEY UPDATE
 */

require_once __DIR__ . '/../core/db_connect.php';

// Read achievements JSON file
$jsonPath = __DIR__ . '/../data/achievements.json';

if (!file_exists($jsonPath)) {
    die("Error: achievements.json not found at {$jsonPath}\n");
}

$jsonContent = file_get_contents($jsonPath);
$achievements = json_decode($jsonContent, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error: Invalid JSON in achievements.json - " . json_last_error_msg() . "\n");
}

if (!is_array($achievements)) {
    die("Error: achievements.json must contain an array of achievements\n");
}

// Prepare the SQL statement
$sql = "INSERT INTO achievements (id, title, description, icon_path) 
        VALUES (:id, :title, :description, :icon_path)
        ON DUPLICATE KEY UPDATE 
            title = VALUES(title),
            description = VALUES(description),
            icon_path = VALUES(icon_path)";

try {
    $stmt = $pdo->prepare($sql);
    
    $insertedCount = 0;
    $updatedCount = 0;
    
    foreach ($achievements as $achievement) {
        // Validate required fields
        if (!isset($achievement['id']) || !isset($achievement['title'])) {
            echo "Warning: Skipping achievement - missing id or title\n";
            continue;
        }
        
        $stmt->execute([
            ':id' => $achievement['id'],
            ':title' => $achievement['title'],
            ':description' => $achievement['description'] ?? '',
            ':icon_path' => $achievement['icon_path'] ?? ''
        ]);
        
        if ($stmt->rowCount() > 0) {
            if ($stmt->rowCount() === 1) {
                $insertedCount++;
                echo "✓ Inserted: {$achievement['title']}\n";
            } else {
                $updatedCount++;
                echo "↻ Updated: {$achievement['title']}\n";
            }
        }
    }
    
    echo "\n✅ Seeding complete!\n";
    echo "   Inserted: {$insertedCount}\n";
    echo "   Updated: {$updatedCount}\n";
    echo "   Total: " . count($achievements) . "\n";
    
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage() . "\n");
}
