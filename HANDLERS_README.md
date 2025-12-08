# UNIFIED HANDLERS DOCUMENTATION

## Overview
This document describes the two powerful controller files that serve as the "brains" of the Webibo game system. These handlers replace all hardcoded logic from individual stage files with database-driven, centralized controllers.

## Architecture

```
User Request → Handler (Controller) → Database + Content Files → View Template → JavaScript
```

**Key Principle:** The handlers output data in a format **identical** to the original hardcoded files, ensuring zero changes needed in JavaScript files.

---

## File Locations

- `/controllers/lecture_handler.php` - Handles all lecture activities
- `/controllers/activity_handler.php` - Handles all quiz/game activities (multiple-choice, fill-blank, code-editor)
- `/core/models/LevelModel.php` - Database queries for levels and content loading

---

## 1. LECTURE HANDLER

**File:** `/controllers/lecture_handler.php`

### Purpose
Serves lecture content dynamically from HTML files based on level ID.

### URL Format
```
/controllers/lecture_handler.php?id=1
/controllers/lecture_handler.php?id=3
/controllers/lecture_handler.php?id=5
```

### Request Flow

#### GET Request Only
1. **Validate User Session**
   - Checks if user is logged in
   - Redirects to login if not authenticated

2. **Get Level ID from URL**
   - Extracts `?id=X` parameter
   - Validates it's a positive integer

3. **Query Database**
   - Fetches level metadata from `levels` table
   - Joins with `courses` table for course info
   - Uses `LevelModel::getLevelById($pdo, $levelId)`

4. **Verify Level Type**
   - Confirms `level_type === 'lecture'`
   - Dies with error if wrong type

5. **Load HTML Content**
   - Constructs path: `/data/html/{id}.html`
   - Reads file contents into `$lectureHtml` variable
   - Uses `LevelModel::readContentFile($levelId, 'lecture')`

6. **Get User Stats**
   - Fetches hearts from `user_stats` table
   - Uses `StatsModel::getStatsByUserId($pdo, $userId)`

7. **Calculate Progress**
   - Based on `order_in_course` field
   - Scaled to 0-10 for progress bar

8. **Determine Next Level**
   - Queries for next level in sequence
   - Uses `LevelModel::getNextLevel($pdo, $levelId)`
   - Sets redirect URL accordingly

9. **Render View**
   - Injects `$lectureHtml` into `window.lectureConfig.stages[0].text`
   - Sets `disableAnimation: true` for static HTML display
   - Outputs complete HTML page with embedded JavaScript

### Variables Available to View

```php
$lectureHtml       // String: HTML content from file
$currentHearts     // Int: User's current hearts (e.g., 10)
$currentProgress   // Float: Progress value 0-10
$level             // Array: Level metadata from database
$redirectUrl       // String: URL to next level or dashboard
```

### JavaScript Configuration Output

```javascript
window.lectureConfig = {
    disableAnimation: true,
    stages: [
        {
            text: `<h1>HTML Basics</h1><p>HTML is...</p>...`
        }
    ]
};
```

### Example Usage

**URL:** `/controllers/lecture_handler.php?id=1`

**Database Query Result:**
```php
$level = [
    'id' => 1,
    'course_id' => 1,
    'level_type' => 'lecture',
    'title' => 'Welcome to HTML!',
    'xp_reward' => 10,
    'order_in_course' => 1,
    'parent_level_id' => NULL
];
```

**Content File:** `/data/html/1.html`
```html
<!DOCTYPE html>
<html>
<head><title>Welcome to HTML</title></head>
<body>
    <h1>👋 Welcome to HTML!</h1>
    <p>Hello! Are you ready to learn HTML?</p>
    ...
</body>
</html>
```

**Output:** Full HTML page with lecture content embedded in `window.lectureConfig`

---

## 2. ACTIVITY HANDLER

**File:** `/controllers/activity_handler.php`

### Purpose
Handles all interactive quiz activities: multiple-choice, fill-in-blank, and code-editor challenges.

### URL Format
```
/controllers/activity_handler.php?id=2  (Multiple Choice)
/controllers/activity_handler.php?id=4  (Fill-Blank)
/controllers/activity_handler.php?id=6  (Code Editor)
```

### Request Types

#### A. GET REQUEST (Load Activity)

1. **Validate User Session**
   - Same as lecture handler

2. **Get Level ID from URL**
   - Extracts `?id=X` parameter

3. **Query Database**
   - Fetches level metadata
   - Verifies `level_type !== 'lecture'`

