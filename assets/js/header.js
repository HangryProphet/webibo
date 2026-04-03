function toggleMenu() {
    const headerNav = document.querySelector('.header-nav');
    if (headerNav) {
        headerNav.classList.toggle('mobile-open');
    }
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const headerNav = document.querySelector('.header-nav');
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    
    if (window.innerWidth <= 1024 && 
        headerNav && 
        !headerNav.contains(event.target) && 
        !menuToggle.contains(event.target) &&
        headerNav.classList.contains('mobile-open')) {
        headerNav.classList.remove('mobile-open');
    }
});

