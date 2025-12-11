# Dynamic Content Migration - Implementation Guide

## 📋 Overview

This document outlines the complete implementation strategy for transforming Webibo from a static 10-node system to a fully dynamic, database-driven learning platform with 44 levels across 3 courses.

## 🎯 Phase 1: Data Migration (COMPLETED)

### ✅ SQL Migration Script
**File**: `data_migration_full.sql`

**What it does**:
- Clears existing data (user_progress → levels → courses)
- Inserts 3 courses: HTML, CSS, JavaScript
- Inserts 44 levels with proper:
  - `course_id` (1=HTML, 2=CSS, 3=JS)
  - `level_type` (lecture, multiple-choice, fill-blank, code-editor)
  - `title` (extracted from curriculum document)
  - `xp_reward` (10 for lectures, 25 for quizzes, 30 for coding)
  - `order_in_course` (1-20 for HTML, 1-12 for CSS/JS)
  - `parent_level_id` (creates linear progression)

**Breakdown**:
- **HTML Course** (ID: 1): 20 levels
  - 5 Modules × 4 levels each (Lecture → Quiz → Practice → Challenge)
- **CSS Course** (ID: 2): 12 levels  
  - 3 Modules × 4 levels each
- **JavaScript Course** (ID: 3): 12 levels
  - 3 Modules × 4 levels each

**To Execute**:
```bash
mysql -u root < data_migration_full.sql
```

---

## 📁 Phase 2: Content Files Generation

### Content Structure

All content files live in `/data/html/` directory:
- **Lectures**: `{level_id}.html` (e.g., `1.html`, `5.html`)
- **Activities**: `{level_id}.json` (e.g., `2.json`, `3.json`)

### File Naming Convention

```
Level 1 (Lecture)         → 1.html
Level 2 (Multiple Choice) → 2.json
Level 3 (Fill Blank)      → 3.json
Level 4 (Code Editor)     → 4.json
Level 5 (Lecture)         → 5.html
...and so on through Level 44
```

### Content File Formats

#### Lecture HTML Format (`{id}.html`)

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Level {id} - Lecture</title>
    <link rel="stylesheet" href="../assets/css/lecture-content.css">
</head>
<body>
    <!-- PAGE 1 -->
    <div class="page-break"></div>
    <div class="page-header">
        <div class="page-number">Page 1/4</div>
        <h1>Welcome to HTML!</h1>
    </div>

    <div class="wiza-intro">
        <strong>Wiza:</strong> "Hello, aspiring web developer! 👋..."
    </div>

    <p>Content paragraph...</p>

    <div class="code-box">
        <pre><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
  &lt;head&gt;
    &lt;!-- Behind-the-scenes info --&gt;
  &lt;/head&gt;
  &lt;body&gt;
    &lt;!-- Visible content --&gt;
  &lt;/body&gt;
&lt;/html&gt;</code></pre>
    </div>

    <ul>
        <li>HTML is NOT a programming language</li>
        <li>It's a markup language for structuring content</li>
    </ul>

    <!-- PAGE 2 -->
    <div class="page-break"></div>
    <div class="page-header">
        <div class="page-number">Page 2/4</div>
        <h1>The HTML Document Structure</h1>
    </div>
    <!-- ... more pages -->
