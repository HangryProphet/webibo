// Lecture JavaScript - Simple Tutorial Mode
// No typewriter, no animations - just clean navigation

(function() {
    'use strict';

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Enter or Space = Mark Complete
            if (e.key === 'Enter' || e.key === ' ') {
                if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    const completeBtn = document.getElementById('completeBtn');
                    if (completeBtn) completeBtn.click();
                }
            }
            
            // Escape = Go Back
            if (e.key === 'Escape') {
                window.history.back();
            }
        });
        
        console.log('Lecture page loaded in tutorial mode');
    });

})();

