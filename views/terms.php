<?php
require_once '../core/functions.php';
start_session_securely();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Webibo</title>
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
            <h1 class="page-title">Terms of Service</h1>
            <p class="page-subtitle">These Terms explain how you may use Webibo and our related services.</p>

            <section id="overview" class="section">
                <h2>1. Overview</h2>
                <p>Webibo provides interactive learning content, achievements, and community features to help you improve your skills. By creating an account or using the site, you agree to these Terms and any policies referenced here.</p>
                <div class="highlight-box">
                    We may update these Terms as our product evolves. If changes are material, we will notify you through the product or by email before they take effect.
                </div>
            </section>

            <section id="accounts" class="section">
                <h2>2. Accounts and Eligibility</h2>
                <p>You must be at least 13 years old (or the age of digital consent in your region) to use Webibo. You are responsible for:</p>
                <ul>
                    <li>Keeping your login credentials secure.</li>
                    <li>Providing accurate registration details.</li>
                    <li>All activity under your account.</li>
                </ul>
                <p>If you believe your account has been compromised, please reset your password and contact us immediately.</p>
            </section>

            <section id="acceptable-use" class="section">
                <h2>3. Acceptable Use</h2>
                <p>Please use Webibo responsibly. You agree not to:</p>
                <ul>
                    <li>Attempt to disrupt, attack, or reverse engineer the service.</li>
                    <li>Upload malware, infringing material, or content that is harassing, hateful, or illegal.</li>
                    <li>Misrepresent your identity or impersonate others.</li>
                    <li>Automate usage in a way that degrades the service for others.</li>
                </ul>
                <p>We may suspend or remove accounts that violate these rules.</p>
            </section>

            <section id="payments" class="section">
                <h2>4. Payments and Subscriptions</h2>
                <p>If Webibo offers paid plans, you will see pricing, billing frequency, and renewal details at checkout. By subscribing, you authorize recurring charges until you cancel. Refund eligibility will follow any posted refund policy or applicable consumer laws.</p>
            </section>

            <section id="content" class="section">
                <h2>5. Content Ownership</h2>
                <p>Webibo owns the platform, branding, and learning content unless otherwise stated. You retain rights to any original content you submit, but grant us a limited license to display and operate that content within the service.</p>
                <div class="chips">
                    <span class="chip">Platform IP stays with Webibo</span>
                    <span class="chip">Your uploads remain yours</span>
                    <span class="chip">License = operate & display</span>
                </div>
            </section>

            <section id="disclaimers" class="section">
                <h2>6. Disclaimers</h2>
                <p>The service is provided “as is” without warranties of any kind. We do not guarantee uninterrupted availability, specific learning outcomes, or error-free content. You use the service at your own risk.</p>
            </section>

            <section id="liability" class="section">
                <h2>7. Limitation of Liability</h2>
                <p>To the fullest extent permitted by law, Webibo and its team are not liable for indirect, incidental, or consequential damages, or lost profits, arising from your use of the service.</p>
            </section>

            <section id="termination" class="section">
                <h2>8. Suspension and Termination</h2>
                <p>We may suspend or terminate access if you violate these Terms or if required by law. You may stop using the service at any time; some data (like audit logs or required records) may be retained as permitted by law.</p>
            </section>

            <section id="contact" class="section">
                <h2>9. Contact</h2>
                <p>If you have questions about these Terms, reach us at:</p>
                <div class="contact-box">
                    <strong>Email:</strong> support@webibo.example<br>
                    <strong>Address:</strong> Webibo Team, 123 Learning Lane, Internet
                </div>
            </section>
        </div>
</body>
</html>

