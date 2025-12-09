<?php require_once __DIR__ . '/../controllers/achievements.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Achievements - Webibo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/achievements.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">All Achievements</h1>
            <div class="achievement-stats">
                <span class="stat-item">
                    <i class="fas fa-trophy"></i> <?php echo $earnedCount; ?> / <?php echo $totalAchievements; ?>
                </span>
                <span class="stat-item">
                    <i class="fas fa-chart-line"></i> <?php echo $completionPercentage; ?>% Complete
                </span>
            </div>
        </div>

        <h2 class="section-title">Your Achievements</h2>

        <div class="achievements-grid">
            <?php foreach ($achievementsData as $achievement): ?>
                <div class="achievement-card <?php echo $achievement['is_earned'] ? 'earned' : 'locked'; ?>">
                    <div class="achievement-icon <?php echo $achievement['is_earned'] ? 'sage' : ''; ?>">
                        <i class="fas <?php echo htmlspecialchars($achievement['icon_path']); ?>"></i>
                        <?php if ($achievement['is_earned']): ?>
                            <span class="achievement-level">✓</span>
                        <?php endif; ?>
                    </div>
                    <div class="achievement-info">
                        <div class="achievement-name"><?php echo htmlspecialchars($achievement['title']); ?></div>
                        <div class="achievement-description"><?php echo htmlspecialchars($achievement['description']); ?></div>
                        <?php if ($achievement['is_earned'] && $achievement['earned_at']): ?>
                            <div class="achievement-earned-date">
                                <i class="fas fa-calendar"></i> 
                                Earned <?php echo date('M j, Y', strtotime($achievement['earned_at'])); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