</body>
</html>
```

**Key Elements**:
- `<div class="page-break">` - Separates pages (lecture.php splits by this)
- `<div class="page-header">` - Contains page number and title
- `<div class="wiza-intro">` - Wiza's dialogue boxes
- `<div class="code-box">` - Code examples
- Standard HTML tags: `<p>`, `<h2>`, `<ul>`, `<li>`

#### Multiple Choice JSON Format (`{id}.json`)

```json
{
  "questions": [
    {
      "question": "What does HTML stand for?",
      "options": [
        "Hyper Transfer Markup Language",
        "High Tech Modern Language",
        "Hyper Tool Markup Language",
        "Hyper Text Markup Language"
      ],
      "correct_answer": "Hyper Text Markup Language",
      "feedback": {
        "correct": {
          "title": "Correct! 🎉",
          "details": "HTML stands for Hyper Text Markup Language. It's called 'Hyper Text' because it can contain links to other documents."
        },
        "wrong": {
          "title": "Not quite! 🤔",
          "details": "HTML stands for Hyper Text Markup Language. It's called 'Hyper Text' because it can contain links to other documents."
        }
      }
    },
    {
      "question": "Which tag contains ALL other HTML elements?",
      "options": ["<body>", "<container>", "<html>", "<main>"],
      "correct_answer": "<html>",
      "feedback": {
        "correct": {
          "title": "Excellent! 🎯",
          "details": "The <html> tag is the root element that wraps EVERYTHING else in an HTML document."
        },
        "wrong": {
          "title": "Try again!",
          "details": "The <html> tag is the root element that wraps EVERYTHING else in an HTML document."
        }
      }
    }
  ],
  "enemy": {
    "name": "Fox",
    "hp": 10,
    "image": "assets/img/enemies/fox-happy.png"
  }
}
```

**Key Fields**:
- `questions`: Array of question objects
- Each question has: `question`, `options` (array), `correct_answer`, `feedback`
- `enemy`: Optional enemy with `name`, `hp`, and `image`

#### Fill-in-the-Blank JSON Format (`{id}.json`)

```json
{
  "questions": [
    {
      "question": "Complete the document declaration",
      "code_template": "<!______ html>",
      "correct_answer": "DOCTYPE",
      "feedback": {
        "correct": {
          "title": "Perfect! ✨",
          "details": "<!DOCTYPE html> is the document type declaration."
        },
        "wrong": {
          "title": "Not quite!",
          "details": "The correct answer is DOCTYPE."
        }
      }
    }
  ],
  "enemy": {
    "name": "Bear",
    "hp": 10,
    "image": "assets/img/enemies/bear-happy.png"
  }
}
```

**Key Fields**:
- `code_template`: String with `___` where blank should be
- `correct_answer`: The text that fills the blank
- Rest same as multiple choice

#### Code Editor JSON Format (`{id}.json`)

```json
{
  "instruction": "Create an <h1> heading that says: 'Hello World!'",
  "correct_code": "<h1>Hello World!</h1>",
  "starter_code": "",
  "validation": {
    "ignore_whitespace": true,
    "case_sensitive": false
  },
  "feedback": {
    "correct": {
      "title": "Perfect! 🎉",
      "details": "Your code is correct!"
    },
    "wrong": {
      "title": "Try Again",
      "details": "Check your syntax and try again."
    }
  }
}
```

**Key Fields**:
- `instruction`: What the user needs to code
- `correct_code`: The expected solution
- `starter_code`: Pre-filled code (optional)
- `validation`: How to compare user code vs correct code

---

## 🔄 Phase 3: Content Generation Methods

### Method 1: Manual Creation (RECOMMENDED)
**Best for**: Ensuring quality, maintaining Wiza's personality

**Process**:
1. Open `WEBIBO STAGES.txt`
2. Find level section (use line numbers from `data_migration_full.sql` comments)
3. Copy content
4. Format into appropriate HTML/JSON structure
5. Save as `{level_id}.html` or `{level_id}.json`

**Pros**: Complete control, perfect formatting
**Cons**: Time-consuming (44 files)

### Method 2: Semi-Automated Script
**Best for**: Batch processing with review

**Process**:
1. Use `scripts/generate_content_files.php` as template
2. Run script to generate drafts
3. Manually review and fix each file
4. Test in browser

**Pros**: Faster initial generation
**Cons**: Requires cleanup, may miss formatting nuances

### Method 3: Hybrid Approach (RECOMMENDED FOR THIS PROJECT)
**Best for**: Balancing speed and quality

**Process**:
1. Manually create Levels 1-4 (set the standard)
2. Use those as templates for similar levels
3. Copy/paste/modify for each module
4. Review all files before commit

**Estimated Time**:
- Lectures (11 files): ~15 min each = 2.5 hours
- Quizzes (11 files): ~10 min each = 2 hours  
- Fill-blanks (11 files): ~10 min each = 2 hours
- Code challenges (11 files): ~5 min each = 1 hour
**Total**: ~7-8 hours of focused work

---

## 🗄️ Phase 4: Database Population

### Execute Migration

```bash
# From project root
cd c:\xampp\htdocs\Webibo

# Run the migration
mysql -u root -p < data_migration_full.sql

# Verify
mysql -u root -p webibo -e "SELECT COUNT(*) FROM levels;"
# Should show: 44

mysql -u root -p webibo -e "SELECT course_id, COUNT(*) FROM levels GROUP BY course_id;"
# Should show:
# 1 | 20  (HTML)
# 2 | 12  (CSS)
# 3 | 12  (JS)
```

### Verify Foreign Keys

```sql
-- Check parent relationships
SELECT 
    l1.id, 
    l1.title, 
    l1.parent_level_id, 
    l2.title AS parent_title