4. **Load JSON Content**
   - Reads `/data/html/{id}.json`
   - Decodes JSON to PHP array
   - Uses `LevelModel::readContentFile($levelId, $level_type, true)`

5. **Get User Stats**
   - Fetches current hearts from database

6. **Build Activity Data Array**
   - **CRITICAL:** Creates `$activity_data` array matching `window.activityConfig` exactly
   - This is a 1-to-1 mapping to ensure JavaScript compatibility

7. **Extract View Variables**
   - Question/instruction text
   - Options array (for multiple-choice)
   - Enemy data
   - Code templates
   - Feedback messages

8. **Render Appropriate Template**
   - Multiple Choice: Renders option buttons
   - Fill-Blank: Renders code template with input field
   - Code Editor: Renders textarea and output panel

9. **Output JavaScript Config**
   - Injects `$activity_data` as `window.activityConfig` using `json_encode()`

#### B. POST REQUEST (Submit Answer)

1. **Load Content Again**
   - Reads JSON to get correct answer

2. **Extract Submitted Answer**
   - From `$_POST['answer']` (multiple-choice, fill-blank)
   - From `$_POST['code']` (code-editor)

3. **Handle SKIP Action**
   - If `$_POST['skip']` is set:
     - Decrease hearts by 1
     - Check for game over (hearts === 0)
     - Redirect to next level or dashboard
     - Exit early

4. **Validate Answer**
   - **Multiple Choice:** Exact string match
   - **Fill-Blank:** Case-insensitive match
   - **Code Editor:** Uses validation rules from JSON:
     - `ignore_whitespace`: Strip all whitespace
     - `case_sensitive`: Convert to lowercase if false
     - `exact_match`: Compare strings

5. **If CORRECT:**
   - Award XP using `StatsModel::addXP($pdo, $userId, $xpReward)`
   - Mark level complete using `ProgressModel::completeLevel()`
   - Determine next level (lecture vs activity)
   - Redirect to appropriate handler or dashboard
   - Exit

6. **If WRONG:**
   - Decrease hearts by 1
   - Update session: `$_SESSION['hearts']`
   - Check for game over (hearts === 0)
   - If game over: redirect to `/views/gameover.php`
   - Otherwise: reload same page (JavaScript shows feedback)

### Activity Data Array Structure

**This is the CRITICAL part - must match activity.js expectations exactly!**

```php
$activity_data = [
    'type' => 'multiple-choice|fill-blank|code-editor',
    'correctAnswer' => '<h1>',                    // Correct answer string
    'currentHearts' => 10,                        // User's current hearts
    'currentProgress' => 1,                       // Progress 0-10
    'redirectUrl' => 'activity_handler.php?id=4', // Next level URL
    'enemyHP' => 10,                              // Enemy HP (max 10)
    'hasEnemy' => true,                           // Whether enemy exists
    'correctTitle' => 'Awesome!',                 // Feedback title for correct
    'correctDetails' => 'You got it right!',      // Feedback details for correct
    'wrongTitle' => 'Not quite!',                 // Feedback title for wrong
    'wrongDetails' => 'The correct answer is...'  // Feedback details for wrong
];
```

### Variables Available to View Templates

```php
// Activity data for JavaScript
$activity_data     // Array: window.activityConfig structure

// View-specific variables
$level             // Array: Level metadata
$question          // String: Question or instruction text
$options           // Array: Answer options (multiple-choice only)
$enemy             // Array: Enemy data (name, hp, image) or null
$codeTemplate      // String: Code with blanks (fill-blank only)
$starterCode       // String: Pre-filled code (code-editor only)
$hint              // String: Hint text (code-editor only)
$teacher           // Array: Teacher data (code-editor only)
$currentHearts     // Int: User's hearts
$currentProgress   // Float: Progress 0-10
$redirectUrl       // String: Next level URL
```

### Example Usage - Multiple Choice

**URL:** `/controllers/activity_handler.php?id=2`

**Database Query Result:**
```php
$level = [
    'id' => 2,
    'level_type' => 'multiple-choice',
    'title' => 'Which tag is used for the largest heading?',
    'xp_reward' => 25,
    'order_in_course' => 2
];
```

**JSON Content File:** `/data/html/2.json`
```json
{
  "question": "Which tag is used for the largest heading?",
  "options": ["<h1>", "<h3>", "<head>"],
  "correct_answer": "<h1>",
  "enemy": {
    "name": "Fox",
    "hp": 10,
    "image": "assets/img/enemies/fox.png"
  },
  "feedback": {
    "correct": {
      "title": "Correct! 🎉",
      "details": "The <h1> tag defines the most important heading..."
    },
    "wrong": {
      "title": "Not quite! 🤔",
      "details": "The <h1> tag defines the most important heading..."
    }
  }
}
```

