<?php

/**
 * UserModel - Database-Driven User Model
 * UserModel - Database-Driven User Model
 * 
 * Handles all user-related database operations using PDO
 * Implements secure password hashing with password_hash() and password_verify()
 * Handles all user-related database operations using PDO
 * Implements secure password hashing with password_hash() and password_verify()
 */
class UserModel
{
    /**
     * Create a new user with hashed password
     * 
     * @param PDO $pdo Database connection
     * Create a new user with hashed password
     * 
     * @param PDO $pdo Database connection
     * @param string $firstName User's first name
     * @param string $lastName User's last name
     * @param string $email User's email address
     * @param string $username User's username
     * @param string $password User's plain text password (will be hashed)
     * @return int|false User ID on success, false on failure
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
     * @param PDO $pdo Database connection
     * @param string $identifier Username or email address
     * @return array|false User data array if found, false otherwise
     */
    public static function getUserByUsernameOrEmail(PDO $pdo, string $identifier): array|false
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
     * @param PDO $pdo Database connection
     * @param string $email Email address to check
     * @return bool True if email exists, false otherwise
     */
    public static function isEmailTaken(PDO $pdo, string $email): bool
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
     * @param PDO $pdo Database connection
     * @param string $username Username to check
     * @return bool True if username exists, false otherwise
     */
    public static function isUsernameTaken(PDO $pdo, string $username): bool
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
     * @param PDO $pdo Database connection
     * @param string $email User's email address
     * @return bool True on success, false if user not found or error
     * @return bool True on success, false if user not found or error
     */
    public static function verifyUserEmail(PDO $pdo, string $email): bool
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

    /**
     * Verify user's current password
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User ID
     * @param string $password Plain text password to verify
     * @return bool True if password matches, false otherwise
     */
    public static function verifyPassword(PDO $pdo, int $userId, string $password): bool
    {
        try {
            $sql = "SELECT password_hash FROM users WHERE id = :user_id LIMIT 1";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return false;
            }
            
            return password_verify($password, $user['password_hash']);

        } catch (PDOException $e) {
            error_log("UserModel::verifyPassword Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user profile information (first name, last name, username)
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User ID
     * @param array $data Associative array with keys: first_name, last_name, username
     * @return bool True on success, false otherwise
     */
    public static function updateUserProfile(PDO $pdo, int $userId, array $data): bool
    {
        try {
            $updates = [];
            $params = [':user_id' => $userId];
            
            if (isset($data['first_name'])) {
                $updates[] = "first_name = :first_name";
                $params[':first_name'] = $data['first_name'];
            }
            
            if (isset($data['last_name'])) {
                $updates[] = "last_name = :last_name";
                $params[':last_name'] = $data['last_name'];
            }
            
            if (isset($data['username'])) {
                $updates[] = "username = :username";
                $params[':username'] = $data['username'];
            }
            
            if (empty($updates)) {
                return false;
            }
            
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :user_id";
            
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute($params);
            
            return $success && $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("UserModel::updateUserProfile Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update user avatar path
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User ID
     * @param string $avatarPath Path to avatar image
     * @return bool True on success, false otherwise
     */
    public static function updateUserAvatar(PDO $pdo, int $userId, string $avatarPath): bool
    {
        try {
            $sql = "UPDATE users SET avatar_path = :avatar_path WHERE id = :user_id";
            
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                ':avatar_path' => $avatarPath,
                ':user_id' => $userId
            ]);
            
            return $success && $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            error_log("UserModel::updateUserAvatar Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if username is taken by another user (excluding current user)
     * 
     * @param PDO $pdo Database connection
     * @param string $username Username to check
     * @param int $excludeUserId User ID to exclude from check
     * @return bool True if username is taken by another user, false otherwise
     */
    public static function isUsernameTakenByOther(PDO $pdo, string $username, int $excludeUserId): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE username = :username AND id != :user_id";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':user_id' => $excludeUserId
            ]);
            
            return $stmt->fetchColumn() > 0;

        } catch (PDOException $e) {
            error_log("UserModel::isUsernameTakenByOther Error: " . $e->getMessage());
            return false;
        }
    }
}

