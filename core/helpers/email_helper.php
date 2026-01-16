<?php
/**
 * Email Helper - PHPMailer Integration
 * 
 * Handles sending verification emails using Gmail SMTP
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send verification email with token link
 * 
 * @param string $recipientEmail User's email address
 * @param string $recipientName User's first name
 * @param string $token Verification token (unhashed)
 * @return bool True on success, false on failure
 */
function sendVerificationEmail(string $recipientEmail, string $recipientName, string $token): bool
{
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'] ?? '';
        $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);
        
        // Recipients
        $mail->setFrom($_ENV['SMTP_FROM'] ?? $_ENV['SMTP_USER'], 'Webibo Team');
        $mail->addAddress($recipientEmail, $recipientName);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email for Webibo';
        
        // Construct verification URL
        $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/webquest';
        $verificationUrl = $baseUrl . '/controllers/verify_email.php?token=' . urlencode($token);
        
        // Email body (matches modernized design)
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                    background-color: #f4f6f8;
                    margin: 0;
                    padding: 0;
                    color: #1f2937;
                    -webkit-font-smoothing: antialiased;
                }
                .wrapper {
                    width: 100%;
                    background-color: #f4f6f8;
                    padding: 40px 0;
                }
                .container {
                    max-width: 500px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
                }
                .header {
                    padding: 40px 40px 0 40px;
                    text-align: center;
                }
                .logo {
                    font-size: 28px;
                    font-weight: 800;
                    color: #1cb0f6;
                    margin: 0;
                    letter-spacing: -0.5px;
                    text-decoration: none;
                }
                .content {
                    padding: 40px;
                    text-align: center;
                }
                .title {
                    font-size: 24px;
                    font-weight: 700;
                    color: #111827;
                    margin: 0 0 16px 0;
                }
                .text {
                    font-size: 16px;
                    line-height: 1.6;
                    color: #4b5563;
                    margin: 0 0 32px 0;
                }
                .button {
                    display: inline-block;
                    background-color: #1cb0f6;
                    color: #ffffff;
                    text-decoration: none;
                    padding: 16px 32px;
                    border-radius: 12px;
                    font-size: 16px;
                    font-weight: 600;
                    transition: background-color 0.2s;
                    box-shadow: 0 4px 6px -1px rgba(28, 176, 246, 0.2);
                }
                .button:hover {
                    background-color: #0d9cdf;
                }
                .expiry-notice {
                    margin-top: 32px;
                    padding: 16px;
                    background-color: #fffbeb;
                    border-radius: 8px;
                    font-size: 13px;
                    color: #92400e;
                    text-align: left;
                }
                .expiry-notice strong {
                    display: block;
                    margin-bottom: 4px;
                    color: #b45309;
                }
                .footer {
                    background-color: #f9fafb;
                    padding: 24px 40px;
                    text-align: center;
                    font-size: 13px;
                    color: #9ca3af;
                    border-top: 1px solid #f3f4f6;
                }
                .footer-link {
                    color: #1cb0f6;
                    text-decoration: none;
                    word-break: break-all;
                }
                @media only screen and (max-width: 600px) {
                    .wrapper { padding: 20px 0; }
                    .container { width: 92%; border-radius: 12px; }
                    .content { padding: 32px 20px; }
                    .header { padding-top: 32px; }
                }
            </style>
        </head>
        <body>
            <div class="wrapper">
                <div class="container">
                    <div class="header">
                        <div class="logo">Webibo</div>
                    </div>
                    
                    <div class="content">
                        <h1 class="title">Verify Your Email</h1>
                        <p class="text">
                            Hi ' . htmlspecialchars($recipientName) . ',<br>
                            Welcome to Webibo! We\'re excited to have you join our learning community. Please verify your email address to get started:
                        </p>
                        
                        <a href="' . htmlspecialchars($verificationUrl) . '" class="button">Verify Email</a>

                        <div class="expiry-notice">
                            <strong>⏱️ Important</strong>
                            This link expires in 24 hours. If you didn\'t create an account with Webibo, you can safely ignore this email.
                        </div>
                    </div>
                    
                    <div class="footer">
                        <p style="margin-bottom: 12px;">Button not working? Paste this link into your browser:</p>
                        <a href="' . htmlspecialchars($verificationUrl) . '" class="footer-link">' . htmlspecialchars($verificationUrl) . '</a>
                        <p style="margin-top: 24px;">© ' . date('Y') . ' Webibo. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ';
        
        // Plain text alternative
        $mail->AltBody = "Hi $recipientName!\n\n"
                       . "Welcome to Webibo! Please verify your email address by clicking this link:\n\n"
                       . "$verificationUrl\n\n"
                       . "This link will expire in 24 hours.\n\n"
                       . "If you didn't create an account, you can safely ignore this email.\n\n"
                       . "Happy learning!\n"
                       . "The Webibo Team";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