**PHP Extraction:**
```php
$question = "Which tag is used for the largest heading?";
$options = ["<h1>", "<h3>", "<head>"];
$enemy = [
    'name' => 'Fox',
    'hp' => 10,
    'image' => 'assets/img/enemies/fox.png'
];

$activity_data = [
    'type' => 'multiple-choice',
    'correctAnswer' => '<h1>',
    'currentHearts' => 10,
    'currentProgress' => 1,
    'redirectUrl' => 'lecture_handler.php?id=3',
    'enemyHP' => 10,
    'hasEnemy' => true,
    'correctTitle' => 'Correct! 🎉',
    'correctDetails' => 'The <h1> tag defines the most important heading...',
    'wrongTitle' => 'Not quite! 🤔',
    'wrongDetails' => 'The <h1> tag defines the most important heading...'
];
```

**HTML Output (simplified):**
```html
<div class="question-section">
    <h1>Which tag is used for the largest heading?</h1>
</div>

<div class="options-section">
    <button data-answer="<h1>" onclick="selectOption(this)">
        <h1>
    </button>
    <button data-answer="<h3>" onclick="selectOption(this)">
        <h3>
    </button>
    <button data-answer="<head>" onclick="selectOption(this)">
        <head>
    </button>
</div>

<div class="enemy-section">
    <img src="assets/img/enemies/fox.png" alt="Fox">
    <div class="enemy-name">Fox</div>
    <div class="enemy-hp-bar">...</div>
</div>

<script>
window.activityConfig = {
    "type": "multiple-choice",
    "correctAnswer": "<h1>",
    "currentHearts": 10,
    "currentProgress": 1,
    "redirectUrl": "lecture_handler.php?id=3",
    "enemyHP": 10,
    "hasEnemy": true,
    "correctTitle": "Correct! 🎉",
    "correctDetails": "The <h1> tag defines...",
    "wrongTitle": "Not quite! 🤔",
    "wrongDetails": "The <h1> tag defines..."
};
</script>
```

### Answer Validation Logic

#### Multiple Choice
```php
$isCorrect = ($submittedAnswer === $correctAnswer);
```

#### Fill-in-Blank
```php
$isCorrect = (strtolower($submittedAnswer) === strtolower($correctAnswer));
```

#### Code Editor
```php
$userCode = $submittedAnswer;
$expectedCode = $correctAnswer;

if ($validation['ignore_whitespace']) {
    $userCode = preg_replace('/\s+/', '', $userCode);
    $expectedCode = preg_replace('/\s+/', '', $expectedCode);
}

if (!$validation['case_sensitive']) {
    $userCode = strtolower($userCode);
    $expectedCode = strtolower($expectedCode);
}

$isCorrect = ($userCode === $expectedCode);
```

---

## 3. LEVELMODEL (Helper Model)

**File:** `/core/models/LevelModel.php`

### Purpose
Centralized database queries and file operations for levels.

### Key Methods

#### `getLevelById($pdo, $levelId)`
```php
// Returns level data with course information
$level = LevelModel::getLevelById($pdo, 1);
// Result:
[
    'id' => 1,
    'course_id' => 1,
    'level_type' => 'lecture',
    'title' => 'Welcome to HTML!',
    'xp_reward' => 10,
    'order_in_course' => 1,
    'parent_level_id' => NULL,
    'course_title' => 'HTML Basics',
    'course_description' => 'Learn the fundamentals...'
]
```

#### `getLevelsByCourse($pdo, $courseId)`
```php
// Returns all levels for a course, ordered by sequence
$levels = LevelModel::getLevelsByCourse($pdo, 1);
```

#### `validateLevelType($pdo, $levelId, $expectedType)`
```php
// Check if level exists and matches expected type
$isValid = LevelModel::validateLevelType($pdo, 2, 'multiple-choice');
// Returns: true/false
```

#### `getNextLevel($pdo, $currentLevelId)`
```php
// Get next level in course sequence
$nextLevel = LevelModel::getNextLevel($pdo, 2);
// Returns level array or false if none
```

#### `getContentFilePath($levelId, $levelType)`
```php
// Build content file path
$path = LevelModel::getContentFilePath(1, 'lecture');
// Returns: "/path/to/project/data/html/1.html"

$path = LevelModel::getContentFilePath(2, 'multiple-choice');
// Returns: "/path/to/project/data/html/2.json"
```

