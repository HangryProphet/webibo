<?php
// Include the edit profile handler to fetch current user data and handle form submission
require_once __DIR__ . '/../controllers/edit_profile_handler.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - WebQuest</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <style>
        .edit-profile-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            margin: 60px 0;
        }

        .cancel-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: none;
            border: none;
            color: #6b7280;
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            text-decoration: none;
            display: inline-block;
        }

        .cancel-btn:hover {
            color: #9ca3af;
        }

        .save-btn {
            width: 100%;
            padding: 18px;
            background: none;
            border: 2px solid #1cb0f6;
            border-radius: 12px;
            color: #1cb0f6;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 24px;
            margin-bottom: 24px;
            transition: all 0.1s;
            box-shadow: 0 4px 0 #0d5a7a;
        }

        .save-btn:hover {
            background-color: rgba(28, 176, 246, 0.1);
        }

        .save-btn:active {
            box-shadow: 0 2px 0 #0d5a7a;
            transform: translateY(2px);
        }

        .avatar-upload {
            margin: 24px 0;
            text-align: center;
        }

        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 16px;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-preview i {
            font-size: 48px;
            color: #6b7280;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-input-wrapper input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-input-label {
            display: inline-block;
            padding: 12px 24px;
            background: #1cb0f6;
            color: white;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s;
        }

        .file-input-label:hover {
            background: #1899d6;
        }

        .error-message, .success-message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        .error-message {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .success-message {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }

        .email-readonly {
            background: #f3f4f6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <a href="profile.php" class="cancel-btn">✕</a>

    <div class="edit-profile-container">
        <h1>Edit Profile</h1>

        <?php
        // Display error messages
        $error = get_error();
        if ($error) {
            echo '<div class="error-message">' . htmlspecialchars($error) . '</div>';
        }

        // Display success messages
        $success = get_success();
        if ($success) {
            echo '<div class="success-message">' . htmlspecialchars($success) . '</div>';
        }
        ?>

        <form method="POST" action="edit_profile.php" enctype="multipart/form-data">
            
            <!-- Avatar Upload Section -->
            <div class="avatar-upload">
                <div class="avatar-preview" id="avatarPreview">
                    <?php if (!empty($currentAvatar) && $currentAvatar !== '/assets/img/avatars/default.png' && file_exists($_SERVER['DOCUMENT_ROOT'] . $currentAvatar)): ?>
                        <img src="<?php echo htmlspecialchars($currentAvatar); ?>" alt="Avatar" id="avatarImage">
                    <?php else: ?>
                        <i class="fas fa-user" id="avatarIcon"></i>
                    <?php endif; ?>
                </div>
                <div class="file-input-wrapper">
                    <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/jpg,image/png,image/gif" onchange="previewAvatar(this)">
                    <label for="avatarInput" class="file-input-label">
                        <i class="fas fa-camera"></i> Change Avatar
                    </label>
                </div>
                <p style="font-size: 12px; color: #6b7280; margin-top: 8px;">Max 5MB (JPEG, PNG, GIF)</p>
            </div>

            <div class="name-row">
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input 
                        type="text" 
                        name="first_name" 
                        placeholder="First name"
                        value="<?php echo htmlspecialchars($firstName); ?>"
                    >
                </div>
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input 
                        type="text" 
                        name="last_name" 
                        placeholder="Last name"
                        value="<?php echo htmlspecialchars($lastName); ?>"
                    >
                </div>
            </div>

            <div class="input-group">
                <i class="fas fa-envelope input-icon"></i>
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email"
                    value="<?php echo htmlspecialchars($email); ?>"
                    class="email-readonly"
                    readonly
                    title="Email cannot be changed"
                >
            </div>

            <div class="input-group">
                <i class="fas fa-user-circle input-icon"></i>
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username"
                    value="<?php echo htmlspecialchars($username); ?>"
                    required
                >
            </div>

            <hr style="margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;">
            <p style="text-align: center; color: #6b7280; margin-bottom: 20px; font-size: 14px;">
                <strong>Change Password</strong> (optional)
            </p>

            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input 
                    type="password" 
                    id="currentPassword" 
                    name="current_password" 
                    placeholder="Current password (required for password change)"
                >
                <button type="button" class="password-toggle" onclick="togglePassword('currentPassword', this)">SHOW</button>
            </div>

            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input 
                    type="password" 
                    id="newPassword" 
                    name="new_password" 
                    placeholder="New password (optional)"
                >
                <button type="button" class="password-toggle" onclick="togglePassword('newPassword', this)">SHOW</button>
            </div>

            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input 
                    type="password" 
                    id="confirmNewPassword" 
                    name="confirm_new_password" 
                    placeholder="Confirm new password"
                >
                <button type="button" class="password-toggle" onclick="togglePassword('confirmNewPassword', this)">SHOW</button>
            </div>

            <button type="submit" class="save-btn">SAVE CHANGES</button>
        </form>
    </div>

    <script src="../assets/js/password_toggle.js"></script>
    <script>
        function previewAvatar(input) {
            const preview = document.getElementById('avatarPreview');
            const file = input.files[0];
            
            if (file) {
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must not exceed 5MB');
                    input.value = '';
                    return;
                }
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPEG, PNG, or GIF)');
                    input.value = '';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Remove existing content
                    preview.innerHTML = '<img src="' + e.target.result + '" alt="Avatar Preview" style="width: 100%; height: 100%; object-fit: cover;">';
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>



