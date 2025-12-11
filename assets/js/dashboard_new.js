/**
 * Dynamic Dashboard with Paginated Roadmap
 * Implements infinite scroll loading for learning path
 */

// Configuration
const CONFIG = {
    API_ENDPOINT: '../controllers/roadmap_api.php',
    COURSE_ID: 1, // Default to HTML course (will be dynamic later)
    ITEMS_PER_PAGE: 10,
    SCROLL_THRESHOLD: 0.8 // Load next page when scrolled 80% of visible content
};

// State management
const state = {
    currentPage: 1,
    isLoading: false,
    hasMore: true,
    totalLevels: 0,
    loadedLevels: []
};

/**
 * Initialize dashboard on DOM ready
 */
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    setupEventListeners();
});

/**
 * Initialize the dashboard
 */
function initializeDashboard() {
    // Setup course module popup
    setupCourseModule();
    
    // Load initial batch of levels
    loadLevelsPage(1);
}

/**
 * Setup course module button and popup
 */
function setupCourseModule() {
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
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
    const roadmapContainer = document.querySelector('.roadmap-container');
    
    if (roadmapContainer) {
        // Add scroll event listener for infinite scroll
        roadmapContainer.addEventListener('scroll', handleScroll);
    }
}

/**
 * Handle scroll event for infinite loading
 */
function handleScroll(e) {
    const container = e.target;
    
    // Check if we should load more
    if (state.isLoading || !state.hasMore) {
        return;
    }
    
    // Calculate scroll position
    const scrollLeft = container.scrollLeft;
    const scrollWidth = container.scrollWidth;
    const clientWidth = container.clientWidth;
    
    // Calculate percentage scrolled
    const scrollPercentage = (scrollLeft + clientWidth) / scrollWidth;
    
    // Load next page if we've scrolled past threshold
    if (scrollPercentage >= CONFIG.SCROLL_THRESHOLD) {
        loadLevelsPage(state.currentPage + 1);
    }
}

/**
 * Load a page of levels from the API
 * @param {number} page - Page number to load
 */
async function loadLevelsPage(page) {
    if (state.isLoading) {
        return;
    }
    
    state.isLoading = true;
    
    try {
        const url = `${CONFIG.API_ENDPOINT}?course_id=${CONFIG.COURSE_ID}&page=${page}`;
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (data.success) {
            // Update state
            state.currentPage = data.page;
            state.hasMore = data.has_more;
            state.totalLevels = data.total_levels;
            
            // Render the new levels
            renderLevels(data.levels);
            
            // Draw the trail after rendering
            setTimeout(drawTrail, 100);
        } else {
            console.error('API error:', data.error);
        }
    } catch (error) {
        console.error('Failed to load levels:', error);
    } finally {
        state.isLoading = false;
    }
}

/**
 * Render level nodes on the roadmap
 * @param {Array} levels - Array of level data
 */
function renderLevels(levels) {
    const roadmap = document.querySelector('.roadmap');
    
    if (!roadmap) {
        console.error('Roadmap container not found');
        return;
    }
    
    levels.forEach(level => {
        // Skip if already loaded
        if (state.loadedLevels.includes(level.level_id)) {
            return;
        }
        
        // Create level node
        const node = createLevelNode(level);
        roadmap.appendChild(node);
        
        // Track loaded level
        state.loadedLevels.push(level.level_id);
    });
}

/**
 * Create a level node element
 * @param {Object} level - Level data
 * @returns {HTMLElement} Level node element
 */
function createLevelNode(level) {
    const node = document.createElement('div');
    node.className = `level-node ${level.status}`;
    
    // Add lecture class if it's a lecture type
    if (level.type === 'lecture') {
        node.classList.add('lecture-node');
    }
    
    // Set position
    node.style.left = `${level.position.left}px`;
    node.style.top = `${level.position.top}px`;
    
    // Set data attributes
    node.dataset.levelId = level.level_id;
    node.dataset.activityType = level.type;
    
    // Create icon
    const icon = document.createElement('i');
    icon.className = `fas ${level.icon}`;
    
    // Override icon for locked nodes
    if (level.status === 'locked') {
        icon.className = 'fas fa-lock';
    }
    
    node.appendChild(icon);
    
    // Create hover popup
    const popup = createNodePopup(level);
    node.appendChild(popup);
    
    // Add click handler for unlocked nodes
    if (level.status !== 'locked') {
        node.style.cursor = 'pointer';
        node.addEventListener('click', () => handleNodeClick(level));
    } else {
        node.style.cursor = 'not-allowed';
    }
    
    return node;
}