#### `readContentFile($levelId, $levelType, $decode = false)`
```php
// Read lecture HTML
$html = LevelModel::readContentFile(1, 'lecture');
// Returns: "<html>...</html>"

// Read and decode activity JSON
$data = LevelModel::readContentFile(2, 'multiple-choice', true);
// Returns: ['question' => '...', 'options' => [...], ...]
```

---

## 4. UPDATED STATSMODEL METHODS

### New Method: `addXP($pdo, $userId, $xpAmount)`

```php
/**
 * Add XP to user's total
 * Increments xp_points in user_stats table
 */
StatsModel::addXP($pdo, 123, 25);  // Award 25 XP to user 123
```

**SQL Executed:**
```sql
UPDATE user_stats 
SET xp_points = xp_points + 25 
WHERE user_id = 123;
```

---

## Integration with Existing JavaScript

### activity.js Compatibility

The `window.activityConfig` object created by `activity_handler.php` matches the exact structure expected by `activity.js`:

**JavaScript Expects:**
```javascript
const config = window.activityConfig || {};
const activityType = config.type;               // ✓ Provided
const correctAnswer = config.correctAnswer;     // ✓ Provided
let currentHearts = config.currentHearts;       // ✓ Provided
let currentProgress = config.currentProgress;   // ✓ Provided
let enemyHP = config.enemyHP;                   // ✓ Provided
const redirectUrl = config.redirectUrl;         // ✓ Provided
```

**PHP Provides (via json_encode):**
```javascript
window.activityConfig = {
    type: "multiple-choice",
    correctAnswer: "<h1>",
    currentHearts: 10,
    currentProgress: 1,
    redirectUrl: "lecture_handler.php?id=3",
    enemyHP: 10,
    hasEnemy: true,
    correctTitle: "Correct! 🎉",
    correctDetails: "...",
    wrongTitle: "Not quite! 🤔",
    wrongDetails: "..."
};
```

**Result:** ✅ Zero JavaScript changes needed!

### lecture.js Compatibility

The `window.lectureConfig` object matches lecture.js expectations:

**JavaScript Expects:**
```javascript
const config = window.lectureConfig || {};
const stages = config.stages || defaultStages;  // ✓ Provided
const disableAnimation = config.disableAnimation; // ✓ Provided
```

**PHP Provides:**
```javascript
window.lectureConfig = {
    disableAnimation: true,
    stages: [
        {
            text: `<h1>HTML Basics</h1><p>...</p>`
        }
    ]
};
```

**Result:** ✅ lecture.js works unchanged!

---

## Security Features

1. **Session Validation**
   - All handlers check `$_SESSION['user']` before proceeding
   - Redirect to login if not authenticated

2. **Input Validation**
   - Level ID cast to integer: `(int)$_GET['id']`
   - Checks for positive values: `$levelId > 0`

3. **Database Type Verification**
   - Lecture handler verifies `level_type === 'lecture'`
   - Activity handler verifies `level_type !== 'lecture'`

4. **File Existence Checks**
   - `file_exists()` before reading content
   - Error logging if file not found

5. **XSS Protection**
   - `htmlspecialchars()` on all user-facing output
   - `json_encode()` with `JSON_HEX_TAG | JSON_HEX_QUOT` for JavaScript

6. **SQL Injection Prevention**
   - All queries use PDO prepared statements
   - Parameters bound via `:placeholder` syntax

---

## Error Handling

### Lecture Handler Errors

```php
// Invalid level ID
if ($levelId <= 0) {
    die("Invalid level ID");
}

// Level not found
if (!$level) {
    die("Level not found");
}

// Wrong level type
if ($level['level_type'] !== 'lecture') {
    die("Invalid level type. Expected 'lecture', got '{$level['level_type']}'");
}

// Content file missing
if ($lectureHtml === false) {
    die("Failed to load lecture content for level {$levelId}");
}
```

### Activity Handler Errors

```php
// Same validation as lecture handler, plus:

// Activity-specific validation
if ($level['level_type'] === 'lecture') {
    die("Invalid level type. This handler is for activities only.");
}

// JSON decode errors
if (!$activityContent) {
    die("Failed to load activity content");
}
```

### LevelModel Error Logging

All database errors are logged via `error_log()`:

```php
catch (PDOException $e) {
    error_log("LevelModel::getLevelById() - Error: " . $e->getMessage());
    return false;
}
```

---

## Testing Checklist

