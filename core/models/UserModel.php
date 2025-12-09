<?php

/**
 * UserModel - Database-Driven User Model
 * 
 * Handles all user-related database operations using PDO
 * Implements secure password hashing with password_hash() and password_verify()
 */
class UserModel
{
    /**
     * Create a new user with hashed password
     * 
     * @param PDO $pdo Database connection
     * @param string $firstName User's first name
     * @param string $lastName User's last name
     * @param string $email User's email address
     * @param string $username User's username
     * @param string $password User's plain text password (will be hashed)
     * @return int|false User ID on success, false on failure
     */
    public static function createUser(PDO $pdo, string $firstName, string $lastName, string $email, string $username, string $password): int|false
    {
        try {
            // Hash the password securely
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Prepare SQL statement
            $sql = "INSERT INTO users (username, email, password_hash, first_name, last_name, is_verified) 
                    VALUES (:username, :email, :password_hash, :first_name, :last_name, FALSE)";
            
            $stmt = $pdo->prepare($sql);
            
            // Execute with parameters
            $success = $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password_hash' => $passwordHash,
                ':first_name' => $firstName,
                ':last_name' => $lastName
            ]);

            if ($success) {
                return (int) $pdo->lastInsertId();
            }

            return false;

        } catch (PDOException $e) {
            error_log("UserModel::createUser Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by username or email
     * 
     * @param PDO $pdo Database connection
     * @param string $identifier Username or email address
     * @return array|false User data array if found, false otherwise
     */
    public static function getUserByUsernameOrEmail(PDO $pdo, string $identifier): array|false
    {
        try {
            $sql = "SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$identifier, $identifier]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user ?: false;

        } catch (PDOException $e) {
            error_log("UserModel::getUserByUsernameOrEmail Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user by ID
     * 
     * @param PDO $pdo Database connection
     * @param int $id User ID
     * @return array|false User data array if found, false otherwise
     */
    public static function getUserById(PDO $pdo, int $id): array|false
    {
        try {
            $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user ?: false;

        } catch (PDOException $e) {
            error_log("UserModel::getUserById Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if email is already taken
     * 
     * @param PDO $pdo Database connection
     * @param string $email Email address to check
     * @return bool True if email exists, false otherwise
     */
    public static function isEmailTaken(PDO $pdo, string $email): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            
            return $stmt->fetchColumn() > 0;

        } catch (PDOException $e) {
            error_log("UserModel::isEmailTaken Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if username is already taken
     * 
     * @param PDO $pdo Database connection
     * @param string $username Username to check
     * @return bool True if username exists, false otherwise
     */
    public static function isUsernameTaken(PDO $pdo, string $username): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':username' => $username]);
            
            return $stmt->fetchColumn() > 0;

        } catch (PDOException $e) {
            error_log("UserModel::isUsernameTaken Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify user's email (set is_verified to true)
     * 
     * @param PDO $pdo Database connection
     * @param string $email User's email address
     * @return bool True on success, false if user not found or error
     */
    public static function verifyUserEmail(PDO $pdo, string $email): bool
    {
        try {
            $sql = "UPDATE users SET is_verified = TRUE WHERE email = :email";
            
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([':email' => $email]);
            
            return $success && $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("UserModel::verifyUserEmail Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total user count
     * 
     * @param PDO $pdo Database connection
     * @return int Total number of users
     */
    public static function getUserCount(PDO $pdo): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM users";
            
            $stmt = $pdo->query($sql);
            
            return (int) $stmt->fetchColumn();

        } catch (PDOException $e) {
            error_log("UserModel::getUserCount Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Update user password
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User ID
     * @param string $newPassword New plain text password (will be hashed)
     * @return bool True on success, false otherwise
     */
    public static function updatePassword(PDO $pdo, int $userId, string $newPassword): bool
    {
        try {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            $sql = "UPDATE users SET password_hash = :password_hash WHERE id = :user_id";
            
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                ':password_hash' => $passwordHash,
                ':user_id' => $userId
            ]);
            
            return $success && $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("UserModel::updatePassword Error: " . $e->getMessage());
            return false;
        }
    }
}
