// Add click handlers to roadmap nodes
document.addEventListener('DOMContentLoaded', function() {
    const levelNodes = document.querySelectorAll('.level-node');
    const roadmap = document.querySelector('.roadmap');
    const trailSvg = document.querySelector('.roadmap-trail');
    const trailPath = document.querySelector('.trail-path');
    
    // Map node indices to new universal view files with level IDs
    const nodeFileMap = [
        'lecture.php?id=1',    // Level 1: Lecture
        'activity.php?id=2',   // Level 2: Multiple Choice
        'lecture.php?id=3',    // Level 3: Lecture
        'activity.php?id=4',   // Level 4: Fill-Blank
        'lecture.php?id=5',    // Level 5: Lecture
        'activity.php?id=6',   // Level 6: Code Editor
        'lecture.php?id=7'     // Level 7: Lecture
    ];
    
    // Helper: route through loading screen for a brief delay
    function redirectWithLoading(targetPath) {
        const loadingUrl = `loading_screen.php?redirect=${encodeURIComponent(targetPath)}`;
        window.location.href = loadingUrl;
    }

    // Convert a list of points into a smooth Catmull-Rom spline path
    function buildSmoothPath(points) {
        if (points.length < 2) return '';

        const p = points.map(pt => ({ x: pt.x, y: pt.y }));
        let d = `M ${p[0].x} ${p[0].y}`;

        for (let i = 0; i < p.length - 1; i++) {
            const p0 = p[i - 1] || p[i];
            const p1 = p[i];
            const p2 = p[i + 1];
            const p3 = p[i + 2] || p2;

            const cp1x = p1.x + (p2.x - p0.x) / 6;
            const cp1y = p1.y + (p2.y - p0.y) / 6;
            const cp2x = p2.x - (p3.x - p1.x) / 6;
            const cp2y = p2.y - (p3.y - p1.y) / 6;

            d += ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${p2.x} ${p2.y}`;
        }

        return d;
    }

    function getNodeCenter(node) {
        const left = parseFloat(node.style.left) || 0;
        const top = parseFloat(node.style.top) || 0;
        return {
            x: left + node.offsetWidth / 2,
            y: top + node.offsetHeight / 2
        };
    }

    function drawTrail() {
        if (!roadmap || !trailSvg || !trailPath) return;
        if (!levelNodes.length) return;

        // Match the SVG viewBox to the roadmap area for pixel-perfect alignment
        const width = roadmap.clientWidth;
        const height = roadmap.clientHeight;
        trailSvg.setAttribute('viewBox', `0 0 ${width} ${height}`);
        trailSvg.setAttribute('width', width);
        trailSvg.setAttribute('height', height);

        const points = Array.from(levelNodes)
            .sort((a, b) => parseFloat(a.dataset.levelId || '0') - parseFloat(b.dataset.levelId || '0'))
            .map(getNodeCenter)
            .filter(pt => Number.isFinite(pt.x) && Number.isFinite(pt.y));

        const pathData = buildSmoothPath(points);
        trailPath.setAttribute('d', pathData);
    }
    
    levelNodes.forEach((node, index) => {
        // Always enable nodes and show them as completed
        node.classList.remove('locked', 'current');
        node.classList.add('completed');
        node.style.cursor = 'pointer';
        node.addEventListener('click', function() {
            // Use the mapped file if available, otherwise fallback to index-based naming
            const filePath = nodeFileMap[index] || `html/stage-${String(index + 1).padStart(3, '0')}-act.php`;
            redirectWithLoading(filePath);
        });
    });

    drawTrail();

    // Scroll to current level node on mobile view
    function scrollToCurrentNode() {
        if (window.innerWidth <= 1024) {
            const currentNode = document.querySelector('.level-node.current');
            const roadmapContainer = document.querySelector('.roadmap-container');
            const roadmap = document.querySelector('.roadmap');
            
            if (currentNode && roadmapContainer && roadmap) {
                // Get the scale factor based on screen size
                const scale = window.innerWidth <= 480 ? 0.6 : (window.innerWidth <= 768 ? 0.75 : 0.85);
                
                // Get the node's left position from inline style (relative to unscaled roadmap)
                const nodeLeft = parseFloat(currentNode.style.left) || 0;
                
                // Get the node's actual rendered width (accounts for scale transform on node)
                const nodeWidth = currentNode.offsetWidth;
                
                // Calculate the node's center position in the scaled roadmap coordinate system
                // Roadmap is scaled, so node positions are scaled too
                const nodeCenterInScaledRoadmap = (nodeLeft * scale) + (nodeWidth / 2);
                
                // Calculate scroll position to center the node
                // We want the node center to align with the container center
                const containerWidth = roadmapContainer.clientWidth;
                const scrollLeft = nodeCenterInScaledRoadmap - (containerWidth / 2);
                
                // Smooth scroll to center the current node
                roadmapContainer.scrollTo({
                    left: Math.max(0, scrollLeft),
                    behavior: 'smooth'
                });
            }
        }
    }

    // Scroll to current node on load (with a small delay to ensure DOM is ready)
    setTimeout(scrollToCurrentNode, 100);
    // Re-draw trail after layout settles
    setTimeout(drawTrail, 120);

    // Also scroll on window resize (in case orientation changes)
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            scrollToCurrentNode();
            drawTrail();
        }, 250);
    });

    // Handle top button hover popups on mobile (tap to show/hide)
    if (window.innerWidth <= 768) {
        const topBtnWrappers = document.querySelectorAll('.top-btn-wrapper');
        
        topBtnWrappers.forEach(wrapper => {
            const btn = wrapper.querySelector('.top-btn');
            const popup = wrapper.querySelector('.hover-popup');
            
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Close all other popups
                document.querySelectorAll('.hover-popup, .sidebar-hover-popup').forEach(p => {
                    if (p !== popup) {
                        p.style.opacity = '0';
                        p.style.visibility = 'hidden';
                    }
                });
                
                // Toggle current popup
                if (popup.style.opacity === '1') {
                    popup.style.opacity = '0';
                    popup.style.visibility = 'hidden';
                } else {
                    popup.style.opacity = '1';
                    popup.style.visibility = 'visible';
                }
            });
        });

        // Handle sidebar menu hover popups on mobile (tap to show/hide)
        const menuItemWrappers = document.querySelectorAll('.menu-item-wrapper');
        
        menuItemWrappers.forEach(wrapper => {
            const menuItem = wrapper.querySelector('.menu-item');
            const popup = wrapper.querySelector('.sidebar-hover-popup');
            
            menuItem.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Close all other popups
                document.querySelectorAll('.hover-popup, .sidebar-hover-popup').forEach(p => {
                    if (p !== popup) {
                        p.style.opacity = '0';
                        p.style.visibility = 'hidden';
                    }
                });
                
                // Toggle current popup
                if (popup.style.opacity === '1') {
                    popup.style.opacity = '0';
                    popup.style.visibility = 'hidden';
                } else {
                    popup.style.opacity = '1';
                    popup.style.visibility = 'visible';
                }
            });
        });
        
        // Close popups when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.top-btn-wrapper') && !e.target.closest('.menu-item-wrapper')) {
                document.querySelectorAll('.hover-popup, .sidebar-hover-popup').forEach(popup => {
                    popup.style.opacity = '0';
                    popup.style.visibility = 'hidden';
                });
            }
        });
    }
});