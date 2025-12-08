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
        $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/Webibo';
        $verificationUrl = $baseUrl . '/controllers/verify_email.php?token=' . urlencode($token);
        
        // Email body (matches Duolingo-style design)
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                    background-color: #f3f4f6;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background: linear-gradient(135deg, #1cb0f6 0%, #0d7ca8 100%);
                    padding: 40px 20px;
                    text-align: center;
                    color: #ffffff;
                }
                .header h1 {
                    margin: 0;
                    font-size: 32px;
                    font-weight: 700;
                }
                .content {
                    padding: 40px 30px;
                }
                .greeting {
                    font-size: 18px;
                    font-weight: 600;
                    color: #1f2937;
                    margin-bottom: 20px;
                }
                .message {
                    font-size: 15px;
                    color: #4b5563;
                    line-height: 1.6;
                    margin-bottom: 30px;
                }
                .button-container {
                    text-align: center;
                    margin: 30px 0;
                }
                .verify-button {
                    display: inline-block;
                    background-color: #1cb0f6;
                    color: #ffffff;
                    text-decoration: none;
                    padding: 16px 48px;
                    border-radius: 12px;
                    font-size: 16px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    box-shadow: 0 4px 0 #0d7ca8;
                }
                .verify-button:hover {
                    background-color: #17a1e0;
                }
                .alt-link {
                    font-size: 13px;
                    color: #6b7280;
                    margin-top: 20px;
                    padding: 20px;
                    background-color: #f9fafb;
                    border-radius: 8px;
                    word-break: break-all;
                }
                .footer {
                    padding: 20px;
                    text-align: center;
                    font-size: 13px;
                    color: #9ca3af;
                    background-color: #f9fafb;
                }
                .expiry-notice {
                    background-color: #fef3c7;
                    border-left: 4px solid #f59e0b;
                    padding: 15px;
                    margin: 20px 0;
                    font-size: 14px;
                    color: #92400e;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎓 Webibo</h1>
                </div>
                <div class="content">
                    <div class="greeting">Hi ' . htmlspecialchars($recipientName) . '! 👋</div>
                    <div class="message">
                        Welcome to <strong>Webibo</strong>! We\'re excited to have you join our learning community.
                        <br><br>
                        To get started, please verify your email address by clicking the button below:
                    </div>
                    <div class="button-container">
                        <a href="' . htmlspecialchars($verificationUrl) . '" class="verify-button">
                            Verify My Email
                        </a>
                    </div>
                    <div class="expiry-notice">
                        ⏱️ <strong>Important:</strong> This verification link will expire in 24 hours.
                    </div>
                    <div class="alt-link">
                        <strong>Link not working?</strong> Copy and paste this URL into your browser:<br>
                        ' . htmlspecialchars($verificationUrl) . '
                    </div>
                    <div class="message">
                        If you didn\'t create an account with Webibo, you can safely ignore this email.
                    </div>
                </div>
                <div class="footer">
                    © ' . date('Y') . ' Webibo. All rights reserved.<br>
                    Happy learning! 🚀
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
 * Send password reset email (for future implementation)
 * 
 * @param string $recipientEmail User's email address
 * @param string $recipientName User's first name
 * @param string $resetToken Password reset token
 * @return bool True on success, false on failure
 */
function sendPasswordResetEmail(string $recipientEmail, string $recipientName, string $resetToken): bool
{
    // Future implementation for password reset functionality
    return false;
}
