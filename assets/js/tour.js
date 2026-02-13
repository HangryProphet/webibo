/**
 * Webibo Perfected Walkthrough
 * 
 * Theme-aligned, responsive, and handles mobile navigation.
 */
class WebiboTour {
    constructor() {
        this.steps = [
            {
                title: "Welcome Back!",
                content: "I'm Wiza, your master of code! Ready to continue your journey to greatness?",
                wiza: "../assets/img/wiza/wiza-flying.webp",
                target: null
            },
            {
                title: "Your Adventure",
                content: "Explore the vast world of coding right here. Your roadmap to mastery awaits!",
                wiza: "../assets/img/wiza/wiza-pointing.webp",
                target: "a.nav-item[href='dashboard.php']",
                mobilePrereq: ".mobile-menu-toggle"
            },
            {
                title: "The Roadmap",
                content: "Follow this path to level up your skills. Each node is a new milestone!",
                wiza: "../assets/img/wiza/wiza-teach-happy.webp",
                target: ".roadmap-container"
            },
            {
                title: "Master Lessons",
                content: "Orange nodes are Lectures. This is where the magic happens and knowledge is gained!",
                wiza: "../assets/img/wiza/wiza-teach.webp",
                target: ".level-node.lecture-node"
            },
            {
                title: "Accept Challenges",
                content: "Blue nodes are Challenges! Complete them to prove your worth and earn XP.",
                wiza: "../assets/img/wiza/wiza-pointing.webp",
                target: ".level-node:not(.lecture-node):not(.locked)"
            },
            {
                title: "The Module Switch",
                content: "Jump between HTML, CSS, and JS modules anytime using this selector.",
                wiza: "../assets/img/wiza/wiza-pointing.webp",
                target: ".course-module-btn"
            },
            {
                title: "Track Success",
                content: "Keep an eye on your XP, Streaks, and Badges here. They reflect your growth as a hero!",
                wiza: "../assets/img/wiza/wiza-thinking.webp",
                target: ".top-right-buttons" // Spotlight for stats
            },
            {
                title: "Hall of Fame",
                content: "View all your hard-earned Achievements here. Collect them all!",
                wiza: "../assets/img/wiza/wiza-heart-eyes.webp",
                target: "a.nav-item[href='achievements.php']",
                mobilePrereq: ".mobile-menu-toggle"
            },
            {
                title: "Expert Guidance",
                content: "Stuck on a tricky problem? Tap the lightbulb to access the student guide for hints!",
                wiza: "../assets/img/wiza/wiza-teach.webp",
                target: ".idea-node-btn" // Spotlight for help/guide button
            },
            {
                title: "Ready to Start?",
                content: "Your legendary adventure begins now! Go forth and code!",
                wiza: "../assets/img/wiza/wiza-heart-eyes.webp",
                target: null
            }
        ];

        this.currentStep = 0;
        this.overlay = null;
        this.hole = null;
        this.tooltip = null;
    }

    init() {
        this.createElements();
        this.showStep();
        window.addEventListener('resize', () => {
            if (this.overlay) this.updateLayout();
        });
    }

    createElements() {
        if (document.querySelector('.tour-overlay')) return;

        this.overlay = document.createElement('div');
        this.overlay.className = 'tour-overlay';

        const svgNamespace = "http://www.w3.org/2000/svg";
        const svg = document.createElementNS(svgNamespace, "svg");
        svg.setAttribute("class", "tour-svg-mask");

        const defs = document.createElementNS(svgNamespace, "defs");
        const mask = document.createElementNS(svgNamespace, "mask");
        mask.setAttribute("id", "tour-mask");

        const bgRect = document.createElementNS(svgNamespace, "rect");
        bgRect.setAttribute("width", "100%");
        bgRect.setAttribute("height", "100%");
        bgRect.setAttribute("fill", "white");

        this.hole = document.createElementNS(svgNamespace, "rect");
        this.hole.setAttribute("fill", "black");
        this.hole.setAttribute("rx", "16");

        mask.appendChild(bgRect);
        mask.appendChild(this.hole);
        defs.appendChild(mask);
        svg.appendChild(defs);

        const finalRect = document.createElementNS(svgNamespace, "rect");
        finalRect.setAttribute("width", "100%");
        finalRect.setAttribute("height", "100%");
        finalRect.setAttribute("fill", "rgba(10, 15, 20, 0.8)"); // Dark theme overlay
        finalRect.setAttribute("mask", "url(#tour-mask)");

        svg.appendChild(finalRect);
        this.overlay.appendChild(svg);

        this.tooltip = document.createElement('div');
        this.tooltip.className = 'tour-tooltip';

        document.body.appendChild(this.overlay);
        document.body.appendChild(this.tooltip);
    }

    isMobile() {
        return window.innerWidth <= 1024;
    }

