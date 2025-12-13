<?php
// Include the edit profile handler to fetch current user data and handle form submission
require_once __DIR__ . '/../controllers/edit_profile_handler.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Webibo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link rel="stylesheet" href="../assets/css/edit_profile.css">
    <style>
        .fade-up {
            opacity: 0;
            transform: translateY(16px);
            animation: fadeUp 0.6s ease forwards;
            animation-delay: 0.05s;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <a href="profile.php" class="cancel-btn">✕</a>

    <div class="edit-profile-container fade-up">
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
                    <?php if (!empty($currentAvatar) && $currentAvatar !== '../assets/img/avatars/default.png' && file_exists(__DIR__ . '/' . $currentAvatar)): ?>
                        <img src="<?php echo htmlspecialchars($currentAvatar); ?>" alt="Avatar" id="avatarImage">
                    <?php else: ?>
                        <i class="fas fa-user" id="avatarIcon"></i>
                    <?php endif; ?>
                    <label for="avatarInput" class="file-input-label avatar-btn">
                        <i class="fas fa-camera"></i>
                    </label>
                </div>
                <div class="file-input-wrapper">
                    <input type="file" name="avatar" id="avatarInput" accept="image/jpeg,image/jpg,image/png,image/gif" onchange="previewAvatar(this)">
                </div>
                <p class="avatar-note">Max 5MB (JPEG, PNG, GIF)</p>
            </div>

            <h1 class="section-title">Personal Information</h1>
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

            <h1 class="section-title">Security</h1>
            <p class="password-note">
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



