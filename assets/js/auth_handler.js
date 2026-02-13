/**
 * Auth Handler - AJAX Form Submission
 * 
 * Intercepts login and signup form submissions to provide
 * feedback without page reloads.
 */
document.addEventListener('DOMContentLoaded', function () {
    const authForm = document.querySelector('.login-container form, .signup-container form');

    if (!authForm) return;

    authForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = authForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.textContent;

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> PLEASE WAIT...';

        // Clear previous feedback
        clearFeedback();

        const formData = new FormData(authForm);
        const action = authForm.getAttribute('action');

        try {
            const response = await fetch(action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const result = await response.json();

            if (result.success) {
                // If there's a success message, show it before redirecting
                if (result.message) {
                    showFeedback(result.message, 'success');
                    // Wait a bit if it's a success message, then redirect
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1500);
                } else {
                    // Direct redirect
                    window.location.href = result.redirect;
                }
            } else {
                // Show error message
                showFeedback(result.error || 'An unexpected error occurred.', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            }
        } catch (error) {
            console.error('Error:', error);
            showFeedback('An error occurred. Please try again.', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        }
    });

    function showFeedback(message, type) {
        let feedbackDiv = document.querySelector('.feedback-message');

        if (!feedbackDiv) {
            feedbackDiv = document.createElement('div');
            feedbackDiv.className = `feedback-message ${type}`;
            // Find a good place to insert - usually before the auth-switch
            const authSwitch = document.querySelector('.auth-switch');
            if (authSwitch) {
                authSwitch.parentNode.insertBefore(feedbackDiv, authSwitch);
            } else {
                authForm.appendChild(feedbackDiv);
            }
        } else {
            feedbackDiv.className = `feedback-message ${type}`;
            feedbackDiv.style.display = 'flex'; // Ensure it's visible if it was hidden
        }

        const icon = type === 'success' ? '✓' : '⚠';
        feedbackDiv.innerHTML = `<span class="feedback-icon">${icon}</span><span>${message}</span>`;

        // Add animation class if not present
        feedbackDiv.classList.add('fade-up');
    }

    function clearFeedback() {
        const feedbackDiv = document.querySelector('.feedback-message');
        if (feedbackDiv) {
            // We could remove it or just empty it. Let's remove it to be clean
            // but keep the existing ones if we want to preserve layout
            feedbackDiv.innerHTML = '';
            feedbackDiv.style.display = 'none';
        }
    }
});
