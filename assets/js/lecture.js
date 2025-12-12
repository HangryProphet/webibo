// Lecture JavaScript - PowerPoint-like Slide Navigation
// Handles slide navigation with Next/Previous buttons

(function() {
    'use strict';

    const victorySound = (typeof Audio !== 'undefined') ? new Audio('../assets/sfx/victory.mp3') : null;

    function getVolume() {
        const savedVolume = localStorage.getItem('webibo_sound_volume');
        return savedVolume !== null ? parseInt(savedVolume, 10) / 100 : 0.7;
    }

    function setVictoryVolume(volume) {
        if (victorySound) {
            victorySound.volume = volume;
        }
    }

    function playVictorySound() {
        if (!victorySound) return;
        
        // Don't play if volume is 0
        const currentVolume = getVolume();
        if (currentVolume === 0) return;
        
        try {
            victorySound.currentTime = 0;
            const playPromise = victorySound.play();
            if (playPromise && typeof playPromise.catch === 'function') {
                playPromise.catch(err => console.warn('Audio playback blocked:', err));
            }
        } catch (e) {
            console.error('Error playing victory sound:', e);
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Setup exit modal handlers
        setupExitModal();

        // Set initial volume and listen for settings updates
        setVictoryVolume(getVolume());
        window.addEventListener('volumeChange', function(e) {
            const volume = e.detail.volume;
            setVictoryVolume(volume);
        });

        // Hook victory modal to play victory sound
        const originalShowVictoryModal = window.showVictoryModal;
        if (typeof originalShowVictoryModal === 'function') {
            window.showVictoryModal = function() {
                originalShowVictoryModal.apply(this, arguments);
                playVictorySound();
            };
        }
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

