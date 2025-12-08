// Loading Screen Progress Bar Animation
(function() {
    const progressFill = document.getElementById('loadingProgress');
    
    if (!progressFill) return;
    
    // Get redirect URL from query parameter
    const urlParams = new URLSearchParams(window.location.search);
    const redirectUrl = urlParams.get('redirect') || 'dashboard.php';
    
    let progress = 0;
    const targetProgress = 100;
    const duration = 2000; // 2 seconds
    const startTime = Date.now();
    
    function updateProgress() {
        const elapsed = Date.now() - startTime;
        progress = Math.min((elapsed / duration) * targetProgress, targetProgress);
        progressFill.style.width = progress + '%';
        
        if (progress < targetProgress) {
            requestAnimationFrame(updateProgress);
        } else {
            // Redirect after animation completes
            window.location.href = redirectUrl;
        }
    }
    
    // Start animation
    requestAnimationFrame(updateProgress);
})();

