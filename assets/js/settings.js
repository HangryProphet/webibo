/**
 * Settings Page JavaScript
 * Handles sound volume control with localStorage persistence
 */

document.addEventListener('DOMContentLoaded', function() {
    const volumeSlider = document.querySelector('input[name="sound_volume"]');
    const volumeLabel = document.querySelector('.volume-control .hint');
    
    if (!volumeSlider || !volumeLabel) return;
    
    // Load saved volume from localStorage (defaults to 70%)
    const savedVolume = localStorage.getItem('webibo_sound_volume');
    if (savedVolume !== null) {
        const volume = parseInt(savedVolume);
        volumeSlider.value = volume;
        volumeLabel.textContent = volume + '%';
    }
    
    // Update volume display and save to localStorage on change
    volumeSlider.addEventListener('input', function() {
        const volume = parseInt(this.value);
        volumeLabel.textContent = volume + '%';
        localStorage.setItem('webibo_sound_volume', volume);
        
        // Dispatch custom event to notify other scripts of volume change
        window.dispatchEvent(new CustomEvent('volumeChange', { 
            detail: { volume: volume / 100 } 
        }));
    });
});
