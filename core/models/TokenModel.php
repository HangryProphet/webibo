<?php
/**
 * TokenModel - Email Verification Token Management
 * 
 * Handles creation and validation of secure verification tokens
 * Tokens are hashed before storage for security
 */

class TokenModel
{
    /**
     * Create a secure verification token for email verification
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User ID to create token for
     * @return string The unhashed token (to be sent in email)
     */
    public static function createVerificationToken(PDO $pdo, int $userId): string
    {
        try {
            // Generate cryptographically secure random token (32 bytes = 64 hex characters)
            $rawToken = bin2hex(random_bytes(32));
            
            // Hash the token before storing (same security as passwords)
            $hashedToken = password_hash($rawToken, PASSWORD_DEFAULT);
            
            // Set expiration to 24 hours from now
            $expiresAt = date('Y-m-d H:i:s', time() + (24 * 60 * 60));
            
            // Delete any existing tokens for this user (cleanup)
            $deleteStmt = $pdo->prepare("DELETE FROM email_verifications WHERE user_id = ?");
            $deleteStmt->execute([$userId]);
            
            // Insert new token
            $stmt = $pdo->prepare("
                INSERT INTO email_verifications (user_id, token_hash, expires_at)
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([$userId, $hashedToken, $expiresAt]);
            
            // Return the unhashed token (this is what goes in the email)
            return $rawToken;
            
        } catch (PDOException $e) {
            error_log("TokenModel::createVerificationToken failed: " . $e->getMessage());
            return '';
        }
    }
    
    /**
     * Validate a verification token and verify the user
     * 
     * @param PDO $pdo Database connection
     * @param string $token The raw token from the URL
     * @return bool True if token is valid and user verified, false otherwise
     */
    public static function validateVerificationToken(PDO $pdo, string $token): bool
    {
        try {
            // Get all non-expired tokens
            $stmt = $pdo->prepare("
                SELECT id, user_id, token_hash 
                FROM email_verifications 
                WHERE expires_at > NOW()
            ");
            
            $stmt->execute();
            $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Check each token hash against the provided token
            foreach ($tokens as $row) {
                if (password_verify($token, $row['token_hash'])) {
                    // Token is valid! Update user verification status
                    $updateStmt = $pdo->prepare("
                        UPDATE users 
                        SET is_verified = TRUE 
                        WHERE id = ?
                    ");
                    
                    $updateStmt->execute([$row['user_id']]);
                    
                    // Delete the used token
                    $deleteStmt = $pdo->prepare("DELETE FROM email_verifications WHERE id = ?");
                    $deleteStmt->execute([$row['id']]);
                    
                    return true;
                }
            }
            
            // No matching token found
            return false;
            
        } catch (PDOException $e) {
            error_log("TokenModel::validateVerificationToken failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if a user has a pending verification token
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User ID to check
     * @return bool True if pending token exists
     */
    public static function hasPendingToken(PDO $pdo, int $userId): bool
    {
        try {
            $stmt = $pdo->prepare("
                SELECT COUNT(*) as count 
                FROM email_verifications 
                WHERE user_id = ? AND expires_at > NOW()
            ");
            
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("TokenModel::hasPendingToken failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate a verification token and return the user ID
     * 
     * @param PDO $pdo Database connection
     * @param string $token The raw token from the URL
     * @return int|false User ID if valid, false otherwise
     */
    public static function validateAndGetUserId(PDO $pdo, string $token)
    {
        try {
            // Get all non-expired tokens
            $stmt = $pdo->prepare("
                SELECT id, user_id, token_hash 
                FROM email_verifications 
                WHERE expires_at > NOW()
            ");
            
            $stmt->execute();
            $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Check each token hash against the provided token
            foreach ($tokens as $row) {
                if (password_verify($token, $row['token_hash'])) {
                    // Token is valid! Update user verification status
                    $updateStmt = $pdo->prepare("
                        UPDATE users 
                        SET is_verified = TRUE 
                        WHERE id = ?
                    ");
                    
                    $updateStmt->execute([$row['user_id']]);
                    
                    // Delete the used token
                    $deleteStmt = $pdo->prepare("DELETE FROM email_verifications WHERE id = ?");
                    $deleteStmt->execute([$row['id']]);
                    
                    return (int)$row['user_id'];
                }
            }
            
            // No matching token found
            return false;
            
        } catch (PDOException $e) {
            error_log("TokenModel::validateAndGetUserId failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create a password reset token
     * 
     * @param PDO $pdo Database connection
     * @param string $email User's email address
     * @return string The unhashed token (to be sent in email)
     */
    public static function createPasswordResetToken(PDO $pdo, string $email): string
    {
        try {
            // Get user by email
            $userStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $userStmt->execute([$email]);
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return '';
            }
            
            // Generate cryptographically secure random token (32 bytes = 64 hex characters)
            $rawToken = bin2hex(random_bytes(32));
            
            // Hash the token before storing
            $hashedToken = password_hash($rawToken, PASSWORD_DEFAULT);
            
            // Set expiration to 1 hour from now
            $expiresAt = date('Y-m-d H:i:s', time() + (60 * 60));
            
            // Delete any existing password reset tokens for this user
            $deleteStmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $deleteStmt->execute([$user['id']]);
            
            // Insert new token
            $stmt = $pdo->prepare("
                INSERT INTO password_resets (user_id, token_hash, expires_at)
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([$user['id'], $hashedToken, $expiresAt]);
            
            // Return the unhashed token (this is what goes in the email)
            return $rawToken;
            
        } catch (PDOException $e) {
            error_log("TokenModel::createPasswordResetToken failed: " . $e->getMessage());
            return '';
        }
    }

    /**
     * Validate a password reset token
     * 
     * @param PDO $pdo Database connection
     * @param string $token The raw token from the URL
     * @return int|false User ID if token is valid, false otherwise
     */
    public static function validatePasswordResetToken(PDO $pdo, string $token): int|false
    {
        try {
            // Get all non-expired tokens
            $stmt = $pdo->prepare("
                SELECT id, user_id, token_hash 
                FROM password_resets 
                WHERE expires_at > NOW()
            ");
            
            $stmt->execute();
            $tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Check each token hash against the provided token
            foreach ($tokens as $row) {
                if (password_verify($token, $row['token_hash'])) {
                    // Token is valid! Return user ID
                    return (int)$row['user_id'];
                }
            }
            
            // No matching token found
            return false;
            
        } catch (PDOException $e) {
            error_log("TokenModel::validatePasswordResetToken failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a password reset token after use
     * 
     * @param PDO $pdo Database connection
     * @param int $userId User ID
     * @return bool True on success
     */
    public static function deletePasswordResetToken(PDO $pdo, int $userId): bool
    {
        try {
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
            return $stmt->execute([$userId]);
            
        } catch (PDOException $e) {
            error_log("TokenModel::deletePasswordResetToken failed: " . $e->getMessage());
            return false;
        }
    }
}
