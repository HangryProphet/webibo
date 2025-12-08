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
            </div>
        </div>
    </div>

    <!-- Main Horizontal Cards Layout -->
    <div class="lecture-main">
        <div class="cards-container" id="cardsContainer">
            <!-- Content will be injected here by JavaScript -->
        </div>
    </div>

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
        </button>
    </div>

    <script>
        // Pass data to JS
        window.redirectUrl = "<?php echo $redirectUrl; ?>";
        
        // Parse the HTML content into cards
        const htmlContent = <?php echo json_encode($lectureHtml); ?>;
        const cardsContainer = document.getElementById('cardsContainer');
        
        // Create a temporary div to parse HTML
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlContent;
        
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
        }
        
        function markComplete() {
            window.location.href = window.redirectUrl;
        }
        
        function goToPrevious() {
            window.history.back();
        }
    </script>
    <script src="../assets/js/lecture.js"></script>
</body>
</html>
