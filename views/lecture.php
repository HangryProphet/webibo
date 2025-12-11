<?php require_once __DIR__ . '/../controllers/lecture_handler.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($level['title']); ?> - Lecture - Webibo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/lecture.css">
</head>
<body>
    <!-- Header with Progress -->
    <div class="header">
        <button class="close-btn" onclick="showExitModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%;"></div>
            </div>
        </div>
    </div>

    <!-- Main Horizontal Cards Layout -->
    <div class="lecture-main">
        <div class="cards-container" id="cardsContainer">
            <!-- Content will be injected here by JavaScript -->
        </div>
    </div>

    <!-- Navigation Footer -->
    <div class="lecture-nav-footer">
        <button type="button" class="nav-btn nav-btn-secondary" id="prevBtn" onclick="previousSlide()" style="display: none;">
            PREVIOUS
        </button>
        <button type="button" class="nav-btn nav-btn-primary" id="nextBtn" onclick="nextSlide()">
            NEXT
        </button>
        <button type="button" class="nav-btn nav-btn-primary" id="completeBtn" onclick="showVictoryModal()" style="display: none;">
            COMPLETE
        </button>
    </div>

    <script>
        // Pass data to JS
        window.redirectUrl = "<?php echo $redirectUrl; ?>";
        
        // Parse the HTML content into slides
        const htmlContent = <?php echo json_encode($lectureHtml); ?>;
        const cardsContainer = document.getElementById('cardsContainer');
        
        // Create a temporary div to parse HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlContent;
        
        // Extract content from body tag if it exists (handles full HTML documents)
        let contentRoot = tempDiv;
        const bodyElement = tempDiv.querySelector('body');
        if (bodyElement) {
            contentRoot = bodyElement;
        }
        
        // Extract slides based on page-break/page-header or slide divs
        let slides = [];
        const pageBreaks = contentRoot.querySelectorAll('.page-header, .page-break');
        const slideDivs = contentRoot.querySelectorAll('.slide');
        
        if (pageBreaks.length > 0) {
            // Split content by page-header and page-break
            const allChildren = Array.from(contentRoot.children);
            let currentSlide = null;
            
            allChildren.forEach((child, index) => {
                if (child.classList.contains('page-header')) {
                    // Start a new slide when we encounter a page header
                    if (currentSlide) {
                        slides.push(currentSlide);
                    }
                    currentSlide = document.createElement('div');
                    currentSlide.className = 'lecture-slide';
                    currentSlide.appendChild(child.cloneNode(true));
                } else if (child.classList.contains('page-break')) {
                    // Page break marks the end of a slide, don't include it
                    if (currentSlide) {
                        slides.push(currentSlide);
                        currentSlide = null;
                    }
                } else if (currentSlide) {
                    // Add content to current slide
                    currentSlide.appendChild(child.cloneNode(true));
                }
            });
            
            // Add the last slide if there's content
            if (currentSlide) {
                slides.push(currentSlide);
            }
        } else if (slideDivs.length > 0) {
            // Use slide divs
            slideDivs.forEach((slide, index) => {
                const slideWrapper = document.createElement('div');
                slideWrapper.className = 'lecture-slide';
                slideWrapper.innerHTML = slide.innerHTML;
                slides.push(slideWrapper);
            });
        } else {
            // No page breaks, show everything as one slide
            const slide = document.createElement('div');
            slide.className = 'lecture-slide';
            // Use innerHTML of contentRoot if it's a body element, otherwise use htmlContent
            slide.innerHTML = bodyElement ? bodyElement.innerHTML : htmlContent;
            slides.push(slide);
        }
        
        // Add slides to container
        slides.forEach((slide, index) => {
            slide.style.display = index === 0 ? 'block' : 'none';
            cardsContainer.appendChild(slide);
        });
        
        // Slide navigation state
        let currentSlideIndex = 0;
        const totalSlides = slides.length;
        
        // Update navigation buttons
        function updateNavigation() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const completeBtn = document.getElementById('completeBtn');
            const footer = document.querySelector('.lecture-nav-footer');
            
            // Show/hide Previous button
            const showPrev = currentSlideIndex > 0;
            prevBtn.style.display = showPrev ? 'inline-flex' : 'none';
            
            // Adjust footer layout based on whether previous is visible
            if (showPrev) {
                footer.style.justifyContent = 'space-between';
            } else {
                footer.style.justifyContent = 'flex-end';
            }
            
            // Show/hide Next and Finish buttons
            if (currentSlideIndex === totalSlides - 1) {
                // Last slide
                nextBtn.style.display = 'none';
                completeBtn.style.display = 'inline-flex';
            } else {
                // Not last slide
                nextBtn.style.display = 'inline-flex';
                completeBtn.style.display = 'none';
            }
        }
        
        // Update progress bar based on slide progress
        function updateProgress() {
            const progressFill = document.getElementById('progressFill');
            const progress = ((currentSlideIndex + 1) / totalSlides) * 100;
            progressFill.style.width = progress + '%';
        }
        
        // Navigation functions
        function nextSlide() {
            if (currentSlideIndex < totalSlides - 1) {
                slides[currentSlideIndex].style.display = 'none';
                currentSlideIndex++;
                slides[currentSlideIndex].style.display = 'block';
                updateNavigation();
                updateProgress();
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
        
        function previousSlide() {
            if (currentSlideIndex > 0) {
                slides[currentSlideIndex].style.display = 'none';
                currentSlideIndex--;
                slides[currentSlideIndex].style.display = 'block';
                updateNavigation();
                updateProgress();
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
        
        function showVictoryModal() {
            const victoryModal = document.getElementById('victoryModal');
            const victoryMessage = document.getElementById('victoryMessage');
            
            if (victoryModal) {
                // Customize message based on next level availability
                if (window.hasNextLevel) {
                    victoryMessage.textContent = 'Great job! You\'ve mastered this lesson! Ready for the next one?';
                } else {
                    victoryMessage.textContent = 'Incredible! You\'ve completed all lessons in this course! 🎓';
                }
                
                victoryModal.classList.add('active');
            }
        }
        
        function completeLecture() {
            // Submit completion form to save progress
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            
            const completeInput = document.createElement('input');
            completeInput.type = 'hidden';
            completeInput.name = 'complete';
            completeInput.value = '1';
            
            const levelIdInput = document.createElement('input');
            levelIdInput.type = 'hidden';
            levelIdInput.name = 'level_id';
            levelIdInput.value = '<?php echo $levelId; ?>';
            
            form.appendChild(completeInput);
            form.appendChild(levelIdInput);
            document.body.appendChild(form);
            form.submit();
        }
        
        // Initialize navigation and progress
        updateNavigation();
        updateProgress();
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && currentSlideIndex > 0) {
                previousSlide();
            } else if (e.key === 'ArrowRight' && currentSlideIndex < totalSlides - 1) {
                nextSlide();
            } else if (e.key === 'Enter' && currentSlideIndex === totalSlides - 1) {
                if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    showVictoryModal();
                }
            }
        });
    </script>
    
    <!-- Exit Confirmation Modal -->
    <div class="modal-overlay" id="exitModal">
        <div class="modal-content">
            <img src="../assets/img/wiza/wiza-sad.png" alt="Wiza Sad" class="modal-image">
            <h2 class="modal-title">Hold on! Leaving now will reset your current progress.</h2>
            <p class="modal-message">Do you still want to end this session?</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-primary" onclick="closeModal()">KEEP LEARNING</button>
                <button class="modal-btn modal-btn-danger" onclick="exitToDashboard()">END SESSION</button>
            </div>
        </div>
    </div>
    
    <!-- Victory Modal -->
    <div class="modal-overlay" id="victoryModal">
        <div class="modal-content">
            <img src="../assets/img/wiza/wiza-heart-eyes.png" alt="Wiza Celebrating" class="modal-image">
            <h2 class="modal-title" style="color: #58cc02;">Lecture Complete! 🎉</h2>
            <p class="modal-message" id="victoryMessage">Great job! You've mastered this lesson!</p>
            <div class="modal-buttons">
                <button class="modal-btn modal-btn-primary" onclick="completeLecture()" id="nextLevelBtn">
                    <?php echo $hasNextLevel ? 'CONTINUE TO NEXT LEVEL' : 'BACK TO MAP'; ?>
                </button>
                <?php if ($hasNextLevel): ?>
                <button class="modal-btn modal-btn-secondary" onclick="exitToDashboard()">BACK TO MAP</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Pass next level data to JavaScript
        window.hasNextLevel = <?php echo $hasNextLevel ? 'true' : 'false'; ?>;
        window.nextLevelUrl = "<?php echo $nextLevelUrl; ?>";
    </script>
    
    <script src="../assets/js/lecture.js"></script>
</body>
</html>
