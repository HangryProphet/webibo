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
<<<<<<< HEAD
    <div class="lecture-header">
        <div class="lecture-logo">
            <a href="dashboard.php">
                <i class="fas fa-code"></i> Webibo
            </a>
        </div>
        <div class="lecture-progress">
            <span class="progress-text">Progress: <?php echo round(($currentProgress / 10) * 100); ?>%</span>
            <div class="progress-bar-mini">
                <div class="progress-fill-mini" style="width: <?php echo ($currentProgress / 10) * 100; ?>%;"></div>
=======
    <div class="header">
        <button class="close-btn" onclick="showExitModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%;"></div>
>>>>>>> origin/travis
            </div>
        </div>
    </div>

    <!-- Main Horizontal Cards Layout -->
    <div class="lecture-main">
        <div class="cards-container" id="cardsContainer">
            <!-- Content will be injected here by JavaScript -->
        </div>
    </div>

<<<<<<< HEAD
    <!-- Mascot Guide -->
    <div class="mascot-guide">
        <img src="../assets/img/wiza/wiza-teach.png" alt="Wiza Guide" class="mascot-image">
    </div>

    <!-- Navigation Footer -->
    <div class="lecture-nav-footer">
        <button type="button" class="nav-btn nav-btn-secondary" id="prevBtn" onclick="goToPrevious()">
            <i class="fas fa-arrow-left"></i> Previous
        </button>
        <button type="button" class="nav-btn nav-btn-primary" id="completeBtn" onclick="markComplete()">
            Continue <i class="fas fa-arrow-right"></i>
=======


    <!-- Navigation Footer -->
    <div class="lecture-nav-footer">
        <button type="button" class="nav-btn nav-btn-secondary" id="prevBtn" onclick="previousSlide()" style="display: none;">
            PREVIOUS
        </button>
        <button type="button" class="nav-btn nav-btn-primary" id="nextBtn" onclick="nextSlide()">
            NEXT
        </button>
        <button type="button" class="nav-btn nav-btn-primary" id="completeBtn" onclick="markComplete()" style="display: none;">
            FINISH
>>>>>>> origin/travis
        </button>
    </div>

    <script>
        // Pass data to JS
        window.redirectUrl = "<?php echo $redirectUrl; ?>";
        
<<<<<<< HEAD
        // Parse the HTML content into cards
=======
        // Parse the HTML content into slides
>>>>>>> origin/travis
        const htmlContent = <?php echo json_encode($lectureHtml); ?>;
        const cardsContainer = document.getElementById('cardsContainer');
        
        // Create a temporary div to parse HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlContent;
        
<<<<<<< HEAD
        // Extract slides or create cards from content
        const slides = tempDiv.querySelectorAll('.slide');
        
        if (slides.length > 0) {
            // If slides exist, convert each to a card
            slides.forEach((slide, index) => {
                const card = document.createElement('div');
                card.className = 'lecture-card';
                card.innerHTML = slide.innerHTML;
                cardsContainer.appendChild(card);
            });
        } else {
            // Otherwise, split by h1/h2 tags
            const children = Array.from(tempDiv.children);
            let currentCard = null;
            
            children.forEach(child => {
                if (child.tagName === 'H1' || child.tagName === 'H2') {
                    // Start new card
                    currentCard = document.createElement('div');
                    currentCard.className = 'lecture-card';
                    currentCard.appendChild(child.cloneNode(true));
                    cardsContainer.appendChild(currentCard);
                } else if (currentCard) {
                    // Add to current card
                    currentCard.appendChild(child.cloneNode(true));
                } else {
                    // No card yet, create first one
                    currentCard = document.createElement('div');
                    currentCard.className = 'lecture-card';
                    currentCard.appendChild(child.cloneNode(true));
                    cardsContainer.appendChild(currentCard);
                }
            });
        }
        
        // If no cards created, show all content in one card
        if (cardsContainer.children.length === 0) {
            const card = document.createElement('div');
            card.className = 'lecture-card';
            card.innerHTML = htmlContent;
            cardsContainer.appendChild(card);
=======
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
            let foundFirstPageBreak = false;
            
            allChildren.forEach((child, index) => {
                if (child.classList.contains('page-header') || child.classList.contains('page-break')) {
                    // Start a new slide when we encounter a page break
                    if (currentSlide) {
                        slides.push(currentSlide);
                    }
                    currentSlide = document.createElement('div');
                    currentSlide.className = 'lecture-slide';
                    currentSlide.appendChild(child.cloneNode(true));
                    foundFirstPageBreak = true;
                } else if (foundFirstPageBreak && currentSlide) {
                    // Only add to current slide if we've already found the first page break
                    // This ensures we skip any content before the first page break
                    currentSlide.appendChild(child.cloneNode(true));
                }
                // Ignore any content before the first page break
            });
            
            // Add the last slide (only if we found at least one page break)
            if (currentSlide && foundFirstPageBreak) {
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
>>>>>>> origin/travis
        }
        
        function markComplete() {
            window.location.href = window.redirectUrl;
        }
        
<<<<<<< HEAD
        function goToPrevious() {
            window.history.back();
        }
    </script>
=======
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
                    markComplete();
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
    
>>>>>>> origin/travis
    <script src="../assets/js/lecture.js"></script>
</body>
</html>
