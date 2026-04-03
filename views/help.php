<?php
require_once '../core/functions.php';
start_session_securely();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - Webibo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/help.css">
</head>
<body>
    <header class="page-header">
        <button class="back-btn" type="button" aria-label="Go back">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>
        </button>
        <a href="../index.php" class="brand-link">
            <img src="../assets/img/webibo/webibo-logo.png" alt="Webibo logo" class="brand-logo">
            <span class="brand-name">Webibo</span>
        </a>
    </header>

    <div class="content-card">
        <div class="pill">Help Center</div>
        <h1 class="page-title">Frequently Asked Questions</h1>
        <p class="page-subtitle">Quick answers about learning on Webibo and managing your account.</p>

        <section class="section">
            <h2>Using Webibo</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span><strong>What are courses?</strong></span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="faq-answer">
                        Webibo organizes learning into adventures: <strong>HTML</strong> for page structure, <strong>CSS</strong> for styling, and <strong>JavaScript</strong> for interactivity. Each adventure contains quests, lessons, and checkpoints that guide you from basics to hands-on practice.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span><strong>What is a streak?</strong></span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="faq-answer">
                        Your streak counts how many consecutive days you stay active. Complete at least one activity (like a lesson or challenge) each day and your streak will climb by 1. Keep showing up daily to maintain it.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span><strong>What are XPs?</strong></span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="faq-answer">
                        XP (experience points) reward your progress. You earn XP for finishing lessons, answering correctly, and completing quests. Accumulating XP unlocks new levels and shows how far you have advanced.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span><strong>What are achievements?</strong></span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="faq-answer">
                        Achievements are badges for hitting milestones, such as finishing an adventure, keeping a long streak, or acing challenges. They track memorable moments in your learning journey.
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <h2>Account Management</h2>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span><strong>How do I change my username?</strong></span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="faq-answer">
                        Go to <strong>Profile</strong> &gt; <strong>Edit Profile</strong>, update the username field, and save your changes. Your new username will appear across the app right away.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span><strong>How do I update my password?</strong></span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="faq-answer">
                        In <strong>Profile</strong> &gt; <strong>Edit Profile</strong>, enter your current password, then set and confirm a new one. Save to apply the update.
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question" aria-expanded="false">
                        <span><strong>I’m having trouble accessing my account</strong></span>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="faq-answer">
                        Use <strong>Forgot Password</strong> on the login page to reset access. If you still cannot sign in, double-check spam for the reset email or contact support with the email tied to your account.
                    </div>
                </div>
            </div>
        </section>

        <section class="feedback-section">
            <div class="feedback-cta">
                <div class="feedback-cta__text">Still unsure about something?</div>
                <button class="login-btn" id="open-feedback-modal">SEND FEEDBACK</button>
            </div>
        </section>
    </div>

    <div class="modal-overlay" id="feedback-modal" aria-hidden="true">
        <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="feedback-modal-title">
            <button class="modal-close" type="button" aria-label="Close feedback form">&times;</button>
            <h3 id="feedback-modal-title">Explain Your Issue</h3>
            <p class="modal-subtitle">
                Please provide a detailed explanation of the problem you're encountering so we can better understand the situation.
            </p>
            <form class="modal-form">
                <label class="modal-label" for="feedback-email">Email <span class="required-asterisk" aria-hidden="true">*</span></label>
                <input class="modal-input" type="email" id="feedback-email" name="email" placeholder="you@example.com" autocomplete="email">

                <label class="modal-label" for="feedback-subject">Subject <span class="required-asterisk" aria-hidden="true">*</span></label>
                <input class="modal-input" type="text" id="feedback-subject" name="subject" placeholder="Subject">

                <label class="modal-label" for="feedback-description">Description <span class="required-asterisk" aria-hidden="true">*</span></label>
                <textarea class="modal-textarea" id="feedback-description" name="description" placeholder="Describe the issue..." rows="4"></textarea>

                <button type="button" class="login-btn modal-submit">Submit</button>
            </form>
        </div>
    </div>

    <script>
        const faqToggles = document.querySelectorAll('.faq-question');

        const closeItem = (item) => {
            const answer = item.querySelector('.faq-answer');
            item.classList.remove('open');
            item.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            answer.style.maxHeight = 0;
        };

        const openItem = (item) => {
            const answer = item.querySelector('.faq-answer');
            item.classList.add('open');
            item.querySelector('.faq-question').setAttribute('aria-expanded', 'true');
            // Add extra space to avoid cropping when text wraps or fonts resize
            answer.style.maxHeight = answer.scrollHeight + 40 + 'px';
        };

        faqToggles.forEach((btn) => {
            btn.addEventListener('click', () => {
                const item = btn.closest('.faq-item');
                const isOpen = item.classList.contains('open');

                // Close any other open items in the same list
                item.parentElement.querySelectorAll('.faq-item.open').forEach((openItemEl) => {
                    if (openItemEl !== item) {
                        closeItem(openItemEl);
                    }
                });

                if (isOpen) {
                    closeItem(item);
                } else {
                    openItem(item);
                }
            });
        });

        window.addEventListener('resize', () => {
            document.querySelectorAll('.faq-item.open .faq-answer').forEach((answer) => {
                answer.style.maxHeight = answer.scrollHeight + 'px';
            });
        });

        const modal = document.getElementById('feedback-modal');
        const openModalBtn = document.getElementById('open-feedback-modal');
        const closeModalBtn = modal.querySelector('.modal-close');

        const openModal = () => {
            modal.classList.add('is-visible');
            modal.setAttribute('aria-hidden', 'false');
        };

        const closeModal = () => {
            modal.classList.remove('is-visible');
            modal.setAttribute('aria-hidden', 'true');
        };

        openModalBtn.addEventListener('click', openModal);
        closeModalBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && modal.classList.contains('is-visible')) {
                closeModal();
            }
        });

        document.querySelector('.back-btn')?.addEventListener('click', () => {
            window.history.back();
        });
    </script>
</body>
</html>

