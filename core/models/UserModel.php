<?php

/**
 * UserModel - Mock User Model (Prototype)
 * 
 * This is a temporary mock model that simulates database operations using a static array.
 * This will be replaced with actual database operations in the future.
 */
class UserModela
{
    /**
     * Mock database - Static array to simulate user storage
     * In production, this will be replaced with actual database queries
     */
    private static $users = [
        [
            'id' => 1,
            'username' => 'user1',
            'email' => 'user1@example.com',
            'password' => '123', // In production, this would be hashed
            'first_name' => 'Demo',
            'last_name' => 'User',
            'is_verified' => true,
            'created_at' => '2025-12-01 10:00:00'
        ]
    ];

    /**
     * Create a new user
     * 
     * @param string $firstName User's first name
     * @param string $lastName User's last name
     * @param string $email User's email address
     * @param string $username User's username
     * @param string $password User's password (will be stored as plain text in mock)
     * @return bool True on success, false on failure
     */
    public static function createUser(string $firstName, string $lastName, string $email, string $username, string $password): bool
    {
        // Check if email or username already exists
        if (self::isEmailTaken($email) || self::isUsernameTaken($username)) {
            return false;
        }

        // Generate new ID (increment from last user)
        $newId = empty(self::$users) ? 1 : max(array_column(self::$users, 'id')) + 1;

        // Create new user array
        $newUser = [
            'id' => $newId,
            'username' => $username,
            'email' => $email,
            'password' => $password, // In production, use password_hash()
            'first_name' => $firstName,
            'last_name' => $lastName,
            'is_verified' => false, // New users need to verify email
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Add to mock database
        self::$users[] = $newUser;

        return true;
    }

    /**
     * Get user by username or email
     * 
     * @param string $identifier Username or email address
     * @return array|false User data array if found, false otherwise
     */
    public static function getUserByUsernameOrEmail(string $identifier): array|false
    {
        foreach (self::$users as $user) {
            if ($user['username'] === $identifier || $user['email'] === $identifier) {
                return $user;
            }
        }

        return false;
    }

    /**
     * Check if email is already taken
     * 
     * @param string $email Email address to check
     * @return bool True if email exists, false otherwise
     */
    public static function isEmailTaken(string $email): bool
    {
        foreach (self::$users as $user) {
            if ($user['email'] === $email) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if username is already taken
     * 
     * @param string $username Username to check
     * @return bool True if username exists, false otherwise
     */
    public static function isUsernameTaken(string $username): bool
    {
        foreach (self::$users as $user) {
            if ($user['username'] === $username) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify user's email (set is_verified to true)
     * 
     * @param string $email User's email address
     * @return bool True on success, false if user not found
     */
    public static function verifyUserEmail(string $email): bool
    {
        foreach (self::$users as &$user) {
            if ($user['email'] === $email) {
                $user['is_verified'] = true;
                return true;
            }
        }

        return false;
    }

    /**
     * Get all users (for debugging purposes)
     * 
     * @return array All users in the mock database
     */
    public static function getAllUsers(): array
    {
        return self::$users;
    }

    /**
     * Reset mock database to initial state (useful for testing)
     * 
     * @return void
     */
    public static function resetDatabase(): void
    {
        self::$users = [
            [
                'id' => 1,
                'username' => 'user1',
                'email' => 'user1@example.com',
                'password' => '123',
                'first_name' => 'Demo',
                'last_name' => 'User',
                'is_verified' => true,
                'created_at' => '2025-12-01 10:00:00'
            ]
        ];
    }
}