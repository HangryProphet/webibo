// Dynamic Dashboard with Database-Driven Roadmap
document.addEventListener('DOMContentLoaded', function() {
    // Course module button click handler
    const courseModuleWrapper = document.querySelector('.course-module-wrapper');
    const courseModuleBtn = document.querySelector('.course-module-btn');
    
    if (courseModuleBtn && courseModuleWrapper) {
        courseModuleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Close all other popups
            document.querySelectorAll('.hover-popup, .sidebar-hover-popup').forEach(p => {
                p.style.opacity = '0';
                p.style.visibility = 'hidden';
            });
            
            // Toggle active class
            courseModuleWrapper.classList.toggle('active');
        });
        
        // Close popup when clicking outside
        document.addEventListener('click', function(e) {
            if (!courseModuleWrapper.contains(e.target)) {
                courseModuleWrapper.classList.remove('active');
            }
        });
    }
    
    // Course selection handlers
    const courseIcons = document.querySelectorAll('.course-icon-item');
    courseIcons.forEach((icon, index) => {
        icon.addEventListener('click', function() {
            const courseId = index + 1; // 0=HTML(1), 1=CSS(2), 2=JS(3)
            selectCourse(courseId);
        });
    });
    
    const roadmap = document.querySelector('.roadmap');
    const trailSvg = document.querySelector('.roadmap-trail');
    const trailPath = document.querySelector('.trail-path');
    
    // Get selected course from localStorage or default to HTML
    let currentCourseId = parseInt(localStorage.getItem('selectedCourse')) || 1;
    
    // Fetch levels from backend and render dynamically
    fetchAndRenderLevels(currentCourseId);
    
    // Update course button display
    updateCourseButton(currentCourseId);
    
    // Initialize drag-to-scroll functionality
    initDragScroll();
    
    // Fetch levels from API and render them
    async function fetchAndRenderLevels(courseId) {
        try {
            const response = await fetch(`../controllers/roadmap_get.php?course_id=${courseId}`);
            if (!response.ok) {
                throw new Error('Failed to fetch levels');
            }
            
            const levels = await response.json();
            
            // DEBUG: Log fetched levels
            console.log('Fetched levels for course', courseId, ':', levels);
            console.log('First 3 levels status:', levels.slice(0, 3).map(l => ({id: l.id, status: l.status})));
            
            // Render all levels
            renderLevels(levels);
            
            // Draw trail after rendering
            setTimeout(drawTrail, 100);
        setTimeout(scrollToCurrentNode, 120);
            
        } catch (error) {
            console.error('Error fetching levels:', error);
            // Show error message to user
            roadmap.innerHTML = '<div style="color: red; padding: 20px;">Failed to load levels. Please refresh the page.</div>';
        }
    }
    
    // Render level nodes dynamically
    function renderLevels(levels) {
        // Clear existing nodes
        const existingNodes = roadmap.querySelectorAll('.level-node');
        existingNodes.forEach(node => node.remove());
        
        levels.forEach((level, index) => {
            const node = createLevelNode(level);
            roadmap.appendChild(node);
        });
        
        // Re-query level nodes after rendering
        levelNodes = document.querySelectorAll('.level-node');
        ensureCurrentNodeExists();
        
        // Draw trail after nodes are in DOM
        setTimeout(() => {
            drawTrail();
            scrollToCurrentNode();
        }, 100);
    }
    
    // Select and switch to a course
    function selectCourse(courseId) {
        // Save to localStorage
        localStorage.setItem('selectedCourse', courseId);
        currentCourseId = courseId;
        
        // Update UI
        updateCourseButton(courseId);
        
        // Close popup
        courseModuleWrapper.classList.remove('active');
        
        // Reload roadmap with new course
        fetchAndRenderLevels(courseId);
    }
    
    // Update course button display
    function updateCourseButton(courseId) {
        const iconSection = document.querySelector('.course-icon-section i');
        const labelSection = document.querySelector('.course-label');
        const titleSection = document.querySelector('.course-title');
        
        const courses = [
            { icon: 'fab fa-html5', label: 'Module 1', title: 'Introduction to HTML' },
            { icon: 'fab fa-css3-alt', label: 'Module 2', title: 'Introduction to CSS' },
            { icon: 'fab fa-js', label: 'Module 3', title: 'Introduction to JavaScript' }
        ];
        
        const course = courses[courseId - 1];
        if (iconSection && labelSection && titleSection && course) {
            iconSection.className = course.icon;
            labelSection.textContent = course.label;
            titleSection.textContent = course.title;
        }
    }
    
    // Create a single level node element
    function createLevelNode(level) {
        const node = document.createElement('div');
        node.className = `level-node ${level.status}`;
        node.dataset.levelId = level.id;
        node.dataset.activityType = level.type === 'lecture' ? 'Lecture' : 'Challenge';
        
        // DEBUG: Log class application for first 3 nodes
        if (level.id <= 3) {
            console.log(`Creating level ${level.id}: status=${level.status}, className="${node.className}"`);
        }
        
        // Position the node
        node.style.left = level.position.left + 'px';
        node.style.top = level.position.top + 'px';
        
        // Add lecture class if it's a lecture
        if (level.type === 'lecture') {
            node.classList.add('lecture-node');
        }
        
        // Create node content
        const iconClass = 'fas ' + level.icon;
        const lockIconHtml = '<i class="fas fa-lock"></i>';
        const displayTitle = level.status === 'locked' ? lockIconHtml : level.title;
        const popupTitleHtml = level.status === 'locked'
            ? `<div class="node-popup-text">${lockIconHtml}</div>`
            : '';
        node.innerHTML = `
            <i class="${iconClass}"></i>
            <div class="node-label">${displayTitle}</div>
            <div class="node-popup">
                ${popupTitleHtml}
                <div class="node-popup-type">${level.type === 'lecture' ? 'Lecture' : 'Challenge'}</div>
                <div class="node-popup-xp">${level.xp_reward} XP</div>
            </div>
        `;
        
        // Add click handler (only for non-locked nodes)
        if (level.status !== 'locked') {
            node.style.cursor = 'pointer';
            node.addEventListener('click', function() {
                const targetPage = level.type === 'lecture' 
                    ? `lecture.php?id=${level.id}` 
                    : `activity.php?id=${level.id}`;
                redirectWithLoading(targetPage);
            });
        } else {
            node.style.cursor = 'not-allowed';
            // Update popup text for locked nodes
            const popupText = node.querySelector('.node-popup-text');
            if (popupText) {
                popupText.innerHTML = lockIconHtml;
            }
        }
        
        return node;
    }
    
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

    // Make sure there's always a current (blue) node to guide the user
    function ensureCurrentNodeExists() {
        if (!levelNodes || !levelNodes.length) return;
        
        const hasCurrent = Array.from(levelNodes).some(node => node.classList.contains('current'));
        if (hasCurrent) return;

        // Prefer the first non-locked node; otherwise fallback to the first node
        const firstUnlocked = Array.from(levelNodes).find(node => !node.classList.contains('locked'));
        const targetNode = firstUnlocked || levelNodes[0];
        if (targetNode) {
            targetNode.classList.add('current');
        }
    }

    // Drag-to-scroll functionality
    function initDragScroll() {
        const container = document.querySelector('.roadmap-container');
        if (!container) return;

        let isDown = false;
        let isDragging = false;
        let startX;
        let scrollLeft;
        let startTime;
        const dragThreshold = 5; // pixels to move before considering it a drag

        container.addEventListener('mousedown', (e) => {
            // Only start drag on the container itself or the roadmap, not on nodes
            if (e.target.closest('.level-node')) return;
            
            isDown = true;
            isDragging = false;
            startTime = Date.now();
            container.classList.add('dragging');
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
            container.style.cursor = 'grabbing';
            container.style.userSelect = 'none';
        });

        container.addEventListener('mouseleave', () => {
            if (isDown) {
                isDown = false;
                isDragging = false;
                container.classList.remove('dragging');
                container.style.cursor = 'grab';
                container.style.userSelect = '';
            }
        });

        container.addEventListener('mouseup', (e) => {
            if (isDown) {
                const endTime = Date.now();
                const timeDiff = endTime - startTime;
                
                // If it was a quick click (not a drag), don't prevent other interactions
                if (!isDragging && timeDiff < 200) {
                    // This was just a click, not a drag
                }
                
                isDown = false;
                isDragging = false;
                container.classList.remove('dragging');
                container.style.cursor = 'grab';
                container.style.userSelect = '';
            }
        });

        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 1.5; // Scroll speed multiplier
            
            // Check if we've moved enough to consider this a drag
            if (Math.abs(walk) > dragThreshold) {
                isDragging = true;
            }
            
            if (isDragging) {
                container.scrollLeft = scrollLeft - walk;
            }
        });

        // Set initial cursor
        container.style.cursor = 'grab';
    }

    // Scroll to current level node on all views
    function scrollToCurrentNode() {
        const currentNode = document.querySelector('.level-node.current');
        const roadmapContainer = document.querySelector('.roadmap-container');
        const roadmap = document.querySelector('.roadmap');
        
        if (currentNode && roadmapContainer && roadmap) {
            // Get the scale factor based on screen size
            const scale = window.innerWidth <= 480
                ? 0.6
                : window.innerWidth <= 768
                    ? 0.75
                    : window.innerWidth <= 1024
                        ? 0.85
                        : 1;
            
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
                
                // Close course module popup
                if (courseModuleWrapper) {
                    courseModuleWrapper.classList.remove('active');
                }
                
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
                
                // Close course module popup
                if (courseModuleWrapper) {
                    courseModuleWrapper.classList.remove('active');
                }
                
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
            if (!e.target.closest('.top-btn-wrapper') && !e.target.closest('.menu-item-wrapper') && !e.target.closest('.course-module-wrapper')) {
                document.querySelectorAll('.hover-popup, .sidebar-hover-popup').forEach(popup => {
                    popup.style.opacity = '0';
                    popup.style.visibility = 'hidden';
                });
                
                // Close course module popup
                if (courseModuleWrapper) {
                    courseModuleWrapper.classList.remove('active');
                }
            }
        });
    }
});