    showStep() {
        const step = this.steps[this.currentStep];

        // Handle Mobile menu prerequisite
        if (this.isMobile() && step.mobilePrereq) {
            const menu = document.querySelector('.header-nav');
            if (menu && !menu.classList.contains('mobile-open')) {
                // Temporary step to open menu
                this.tooltip.innerHTML = `
                    <button class="tour-skip" onclick="webiboTour.finish()">Skip</button>
                    <div class="tour-wiza-wrapper"><img src="../assets/img/wiza/wiza-pointing.webp" class="tour-wiza-img" alt="Wiza"></div>
                    <div class="tour-body">
                        <div class="tour-title">Open Menu</div>
                        <div class="tour-content">Tap the hamburger menu to find your ${step.title} link!</div>
                    </div>
                    <div class="tour-footer">
                        <button class="tour-btn tour-btn-next" onclick="webiboTour.openMobileMenu()">Open Menu</button>
                    </div>
                `;
                this.updateLayout(step.mobilePrereq);
                setTimeout(() => this.tooltip.classList.add('active'), 50);
                return;
            }
        }

        this.tooltip.innerHTML = `
            <button class="tour-skip" onclick="webiboTour.finish()">Skip</button>
            <div class="tour-wiza-wrapper"><img src="${step.wiza}" class="tour-wiza-img" alt="Wiza"></div>
            <div class="tour-body">
                <div class="tour-title">${step.title}</div>
                <div class="tour-content">${step.content}</div>
                <div class="tour-arrow"></div>
            </div>
            <div class="tour-footer">
                ${this.currentStep > 0 ? '<button class="tour-btn tour-btn-prev" onclick="webiboTour.prev()">Back</button>' : ''}
                <button class="tour-btn tour-btn-next" onclick="webiboTour.next()">
                    ${this.currentStep === this.steps.length - 1 ? 'Start Now!' : 'Next'}
                </button>
            </div>
        `;

        this.updateLayout(step.target);
        setTimeout(() => this.tooltip.classList.add('active'), 50);
    }

    openMobileMenu() {
        const toggle = document.querySelector('.mobile-menu-toggle');
        if (toggle) toggle.click();
        this.showStep(); // Refresh current step inside open menu
    }

    updateLayout(targetSelector = null) {
        let targetElement = null;
        if (targetSelector) {
            targetElement = document.querySelector(targetSelector);
        }

        if (targetElement && targetElement.offsetParent !== null) {
            const rect = targetElement.getBoundingClientRect();
            const padding = 10;

            this.hole.setAttribute("x", rect.left - padding);
            this.hole.setAttribute("y", rect.top - padding);
            this.hole.setAttribute("width", rect.width + padding * 2);
            this.hole.setAttribute("height", rect.height + padding * 2);
            this.hole.setAttribute("rx", rect.width > 60 ? "16" : "50%");

            this.positionTooltip(rect);
        } else {
            this.hole.setAttribute("width", "0");
            this.hole.setAttribute("height", "0");

            this.tooltip.style.top = '50%';
            this.tooltip.style.left = '50%';
            this.tooltip.style.transform = 'translate(-50%, -50%)';
            const arrow = this.tooltip.querySelector('.tour-arrow');
            if (arrow) arrow.className = 'tour-arrow';
        }
    }

    positionTooltip(targetRect) {
        const tooltipWidth = this.tooltip.offsetWidth || 360;
        const tooltipHeight = this.tooltip.offsetHeight || 220;
        const padding = 25;
        const arrow = this.tooltip.querySelector('.tour-arrow');

        let top, left, arrowClass;

        // Space analysis
        const spaceBelow = window.innerHeight - targetRect.bottom;
        const spaceAbove = targetRect.top;

        if (spaceBelow > tooltipHeight + padding) {
            top = targetRect.bottom + padding;
            left = targetRect.left + (targetRect.width / 2) - (tooltipWidth / 2);
            arrowClass = 'tour-arrow tour-arrow-top';
        } else if (spaceAbove > tooltipHeight + padding) {
            top = targetRect.top - tooltipHeight - padding;
            left = targetRect.left + (targetRect.width / 2) - (tooltipWidth / 2);
            arrowClass = 'tour-arrow tour-arrow-bottom';
        } else {
            // Screen center fallback
            top = (window.innerHeight - tooltipHeight) / 2;
            left = (window.innerWidth - tooltipWidth) / 2;
            arrowClass = 'tour-arrow hidden';
        }

        left = Math.max(12, Math.min(left, window.innerWidth - tooltipWidth - 12));

        this.tooltip.style.top = `${top}px`;
        this.tooltip.style.left = `${left}px`;
        this.tooltip.style.transform = 'none';

        if (arrow && arrowClass !== 'tour-arrow hidden') {
            arrow.className = arrowClass;
            const arrowX = (targetRect.left + targetRect.width / 2) - left;
            arrow.style.left = `${Math.max(30, Math.min(arrowX, tooltipWidth - 30))}px`;
        } else if (arrow) {
            arrow.className = 'hidden';
        }
    }

    next() {
        if (this.currentStep < this.steps.length - 1) {
            this.tooltip.classList.remove('active');
            setTimeout(() => {
                this.currentStep++;
                this.showStep();
            }, 300);
        } else {
            this.finish();
        }
    }

    prev() {
        if (this.currentStep > 0) {
            this.tooltip.classList.remove('active');
            setTimeout(() => {
                this.currentStep--;
                this.showStep();
            }, 300);
        }
    }

    finish() {
        this.overlay.style.opacity = '0';
        this.tooltip.style.opacity = '0';

        setTimeout(() => {
            if (this.overlay && this.overlay.parentNode) {
                document.body.removeChild(this.overlay);
                document.body.removeChild(this.tooltip);
            }
            this.overlay = null;
        }, 400);

        fetch('../controllers/user_complete_walkthrough.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        }).catch(err => console.error(err));
    }
}

const webiboTour = new WebiboTour();
window.webiboTour = webiboTour;