/**
 * Create hover popup for a node
 * @param {Object} level - Level data
 * @returns {HTMLElement} Popup element
 */
function createNodePopup(level) {
    const popup = document.createElement('div');
    popup.className = 'node-hover-popup';
    
    const text = document.createElement('div');
    text.className = 'node-popup-text';
    
    // Show ??? for locked levels
    if (level.status === 'locked') {
        text.textContent = '???';
    } else {
        text.textContent = `${getActivityLabel(level.type)}: ${level.title}`;
    }
    
    popup.appendChild(text);
    return popup;
}

/**
 * Get activity label from level type
 * @param {string} type - Level type
 * @returns {string} Activity label
 */
function getActivityLabel(type) {
    const labels = {
        'lecture': 'Lecture',
        'multiple-choice': 'Quiz',
        'fill-blank': 'Practice',
        'code-editor': 'Challenge'
    };
    
    return labels[type] || 'Activity';
}

/**
 * Handle node click event
 * @param {Object} level - Level data
 */
function handleNodeClick(level) {
    // Determine target page based on level type
    let targetPage;
    
    if (level.type === 'lecture') {
        targetPage = `lecture.php?id=${level.level_id}`;
    } else {
        targetPage = `activity.php?id=${level.level_id}`;
    }
    
    // Redirect through loading screen
    redirectWithLoading(targetPage);
}

/**
 * Redirect through loading screen
 * @param {string} targetPath - Target page path
 */
function redirectWithLoading(targetPath) {
    const loadingUrl = `loading_screen.php?redirect=${encodeURIComponent(targetPath)}`;
    window.location.href = loadingUrl;
}

/**
 * Draw the trail connecting level nodes
 */
function drawTrail() {
    const roadmap = document.querySelector('.roadmap');
    const trailSvg = document.querySelector('.roadmap-trail');
    const trailPath = document.querySelector('.trail-path');
    
    if (!roadmap || !trailSvg || !trailPath) {
        return;
    }
    
    const levelNodes = roadmap.querySelectorAll('.level-node');
    
    if (!levelNodes.length) {
        return;
    }
    
    // Match the SVG viewBox to the roadmap area for pixel-perfect alignment
    const width = roadmap.scrollWidth; // Use scrollWidth to account for overflow
    const height = roadmap.clientHeight;
    trailSvg.setAttribute('viewBox', `0 0 ${width} ${height}`);
    trailSvg.setAttribute('width', width);
    trailSvg.setAttribute('height', height);
    
    // Get node centers sorted by level ID
    const points = Array.from(levelNodes)
        .sort((a, b) => {
            const idA = parseInt(a.dataset.levelId) || 0;
            const idB = parseInt(b.dataset.levelId) || 0;
            return idA - idB;
        })
        .map(getNodeCenter)
        .filter(pt => Number.isFinite(pt.x) && Number.isFinite(pt.y));
    
    // Build smooth path
    const pathData = buildSmoothPath(points);
    trailPath.setAttribute('d', pathData);
}

/**
 * Get the center point of a node
 * @param {HTMLElement} node - Level node element
 * @returns {Object} Center point {x, y}
 */
function getNodeCenter(node) {
    const left = parseFloat(node.style.left) || 0;
    const top = parseFloat(node.style.top) || 0;
    return {
        x: left + node.offsetWidth / 2,
        y: top + node.offsetHeight / 2
    };
}

/**
 * Build a smooth Catmull-Rom spline path through points
 * @param {Array} points - Array of {x, y} points
 * @returns {string} SVG path data
 */
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

// Export functions for debugging (optional)
if (typeof window !== 'undefined') {
    window.DashboardAPI = {
        loadLevelsPage,
        drawTrail,
        state
    };
}