FROM levels l1
LEFT JOIN levels l2 ON l1.parent_level_id = l2.id
ORDER BY l1.id;
```

---

## 🎨 Phase 5: Frontend Integration

### Current State
- Dashboard has hardcoded 10 nodes
- `dashboard.js` uses static positions
- No course switching

### Required Changes

#### 1. Update `controllers/dashboard.php`

**Current**:
```php
// Hardcoded sample data
$userProgress = [
    ['level_id' => 1, 'type' => 'lecture', 'position' => [...]]
];
```

**New**:
```php
// Fetch ALL levels for current course
$currentCourseId = $_SESSION['current_course_id'] ?? 1; // Default to HTML

$sql = "SELECT 
            l.id AS level_id,
            l.level_type AS type,
            l.title,
            l.order_in_course,
            CASE WHEN up.completed_at IS NOT NULL THEN 'completed'
                 WHEN l.order_in_course = 1 OR parent_up.completed_at IS NOT NULL THEN 'unlocked'
                 ELSE 'locked' END AS status
        FROM levels l
        LEFT JOIN user_progress up ON l.id = up.level_id AND up.user_id = :user_id
        LEFT JOIN levels parent_l ON l.parent_level_id = parent_l.id
        LEFT JOIN user_progress parent_up ON parent_l.id = parent_up.level_id AND parent_up.user_id = :user_id
        WHERE l.course_id = :course_id
        ORDER BY l.order_in_course";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'user_id' => $userId,
    'course_id' => $currentCourseId
]);
$userProgress = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate positions dynamically
foreach ($userProgress as &$level) {
    $order = $level['order_in_course'];
    $level['position'] = calculateNodePosition($order, count($userProgress));
}
```

#### 2. Add Position Calculator

```php
/**
 * Calculate node position on roadmap
 * Creates a winding path pattern
 */
function calculateNodePosition($order, $total) {
    $baseX = 50;
    $baseY = 100;
    $horizontalSpacing = 150;
    $verticalSpacing = 120;
    $nodesPerRow = 5;
    
    $row = floor(($order - 1) / $nodesPerRow);
    $col = ($order - 1) % $nodesPerRow;
    
    // Alternate rows go left-to-right and right-to-left
    if ($row % 2 == 1) {
        $col = ($nodesPerRow - 1) - $col;
    }
    
    return [
        'left' => $baseX + ($col * $horizontalSpacing),
        'top' => $baseY + ($row * $verticalSpacing)
    ];
}
```

#### 3. Update `dashboard.js` for Dynamic Rendering

**Current**: Hardcoded SVG path
**New**: Generate path from node positions

```javascript
// Generate trail path dynamically
function generateTrailPath() {
    const nodes = document.querySelectorAll('.level-node');
    if (nodes.length === 0) return '';
    
    let path = '';
    nodes.forEach((node, index) => {
        const rect = node.getBoundingClientRect();
        const container = document.querySelector('.roadmap').getBoundingClientRect();
        
        const x = rect.left - container.left + (rect.width / 2);
        const y = rect.top - container.top + (rect.height / 2);
        
        if (index === 0) {
            path += `M ${x} ${y}`;
        } else {
            path += ` L ${x} ${y}`;
        }
    });
    
    return path;
}

// Apply path
const svgPath = document.querySelector('.trail-path');
svgPath.setAttribute('d', generateTrailPath());
```

#### 4. Add Course Switcher

Update `views/dashboard.php`:

```php
<!-- Course Module Card -->
<div class="course-module-wrapper">
    <button class="course-module-btn">
        <div class="course-icon-section">
            <i class="fab fa-<?php echo getCourseIcon($currentCourseId); ?>"></i>
        </div>
        <div class="course-text-section">
            <div class="course-label">Module <?php echo $currentCourseId; ?></div>
            <div class="course-title"><?php echo getCourseName($currentCourseId); ?></div>
        </div>
    </button>
    <div class="course-popup">
        <div class="popup-header">SELECT COURSE</div>
        <div class="course-icons-list">
            <?php foreach ($allCourses as $course): ?>
            <a href="?switch_course=<?php echo $course['id']; ?>" class="course-icon-item">
                <div class="course-icon-circle <?php echo strtolower($course['title']); ?>-icon">
                    <i class="fab fa-<?php echo getCourseIcon($course['id']); ?>"></i>
                </div>
                <div class="course-icon-label"><?php echo $course['title']; ?></div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
