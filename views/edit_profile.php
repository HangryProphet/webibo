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
    </style>
</head>
<body>
    <a href="profile.php" class="cancel-btn">✕</a>

    <div class="edit-profile-container">
        <h1>Edit Profile</h1>

        <form method="POST" action="#">
            <div class="name-row">
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input 
                        type="text" 
                        name="first_name" 
                        placeholder="First name"
                        value=""
                    >
                </div>
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input 
                        type="text" 
                        name="last_name" 
                        placeholder="Last name"
                        value=""
                    >
                </div>
            </div>

            <div class="input-group">
                <i class="fas fa-envelope input-icon"></i>
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email"
                    value=""
                >
            </div>

            <div class="input-group">
                <i class="fas fa-user-circle input-icon"></i>
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username"
                    value=""
                >
            </div>

            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input 
                    type="password" 
                    id="currentPassword" 
                    name="current_password" 
                    placeholder="Current password"
                >
                <button type="button" class="password-toggle" onclick="togglePassword('currentPassword', this)">SHOW</button>
            </div>

            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input 
                    type="password" 
                    id="newPassword" 
                    name="new_password" 
                    placeholder="New password (leave blank to keep current)"
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
</body>
</html>



