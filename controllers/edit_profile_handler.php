<?php
/**
 * Edit Profile Handler - Controller for Profile Editing
 * 
 * Handles profile updates: first name, last name, username, avatar, and password
 * Uses dev mode for OTP (auto 123456) on localhost
 */

// Include dependencies
require_once __DIR__ . '/../core/db_connect.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../core/models/UserModel.php';
require_once __DIR__ . '/../core/models/TokenModel.php';
require_once __DIR__ . '/../core/services/AchievementService.php';

// Require user to be logged in
require_login();

// Get current user ID
$userId = get_current_user_id();

// Fetch current user data for pre-filling form
try {
    $user = UserModel::getUserById($pdo, $userId);
    
    if (!$user) {
        set_error("Unable to load profile data.");
        redirect('/views/profile.php');
        exit;
    }
    
    // Pre-fill form variables
    $firstName = sanitize_output($user['first_name'] ?? '');
    $lastName = sanitize_output($user['last_name'] ?? '');
    $username = sanitize_output($user['username']);
    $email = sanitize_output($user['email']);
    $currentAvatar = $user['avatar_path'] ?? '/assets/img/avatars/default.png';
    
} catch (PDOException $e) {
    error_log("Edit Profile Handler Error: " . $e->getMessage());
    set_error("An error occurred while loading your profile.");
    redirect('/views/profile.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $hasChanges = false;
    $errors = [];
    
    // Check if user is attempting to change password
    $isChangingPassword = !empty($_POST['new_password'] ?? '');
    
    // Validate current password only if changing password
    if ($isChangingPassword) {
        $currentPassword = $_POST['current_password'] ?? '';
        
        if (empty($currentPassword)) {
            set_error("Current password is required to change password.");
            redirect('/views/edit_profile.php');
            exit;
        }
        
        // Verify current password
        if (!UserModel::verifyPassword($pdo, $userId, $currentPassword)) {
            set_error("Current password is incorrect.");
            redirect('/views/edit_profile.php');
            exit;
        }
    }
    
    // ============ HANDLE AVATAR UPLOAD ============
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        
        if ($_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatarFile = $_FILES['avatar'];
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($avatarFile['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "Avatar must be a valid image (JPEG, PNG, or GIF).";
            }
            
            // Validate file size (max 5MB)
            $maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if ($avatarFile['size'] > $maxSize) {
                $errors[] = "Avatar file size must not exceed 5MB.";
            }
            
            if (empty($errors)) {
                // Generate unique filename
                $extension = pathinfo($avatarFile['name'], PATHINFO_EXTENSION);
                $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $extension;
                $uploadDir = __DIR__ . '/../assets/img/avatars/';
                $uploadPath = $uploadDir . $newFileName;
                
                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Move uploaded file
                if (move_uploaded_file($avatarFile['tmp_name'], $uploadPath)) {
                    $avatarDbPath = '/assets/img/avatars/' . $newFileName;
                    
                    // Delete old avatar if not default
                    if ($currentAvatar !== '/assets/img/avatars/default.png') {
                        $oldAvatarPath = __DIR__ . '/..' . $currentAvatar;
                        if (file_exists($oldAvatarPath)) {
                            unlink($oldAvatarPath);
                        }
                    }
                    
                    // Update avatar in database
                    if (UserModel::updateUserAvatar($pdo, $userId, $avatarDbPath)) {
                        $hasChanges = true;
                        
                        // Trigger achievement: Picture Perfect (upload avatar)
                        AchievementService::checkAchievementsOnEvent($pdo, $userId, 'profile_updated');
                    } else {
                        $errors[] = "Failed to update avatar in database.";
                    }
                } else {
                    $errors[] = "Failed to upload avatar file.";
                }
            }
        } elseif ($_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = "Avatar upload error: " . $_FILES['avatar']['error'];
        }
    }
    
    // ============ HANDLE PROFILE UPDATES ============
    $newFirstName = trim($_POST['first_name'] ?? '');
    $newLastName = trim($_POST['last_name'] ?? '');
    $newUsername = trim($_POST['username'] ?? '');
    
    $profileUpdates = [];
    
    // Check if first name changed
    if ($newFirstName !== $user['first_name']) {
        if (strlen($newFirstName) < 2) {
            $errors[] = "First name must be at least 2 characters.";
        } else {
            $profileUpdates['first_name'] = $newFirstName;
        }
    }
    
    // Check if last name changed
    if ($newLastName !== $user['last_name']) {
        if (strlen($newLastName) < 2) {
            $errors[] = "Last name must be at least 2 characters.";
        } else {
            $profileUpdates['last_name'] = $newLastName;
        }
    }
    
    // Check if username changed
    if ($newUsername !== $user['username']) {
        if (strlen($newUsername) < 3) {
            $errors[] = "Username must be at least 3 characters.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $newUsername)) {
            $errors[] = "Username can only contain letters, numbers, and underscores.";
        } elseif (UserModel::isUsernameTakenByOther($pdo, $newUsername, $userId)) {
            $errors[] = "Username is already taken.";
        } else {
            $profileUpdates['username'] = $newUsername;
        }
    }
    
    // Update profile if there are changes
    if (!empty($profileUpdates) && empty($errors)) {
        if (UserModel::updateUserProfile($pdo, $userId, $profileUpdates)) {
            $hasChanges = true;
            
            // Update session username if changed
            if (isset($profileUpdates['username'])) {
                $_SESSION['user'] = $profileUpdates['username'];
            }
        } else {
            $errors[] = "Failed to update profile information.";
        }
    }
    
    // ============ HANDLE PASSWORD CHANGE ============
    $newPassword = $_POST['new_password'] ?? '';
    $confirmNewPassword = $_POST['confirm_new_password'] ?? '';
    
    if (!empty($newPassword)) {
        // Validate new password
        if (strlen($newPassword) < 8) {
            $errors[] = "New password must be at least 8 characters.";
        } elseif ($newPassword !== $confirmNewPassword) {
            $errors[] = "New passwords do not match.";
        } else {
            // Simple password update (dev mode - no OTP)
            // In production, you would implement OTP verification here
            if (UserModel::updatePassword($pdo, $userId, $newPassword)) {
                $hasChanges = true;
                set_success("Password changed successfully!");
            } else {
                $errors[] = "Failed to update password. Please try again.";
            }
        }
    }
    
    // ============ DISPLAY RESULTS ============
    if (!empty($errors)) {
        set_error(implode(' ', $errors));
        redirect('/views/edit_profile.php');
        exit;
    }
    
    if ($hasChanges) {
        set_success("Profile updated successfully!");
        redirect('/views/profile.php');
        exit;
    }
    
    set_error("No changes were made.");
    redirect('/views/edit_profile.php');
    exit;
}