/**
 * Send password reset email
 * 
 * @param string $recipientEmail User's email address
 * @param string $resetLink Password reset link with token
 * @param string $recipientName User's first name
 * @return bool True on success, false on failure
 */
function sendPasswordResetEmail(string $recipientEmail, string $resetLink, string $recipientName = 'User'): bool
{
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'] ?? '';
        $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);
        
        // Recipients
        $mail->setFrom($_ENV['SMTP_FROM'] ?? $_ENV['SMTP_USER'], 'Webibo Team');
        $mail->addAddress($recipientEmail, $recipientName);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Webibo Password';
        
        // Email body (matches Duolingo-style design)
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                    background-color: #f4f6f8;
                    margin: 0;
                    padding: 0;
                    color: #1f2937;
                    -webkit-font-smoothing: antialiased;
                }
                .wrapper {
                    width: 100%;
                    background-color: #f4f6f8;
                    padding: 40px 0;
                }
                .container {
                    max-width: 500px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.05);
                }
                .header {
                    padding: 40px 40px 0 40px;
                    text-align: center;
                }
                .logo {
                    font-size: 28px;
                    font-weight: 800;
                    color: #1cb0f6;
                    margin: 0;
                    letter-spacing: -0.5px;
                    text-decoration: none;
                }
                .content {
                    padding: 40px;
                    text-align: center;
                }
                .title {
                    font-size: 24px;
                    font-weight: 700;
                    color: #111827;
                    margin: 0 0 16px 0;
                }
                .text {
                    font-size: 16px;
                    line-height: 1.6;
                    color: #4b5563;
                    margin: 0 0 32px 0;
                }
                .button {
                    display: inline-block;
                    background-color: #1cb0f6;
                    color: #ffffff;
                    text-decoration: none;
                    padding: 16px 32px;
                    border-radius: 12px;
                    font-size: 16px;
                    font-weight: 600;
                    transition: background-color 0.2s;
                    box-shadow: 0 4px 6px -1px rgba(28, 176, 246, 0.2);
                }
                .button:hover {
                    background-color: #0d9cdf;
                }
                .security-notice {
                    margin-top: 32px;
                    padding: 16px;
                    background-color: #fffbeb;
                    border-radius: 8px;
                    font-size: 13px;
                    color: #92400e;
                    text-align: left;
                }
                .security-notice strong {
                    display: block;
                    margin-bottom: 4px;
                    color: #b45309;
                }
                .footer {
                    background-color: #f9fafb;
                    padding: 24px 40px;
                    text-align: center;
                    font-size: 13px;
                    color: #9ca3af;
                    border-top: 1px solid #f3f4f6;
                }
                .footer-link {
                    color: #1cb0f6;
                    text-decoration: none;
                    word-break: break-all;
                }
                @media only screen and (max-width: 600px) {
                    .wrapper { padding: 20px 0; }
                    .container { width: 92%; border-radius: 12px; }
                    .content { padding: 32px 20px; }
                    .header { padding-top: 32px; }
                }
            </style>
        </head>
        <body>
            <div class="wrapper">
                <div class="container">
                    <div class="header">
                        <div class="logo">Webibo</div>
                    </div>
                    
                    <div class="content">
                        <h1 class="title">Reset Your Password</h1>
                        <p class="text">
                            Hi ' . htmlspecialchars($recipientName) . ',<br>
                            We received a request to reset the password for your Webibo account. If this was you, you can set a new password here:
                        </p>
                        
                        <a href="' . htmlspecialchars($resetLink) . '" class="button">Reset Password</a>

                        <div class="security-notice">
                            <strong>⏱️ Security Notice</strong>
                            This link expires in 1 hour. If you didn\'t ask to reset your password, you can safely ignore this email.
                        </div>
                    </div>
                    
                    <div class="footer">
                        <p style="margin-bottom: 12px;">Button not working? Paste this link into your browser:</p>
                        <a href="' . htmlspecialchars($resetLink) . '" class="footer-link">' . htmlspecialchars($resetLink) . '</a>
                        <p style="margin-top: 24px;">© ' . date('Y') . ' Webibo. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </body>
        </html>
        ';
        
        // Plain text alternative
        $mail->AltBody = "Hi {$recipientName},\n\n"
                       . "We received a request to reset your password for your Webibo account.\n\n"
                       . "Click this link to reset your password:\n"
                       . $resetLink . "\n\n"
                       . "This link will expire in 1 hour.\n\n"
                       . "If you didn't request a password reset, you can safely ignore this email.\n\n"
                       . "The Webibo Team";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Password reset email failed: {$mail->ErrorInfo}");
        return false;
    }
}
