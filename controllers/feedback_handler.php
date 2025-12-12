<?php
/**
 * Feedback Handler - Sends user feedback via email
 * 
 * Handles feedback form submissions from help page
 * Sends email using SMTP credentials from .env
 */

header('Content-Type: application/json');

// Load helper functions
require_once __DIR__ . '/../core/functions.php';

// Load environment variables
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$email = trim($input['email'] ?? '');
$subject = trim($input['subject'] ?? '');
$description = trim($input['description'] ?? '');

if (empty($email) || empty($subject) || empty($description)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Sanitize inputs
$email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
$subject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

try {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USER'] ?? '';
    $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);

    // Recipients
    $mail->setFrom($_ENV['SMTP_FROM'] ?? 'noreply@webibo.local', 'Webibo Feedback');
    $mail->addAddress($_ENV['SMTP_FROM'] ?? 'admin@webibo.local');
    $mail->addReplyTo($email, 'User Feedback');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Webibo Feedback: ' . $subject;
    
    // Build HTML email body
    $htmlBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
            .content { background-color: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
            .field { margin-bottom: 15px; }
            .field-label { font-weight: bold; color: #555; }
            .field-value { margin-top: 5px; padding: 10px; background-color: white; border-left: 3px solid #4CAF50; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #888; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>📧 New Feedback from Webibo</h1>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='field-label'>From:</div>
                    <div class='field-value'>{$email}</div>
                </div>
                <div class='field'>
                    <div class='field-label'>Subject:</div>
                    <div class='field-value'>{$subject}</div>
                </div>
                <div class='field'>
                    <div class='field-label'>Message:</div>
                    <div class='field-value'>" . nl2br($description) . "</div>
                </div>
            </div>
            <div class='footer'>
                <p>This email was sent from the Webibo feedback form</p>
                <p>Sent at: " . date('F j, Y, g:i a') . "</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $mail->Body = $htmlBody;
    
    // Plain text version
    $mail->AltBody = "New Feedback from Webibo\n\n" .
                     "From: {$email}\n" .
                     "Subject: {$subject}\n\n" .
                     "Message:\n{$description}\n\n" .
                     "Sent at: " . date('F j, Y, g:i a');

    // Send the email
    $mail->send();
    
    // Log success
    error_log("Feedback email sent successfully from: {$email}");
    
    echo json_encode([
        'success' => true, 
        'message' => 'Thank you for your feedback! We\'ll get back to you soon.'
    ]);

} catch (Exception $e) {
    // Log error
    error_log("Feedback email error: {$mail->ErrorInfo}");
    
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Sorry, we couldn\'t send your feedback. Please try again later.'
    ]);
}