### Lecture Handler
- [ ] Load lecture with valid ID (`?id=1`)
- [ ] Load lecture with invalid ID (`?id=999`)
- [ ] Load lecture with non-lecture type (`?id=2`)
- [ ] Verify HTML content displays correctly
- [ ] Check hearts count from database
- [ ] Check progress bar percentage
- [ ] Test SKIP button redirect
- [ ] Test FINISH button redirect
- [ ] Verify next level URL calculation
- [ ] Test exit modal functionality

### Activity Handler (GET)
- [ ] Load multiple-choice activity (`?id=2`)
- [ ] Load fill-blank activity (`?id=4`)
- [ ] Load code-editor activity (`?id=6`)
- [ ] Verify `window.activityConfig` structure
- [ ] Check question/instruction displays
- [ ] Verify options render correctly (multiple-choice)
- [ ] Verify code template renders (fill-blank)
- [ ] Verify textarea renders (code-editor)
- [ ] Check enemy image and name display
- [ ] Verify progress bar and hearts

### Activity Handler (POST)
- [ ] Submit correct answer (multiple-choice)
- [ ] Submit wrong answer (multiple-choice)
- [ ] Submit correct answer (fill-blank)
- [ ] Submit wrong answer (fill-blank)
- [ ] Submit correct code (code-editor)
- [ ] Submit wrong code (code-editor)
- [ ] Test SKIP button functionality
- [ ] Verify XP awarded on correct answer
- [ ] Verify hearts decrease on wrong answer
- [ ] Test game over redirect (hearts = 0)
- [ ] Verify level completion in database
- [ ] Check redirect to next level
- [ ] Check redirect to dashboard (last level)

### Database Operations
- [ ] LevelModel::getLevelById() returns correct data
- [ ] LevelModel::getNextLevel() finds correct next level
- [ ] LevelModel::readContentFile() reads HTML correctly
- [ ] LevelModel::readContentFile() decodes JSON correctly
- [ ] StatsModel::addXP() increments XP in database
- [ ] ProgressModel::completeLevel() marks level complete

---

## Migration from Old Stage Files

### Before (Hardcoded)
```php
// stage-002-mc.php (165 lines of mixed logic)
<?php
$correct_answer = '<h1>';  // ← Hardcoded
$hearts = $_SESSION['hearts'] ?? 10;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($selected === $correct_answer) {
        // Hardcoded redirect
        header("Location: stage-001-act.php");
    }
}
?>
<script>
window.activityConfig = {
    type: 'multiple-choice',
    correctAnswer: '<?php echo $correct_answer; ?>',  // ← Hardcoded
    // ... more hardcoded values
};
</script>
```

### After (Database-Driven)
```php
// activity_handler.php (Dynamic for ALL activities)
<?php
$level = LevelModel::getLevelById($pdo, $levelId);  // ← From database
$activityContent = LevelModel::readContentFile($levelId, $level['level_type'], true);  // ← From JSON

$activity_data = [
    'type' => $level['level_type'],  // ← Dynamic
    'correctAnswer' => $activityContent['correct_answer'],  // ← From JSON
    // ... all values from database/JSON
];
?>
<script>
window.activityConfig = <?php echo json_encode($activity_data); ?>;  // ← Dynamic
</script>
```

### Benefits
✅ One controller handles ALL activities (not 7 separate files)  
✅ Content changes don't require code edits  
✅ Easy to add new levels (just add SQL + JSON file)  
✅ Consistent logic across all activities  
✅ Easier testing and debugging  
✅ True MVC separation  

---

## Future Enhancements

1. **Admin Panel Integration**
   - Edit content files via web interface
   - Update level metadata without SQL

2. **Caching Layer**
   - Cache JSON content in memory
   - Reduce file I/O on high traffic

3. **Analytics**
   - Track which questions users fail most
   - Measure average completion time per level

4. **A/B Testing**
   - Serve different content files to different users
   - Test question effectiveness

5. **Multilingual Support**
   - Store content files per language
   - `/data/html/en/2.json`, `/data/html/es/2.json`

---

## Conclusion

The unified handlers represent a complete separation of concerns:
- **Database:** Stores level metadata and user progress
- **Files:** Store static content (HTML lectures, JSON activities)
- **Controllers:** Load data and serve to views
- **Views:** Display data using templates
- **JavaScript:** Handles interactivity (unchanged from original)

This architecture allows for:
- ✅ Rapid content updates without code changes
- ✅ Scalable level creation workflow
- ✅ Consistent behavior across all activities
- ✅ Easy testing and debugging
- ✅ Future-proof for admin panels and CMS integration

**Critical Success Factor:** The `window.activityConfig` and `window.lectureConfig` objects match the original hardcoded format **exactly**, ensuring zero JavaScript changes required!
