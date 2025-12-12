// Lecture JavaScript - PowerPoint-like Slide Navigation
// Handles slide navigation with Next/Previous buttons

(function() {
    'use strict';

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Setup exit modal handlers
        setupExitModal();
        
        console.log('Lecture page loaded with slide navigation');
    });
    
    // Exit modal functions
    window.showExitModal = function() {
        const exitModal = document.getElementById('exitModal');
        if (exitModal) {
            exitModal.classList.add('active');
        }
    };
    
    window.closeModal = function() {
        const exitModal = document.getElementById('exitModal');
        if (exitModal) {
            exitModal.classList.remove('active');
        }
    };
    
    window.exitToDashboard = function() {
        // Exit without completion (from exit modal during lecture)
        // Victory modal buttons handle their own completion + redirect
        window.location.href = 'dashboard.php';
    };
    
    function setupExitModal() {
        const exitModal = document.getElementById('exitModal');
        if (exitModal) {
            // Close modal when clicking outside
            exitModal.addEventListener('click', function(e) {
                if (e.target === exitModal) {
                    window.closeModal();
                }
            });
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeModal();
            }
        });
    }

})();

