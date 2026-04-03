<?php
require_once '../core/functions.php';
start_session_securely();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Webibo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/legal.css">
</head>
<body>
    <header class="page-header">
        <a href="../index.php" class="brand-link">
            <img src="../assets/img/webibo/webibo-logo.png" alt="Webibo logo" class="brand-logo">
            <span class="brand-name">Webibo</span>
        </a>
    </header>

    <div class="content-card">
            <div class="pill">Last updated: <?php echo date('F j, Y'); ?></div>
            <h1 class="page-title">Privacy Policy</h1>
            <p class="page-subtitle">How Webibo collects, uses, and protects your information.</p>

            <section id="scope" class="section">
                <h2>1. Scope</h2>
                <p>This Privacy Policy applies to the Webibo website, apps, and related services (collectively, the “Service”). By using the Service, you consent to the practices described here.</p>
                <div class="highlight-box">
                    We process your data to deliver the learning experience, keep your account secure, and improve Webibo.
                </div>
            </section>

            <section id="data-we-collect" class="section">
                <h2>2. Data We Collect</h2>
                <p>We collect information you provide directly and information collected automatically when you use the Service.</p>
                <ul>
                    <li><strong>Account data:</strong> name, email, username, password, and profile details.</li>
                    <li><strong>Usage data:</strong> progress, achievements, interactions, device type, browser, and IP for security.</li>
                    <li><strong>Support data:</strong> messages you send to our team.</li>
                </ul>
            </section>

            <section id="how-we-use" class="section">
                <h2>3. How We Use Data</h2>
                <p>We use your information to:</p>
                <ul>
                    <li>Provide and personalize learning content and achievements.</li>
                    <li>Secure your account, detect fraud or abuse, and troubleshoot issues.</li>
                    <li>Improve the platform through analytics and feedback.</li>
                    <li>Communicate important updates, security alerts, or product changes.</li>
                </ul>
            </section>

            <section id="sharing" class="section">
                <h2>4. Sharing and Partners</h2>
                <p>We do not sell your personal data. We may share limited information with:</p>
                <ul>
                    <li>Service providers that help us operate Webibo (e.g., hosting, email).</li>
                    <li>Legal authorities when required to comply with law or protect users.</li>
                </ul>
            </section>

            <section id="security" class="section">
                <h2>5. Security</h2>
                <p>We use reasonable administrative, technical, and physical safeguards to protect your information. No system is perfect—please use a strong password and keep it confidential.</p>
            </section>

            <section id="retention" class="section">
                <h2>6. Retention</h2>
                <p>We keep your data while your account is active and for a reasonable period afterward to comply with legal obligations, resolve disputes, and enforce agreements. Aggregated or de-identified data may be retained longer.</p>
            </section>

            <section id="your-rights" class="section">
                <h2>7. Your Choices</h2>
                <p>Depending on your region, you may have rights to access, correct, export, or delete your personal data. To exercise these rights, contact us using the details below. We may need to verify your identity before fulfilling a request.</p>
            </section>

            <section id="cookies" class="section">
                <h2>8. Cookies</h2>
                <p>We use cookies or similar technologies to remember preferences, keep you signed in, and understand usage. You can control cookies through your browser settings, but some features may not work properly without them.</p>
            </section>

            <section id="contact" class="section">
                <h2>9. Contact</h2>
                <p>If you have questions about privacy, reach us at:</p>
                <div class="contact-box">
                    <strong>Email:</strong> privacy@webibo.example<br>
                    <strong>Address:</strong> Webibo Privacy, 123 Learning Lane, Internet
                </div>
            </section>
        </div>
</body>
</html>