```

Add course switching logic:

```php
// Handle course switch
if (isset($_GET['switch_course'])) {
    $newCourseId = (int)$_GET['switch_course'];
    if ($newCourseId >= 1 && $newCourseId <= 3) {
        $_SESSION['current_course_id'] = $newCourseId;
        header('Location: dashboard.php');
        exit;
    }
}
```

---

## 🧪 Phase 6: Testing Checklist

### Database Testing
- [ ] All 44 levels inserted
- [ ] Parent relationships correct
- [ ] XP rewards assigned properly
- [ ] Course IDs match (1=HTML, 2=CSS, 3=JS)

### Content Files Testing
- [ ] All HTML lectures render correctly
- [ ] Code examples display with proper formatting
- [ ] Wiza dialogues styled correctly
- [ ] All JSON files valid (use JSONLint)
- [ ] Questions display properly
- [ ] Correct answers validated
- [ ] Feedback shows on right/wrong answers

### Frontend Testing
- [ ] Dashboard loads dynamic levels
- [ ] Nodes positioned correctly
- [ ] Trail path connects all nodes
- [ ] First level unlocked by default
- [ ] Subsequent levels locked until parent completed
- [ ] Course switcher works
- [ ] Click level → loads correct lecture/activity
- [ ] Complete level → next level unlocks
- [ ] XP awarded correctly

### User Flow Testing
1. New user registers
2. Sees HTML Course Level 1 unlocked
3. Completes Level 1 (lecture)
4. Level 2 unlocks
5. Completes entire HTML module
6. Switches to CSS course
7. Sees CSS Level 1 unlocked

---

## 📊 Progress Tracking

### Current Status

✅ **COMPLETED**:
- SQL migration script created (`data_migration_full.sql`)
- All 44 levels defined with proper metadata
- Course structure designed (3 courses, proper IDs)
- Parent-child relationships established
- XP rewards assigned

🔄 **IN PROGRESS**:
- Content file generation strategy documented
- Sample files to be created

⏳ **PENDING**:
- Generate all 44 content files (HTML/JSON)
- Update dashboard controller for dynamic data
- Implement position calculator
- Add course switcher
- Test complete user flow

### Estimated Completion Time

| Task | Estimated Time | Status |
|------|---------------|---------|
| SQL Migration | 2 hours | ✅ Done |
| Content Strategy | 1 hour | ✅ Done |
| Sample Content (4 files) | 1 hour | 🔄 Next |
| Remaining Content (40 files) | 6 hours | ⏳ Pending |
| Dashboard Updates | 2 hours | ⏳ Pending |
| Testing & Fixes | 2 hours | ⏳ Pending |
| **TOTAL** | **14 hours** | **20% Complete** |

---

## 🚀 Next Steps (Priority Order)

1. **Run the SQL migration** to populate database
   ```bash
   mysql -u root -p < data_migration_full.sql
   ```

2. **Create sample content files** (Levels 1-4)
   - Manually create 1.html, 2.json, 3.json, 4.json
   - Use these as templates

3. **Update dashboard controller** for dynamic data
   - Fetch levels from database
   - Calculate positions
   - Implement locking logic

4. **Test with sample content**
   - Verify Level 1 loads correctly
   - Complete Level 1, check Level 2 unlocks
   - Test quiz/practice/challenge flows

5. **Generate remaining content** (Levels 5-44)
   - Use templates from Levels 1-4
   - Batch create by module

6. **Add course switcher**
   - Session-based current course
   - Switch between HTML/CSS/JS

7. **Full integration testing**
   - Test all 44 levels
   - Verify progression logic
   - Check XP calculations

---

## 📝 Notes

- **Backward Compatibility**: Old content files (1.html, 2.json, etc.) will be overwritten. Backup if needed.
- **Position System**: Can be refined later. Start with simple grid, enhance to curved paths.
- **Enemy Images**: Ensure images exist in `assets/img/enemies/` directory.
- **Wiza Personality**: Maintain consistent voice across all content.
- **Testing User**: Create test account to verify progression without affecting real data.

---

## 💡 Tips for Content Creation

1. **Use Find & Replace**: When creating similar levels, use templates heavily
2. **Test Incrementally**: Don't create all 44 at once. Test in batches of 4-5
3. **Validate JSON**: Always run JSON files through validator before saving
4. **Escape HTML**: In JSON strings, escape HTML properly: `<` → `&lt;`
5. **Code Formatting**: Use `<pre><code>` blocks for code examples
6. **Wiza Voice**: Keep enthusiastic, encouraging, uses emojis (👋, 🎉, 💪)

---

**Status**: Ready for implementation  
**Last Updated**: December 11, 2025  
**Next Action**: Execute SQL migration and create sample content files
