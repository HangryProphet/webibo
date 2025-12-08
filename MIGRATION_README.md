# THE GREAT MIGRATION - README

## Overview
This migration extracts all hardcoded game content from PHP/JS files into structured SQL and data files, enabling database-driven content management.

## Files Created

### 1. SQL Migration File
**File:** `data_migration.sql`
- Course INSERT: "HTML Basics" course (ID: 1)
- Levels INSERT: 7 levels with proper relationships
  - Level 1: Lecture - Welcome to HTML (10 XP)
  - Level 2: Multiple Choice - H1 Tag Quiz (25 XP)
  - Level 3: Lecture - HTML Basics Reference (10 XP)
  - Level 4: Fill-Blank - Media Query (25 XP)
  - Level 5: Lecture - Best Practices (10 XP)
  - Level 6: Code Editor - First Heading (30 XP)
  - Level 7: Lecture - Congratulations (10 XP)

### 2. Content Files in `/data/html/`

#### Lecture Files (HTML):
- **1.html** - Welcome to HTML introduction (5-slide content)
- **3.html** - HTML Basics reference with code examples
- **5.html** - HTML Best Practices guide
- **7.html** - Congratulations celebration page

#### Activity Files (JSON):
- **2.json** - Multiple choice quiz (Which tag for largest heading?)
  ```json
  {
    "question": "...",
    "options": ["<h1>", "<h3>", "<head>"],
    "correct_answer": "<h1>",
    "enemy": { "name": "Fox", "hp": 10 },
    "feedback": { "correct": {...}, "wrong": {...} }
  }
  ```

- **4.json** - Fill-in-blank quiz (@media max-width)
  ```json
  {
    "question": "...",
    "code_template": "@media (max-width: ___ px) {...}",
    "correct_answer": "600",
    "enemy": { "name": "Bear", "hp": 10 },
    "feedback": { "correct": {...}, "wrong": {...} }
  }
  ```

- **6.json** - Code editor activity (Write first heading)
  ```json
  {
    "instruction": "Write your first HTML heading",
    "correct_code": "<h1>Hello World!</h1>",
    "enemy": null,
    "teacher": { "name": "Wiza" },
    "feedback": { "correct": {...}, "wrong": {...} },
    "validation": { "type": "exact_match", "case_sensitive": false }
  }
  ```

## JSON Schema Documentation

### Multiple Choice Activities
```json
{
  "question": "string",          // Question text
  "options": ["string"],         // Array of answer choices
  "correct_answer": "string",    // The correct answer
  "enemy": {                     // Enemy configuration
    "name": "string",
    "hp": number,
    "image": "string"            // Path to enemy image
  },
  "feedback": {
    "correct": {
      "title": "string",         // Success message title
      "details": "string"        // Detailed explanation (can include HTML)
    },
    "wrong": {
      "title": "string",         // Error message title
      "details": "string"        // Helpful explanation
    }
  }
}
```

### Fill-in-Blank Activities
```json
{
  "question": "string",
  "code_template": "string",     // Code with ___ for blank
  "blank_position": number,      // Which blank if multiple (1-indexed)
  "correct_answer": "string",
  "enemy": { ... },              // Same as multiple choice
  "feedback": { ... }            // Same as multiple choice
}
```

### Code Editor Activities
```json
{
  "instruction": "string",       // What to code
  "starter_code": "string",      // Pre-filled code (optional)
  "correct_code": "string",      // Expected solution
  "hint": "string",              // Helpful hint (can include HTML)
  "enemy": null,                 // Usually no enemy for coding challenges
  "teacher": {
    "name": "string",
    "image": "string"            // Path to teacher image
  },
  "feedback": {
    "correct": { ... },
    "wrong": { ... }
  },
  "validation": {
    "type": "exact_match|contains|regex",
    "case_sensitive": boolean,
    "ignore_whitespace": boolean
  }
}
```

## Migration Steps

### Step 1: Run SQL Migration
```bash
# Import the SQL file into your database
mysql -u root webibo < data_migration.sql

# Or through phpMyAdmin:
# 1. Open phpMyAdmin
# 2. Select 'webibo' database
# 3. Click Import tab
# 4. Choose data_migration.sql
# 5. Click Go
```

### Step 2: Verify Database
```sql
-- Check courses
SELECT * FROM courses;

-- Check levels with course relationship
SELECT l.id, l.order_in_course, l.level_type, l.title, l.xp_reward, c.title as course_title
FROM levels l
JOIN courses c ON l.course_id = c.id
ORDER BY l.order_in_course;
```

### Step 3: Verify Content Files
All content files are already created in `/data/html/`:
- 4 HTML files for lectures (1.html, 3.html, 5.html, 7.html)
- 3 JSON files for activities (2.json, 4.json, 6.json)

### Step 4: Create Activity Controller
You'll need to create a unified controller that:
1. Receives level_id from URL/POST
2. Queries levels table to get level_type
3. Based on level_type:
   - If 'lecture': Load corresponding .html file
   - If 'multiple-choice'/'fill-blank'/'code-editor': Load corresponding .json file
4. Renders appropriate view template
5. Processes user answers
6. Updates user_progress table on completion

### Step 5: Refactor Stage Views
Current stage files (`views/html/stage-*.php`) should become generic templates:
- `lecture_template.php` - For all lecture types
- `multiple_choice_template.php` - For quiz activities
- `fill_blank_template.php` - For fill-blank activities
- `code_editor_template.php` - For coding challenges

These templates receive data from the controller instead of hardcoding it.

## XP Rewards Summary
- Lectures: 10 XP each (Levels 1, 3, 5, 7)
- Multiple Choice: 25 XP (Level 2)
- Fill-in-Blank: 25 XP (Level 4)
- Code Editor: 30 XP (Level 6)
- **Total Course XP: 135 XP**

## Enemy Roster
- **Fox** (HP: 10) - Appears in Level 2 (Multiple Choice)
- **Bear** (HP: 10) - Appears in Level 4 (Fill-Blank)
- **No Enemy** - Levels 1, 3, 5, 6, 7 (Wiza teaches coding in Level 6)

## Next Steps (Not Yet Implemented)
1. Create `/controllers/activity.php` - Unified activity controller
2. Refactor stage views into generic templates
3. Update dashboard to fetch levels from database
4. Create admin panel for content management (future enhancement)
5. Delete old stage-*.php files after migration complete

## Notes
- All content files use UTF-8 encoding
- HTML files include inline CSS for standalone rendering
- JSON files use double quotes per JSON spec
- Feedback messages support HTML tags (wrap in <code> for code snippets)
- File naming: Content files match their level ID (1.html = Level 1, 2.json = Level 2)
- Parent-child relationships ensure linear progression (can't skip levels)

## Testing Checklist
- [ ] Import SQL successfully
- [ ] Verify 1 course and 7 levels in database
- [ ] Confirm all parent_level_id relationships correct
- [ ] Check XP rewards sum to 135
- [ ] Validate all JSON files parse correctly
- [ ] Ensure HTML files render properly in browser
- [ ] Test level progression logic with parent_level_id constraints
