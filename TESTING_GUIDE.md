# TESTING THE UNIFIED HANDLERS

## Prerequisites

1. **Database Migration Complete**
   ```bash
   # Import the migration SQL
   mysql -u root webibo < data_migration.sql
   ```

2. **Verify Database**
   ```sql
   USE webibo;
   SELECT * FROM courses;
   SELECT * FROM levels ORDER BY order_in_course;
   ```

3. **Verify Content Files Exist**
   ```
   data/html/1.html  ✓
   data/html/2.json  ✓
   data/html/3.html  ✓
   data/html/4.json  ✓
   data/html/5.html  ✓
   data/html/6.json  ✓
   data/html/7.html  ✓
   ```

4. **User Account**
   - Must have verified account
   - Must be logged in

---

## Quick Test URLs

### Lectures
- http://localhost/Webibo/controllers/lecture_handler.php?id=1
- http://localhost/Webibo/controllers/lecture_handler.php?id=3
- http://localhost/Webibo/controllers/lecture_handler.php?id=5
- http://localhost/Webibo/controllers/lecture_handler.php?id=7

### Activities
- http://localhost/Webibo/controllers/activity_handler.php?id=2 (Multiple Choice)
- http://localhost/Webibo/controllers/activity_handler.php?id=4 (Fill-Blank)
- http://localhost/Webibo/controllers/activity_handler.php?id=6 (Code Editor)

---

## Test Scenarios

### Test 1: Lecture Display
**URL:** `lecture_handler.php?id=1`

**Expected Results:**
- ✓ Page loads without errors
- ✓ Hearts count displays (from database)
- ✓ Progress bar shows ~14% (1/7 levels)
- ✓ HTML content displays in Wiza's speech bubble
- ✓ "SKIP LECTURE" button present
- ✓ "FINISH" button present
- ✓ No typewriter animation (static display)

**Browser Console Check:**
```javascript
// Open DevTools Console and run:
console.log(window.lectureConfig);

// Expected output:
{
    disableAnimation: true,
    stages: [
        {
            text: "<html>...</html>"  // Full HTML content
        }
    ]
}
```

---

### Test 2: Multiple Choice Activity
**URL:** `activity_handler.php?id=2`

**Expected Results:**
- ✓ Question displays: "Which tag is used for the largest heading?"
- ✓ Three option buttons: `<h1>`, `<h3>`, `<head>`
- ✓ Fox enemy displays with HP bar
- ✓ Hearts display in header
- ✓ Progress bar shows ~14% (2/7 levels)
- ✓ CHECK button disabled initially
- ✓ Clicking option enables CHECK button
- ✓ Option gets "selected" class when clicked

**Browser Console Check:**
```javascript
console.log(window.activityConfig);

// Expected output:
{
    type: "multiple-choice",
    correctAnswer: "<h1>",
    currentHearts: 10,
    currentProgress: 1,
    redirectUrl: "lecture_handler.php?id=3",
    enemyHP: 10,
    hasEnemy: true,
    correctTitle: "Correct! 🎉",
    correctDetails: "The <h1> tag defines...",
    wrongTitle: "Not quite! 🤔",
    wrongDetails: "The <h1> tag defines..."
}
```

**Action Test - Correct Answer:**
1. Click `<h1>` option
2. Click CHECK button
3. Expected:
   - ✓ Feedback panel slides up
   - ✓ Green checkmark icon
   - ✓ Title: "Correct! 🎉"
   - ✓ Enemy HP decreases (animation)
   - ✓ CONTINUE button appears
   - ✓ Clicking CONTINUE redirects to level 3 (lecture)

**Action Test - Wrong Answer:**
1. Click `<h3>` option
2. Click CHECK button
3. Expected:
   - ✓ Feedback panel slides up
   - ✓ Red X icon
   - ✓ Title: "Not quite! 🤔"
   - ✓ Hearts decrease from 10 to 9
   - ✓ CONTINUE button appears
   - ✓ Clicking CONTINUE reloads same page

---

### Test 3: Fill-in-Blank Activity
**URL:** `activity_handler.php?id=4`

**Expected Results:**
- ✓ Question displays: "Fill in the blank to apply a style..."
- ✓ Code template displays with blank input field
- ✓ Bear enemy displays
- ✓ Input field is focused on page load
- ✓ CHECK button disabled initially
- ✓ Typing in input enables CHECK button
- ✓ Enter key submits answer

**Action Test - Correct Answer:**
1. Type "600" in input field
2. Click CHECK or press Enter
3. Expected:
   - ✓ Feedback panel shows "Perfect! 🎯"
   - ✓ XP awarded (25 XP)
   - ✓ Level marked complete in database
   - ✓ Redirect to level 5 (lecture)

**Database Check After Correct:**
```sql
-- Check XP was awarded
SELECT xp_points FROM user_stats WHERE user_id = YOUR_USER_ID;
-- Should increase by 25

-- Check level completion
SELECT * FROM user_progress WHERE user_id = YOUR_USER_ID AND level_id = 4;
-- Should have row with completed_at timestamp
```

---

### Test 4: Code Editor Activity
**URL:** `activity_handler.php?id=6`

**Expected Results:**
- ✓ Wiza teacher image displays
- ✓ Instruction: "Write your first HTML heading"
- ✓ Hint displays (optional)
- ✓ Code editor textarea present
- ✓ Output panel present (hidden initially)
- ✓ CHECK button disabled initially
- ✓ Typing code enables CHECK button
- ✓ No enemy displays (hasEnemy: false)

**Action Test - Correct Code:**
1. Type: `<h1>Hello World!</h1>`
2. Click CHECK
3. Expected:
   - ✓ Output panel appears
   - ✓ Live preview shows heading
   - ✓ Feedback: "Excellent Work! 🎉"
   - ✓ XP awarded (30 XP)
   - ✓ Redirect to level 7 (lecture)

**Action Test - Wrong Code:**
1. Type: `<h2>Hello</h2>`
2. Click CHECK
3. Expected:
   - ✓ Feedback: "Not quite! 🤔"
   - ✓ Hearts decrease
   - ✓ Stay on same page

**Validation Test - Case Insensitive:**
```html
<!-- These should ALL be correct (case_sensitive: false) -->
<h1>Hello World!</h1>
<H1>Hello World!</H1>
<h1>HELLO WORLD!</h1>
```

**Validation Test - Ignore Whitespace:**
```html
<!-- These should ALL be correct (ignore_whitespace: true) -->
<h1>Hello World!</h1>
<h1>  Hello World!  </h1>
<h1>
    Hello World!
</h1>
```

---

### Test 5: SKIP Functionality

**Any Activity - Test SKIP:**
1. Click SKIP button
2. Confirm in alert dialog
3. Expected:
   - ✓ Hearts decrease by 1
   - ✓ Redirect to next level
   - ✓ Session updated

**Test Game Over:**
1. Manually set hearts to 1:
   ```php
   $_SESSION['hearts'] = 1;
   ```
2. Click SKIP or answer wrong
3. Expected:
   - ✓ Redirect to `/views/gameover.php`

---

### Test 6: Progression Flow

**Complete Entire Course:**
1. Start at level 1: `lecture_handler.php?id=1`
2. Click FINISH → Should go to level 2
3. Level 2 (activity): Answer correctly → Should go to level 3
4. Level 3 (lecture): Click FINISH → Should go to level 4
5. Level 4 (activity): Answer correctly → Should go to level 5
6. Level 5 (lecture): Click FINISH → Should go to level 6
7. Level 6 (activity): Answer correctly → Should go to level 7
8. Level 7 (lecture): Click FINISH → Should go to **dashboard**

**Expected Final State:**
```sql
-- All levels completed
SELECT COUNT(*) FROM user_progress WHERE user_id = YOUR_USER_ID;
-- Should return 7

-- XP accumulated
SELECT xp_points FROM user_stats WHERE user_id = YOUR_USER_ID;
-- Should be: 10+25+10+25+10+30+10 = 120 XP
```

---

### Test 7: Error Handling

**Invalid Level ID:**
- URL: `lecture_handler.php?id=999`
- Expected: Error message "Level not found"

**Wrong Handler Type:**
- URL: `lecture_handler.php?id=2` (activity, not lecture)
- Expected: Error message "Invalid level type"

**Missing Content File:**
1. Temporarily rename `data/html/1.html`
2. URL: `lecture_handler.php?id=1`
3. Expected: Error message "Failed to load lecture content"
4. Restore file

**Not Logged In:**
1. Logout or clear session
2. URL: `lecture_handler.php?id=1`
3. Expected: Redirect to login page

---

### Test 8: JavaScript Integration

**Verify activity.js Works Unchanged:**
```javascript
// Open browser console on activity page

// Check config loaded
console.log(window.activityConfig);

// Test option selection (multiple-choice)
selectOption(document.querySelector('.option-btn'));
console.log(selectedAnswer);  // Should match clicked option

// Test answer submission
submitAnswer();  // Should show feedback panel

// Test enemy HP update
console.log(enemyHP);  // Should decrease on wrong answer
```

**Verify lecture.js Works Unchanged:**
```javascript
// Open browser console on lecture page

// Check config loaded
console.log(window.lectureConfig);

// Check stages loaded
console.log(lectureStages);  // Should be array with HTML content

// Test finish button
nextStage();  // Should redirect to next level
```

---

## Browser Compatibility Test

Test in multiple browsers:
- [ ] Chrome/Edge (latest)
- [ ] Firefox (latest)
- [ ] Safari (if available)

Check:
- ✓ All JavaScript executes
- ✓ CSS renders correctly
- ✓ Forms submit properly
- ✓ Redirects work
- ✓ No console errors

---

## Performance Test

**Load Time:**
- Each handler should load in < 200ms
- Database queries should execute in < 50ms
- File reads should complete in < 10ms

**Check with DevTools:**
1. Open Network tab
2. Load handler page
3. Check timing:
   - ✓ TTFB (Time to First Byte) < 100ms
   - ✓ Total load time < 500ms

---

## Debugging Tips

### Enable Error Display
```php
// Add to top of handler file temporarily
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Check PHP Error Log
```bash
# XAMPP default location
tail -f C:\xampp\php\logs\php_error_log
```

### Database Query Debugging
```php
// Add to LevelModel methods
echo "<pre>";
var_dump($sql);
var_dump($stmt->debugDumpParams());
echo "</pre>";
```

### JSON Decode Debugging
```php
// After json_decode()
if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON Error: " . json_last_error_msg());
}
```

### Session Debugging
```php
// Check what's in session
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
```

---

## Success Criteria

### All Tests Pass When:
- ✅ All 7 levels load without errors
- ✅ Correct answers award XP and complete levels
- ✅ Wrong answers decrease hearts correctly
- ✅ Game over triggers at 0 hearts
- ✅ Progression flows level 1 → 2 → 3 → ... → 7 → dashboard
- ✅ `window.activityConfig` matches expected structure
- ✅ `window.lectureConfig` matches expected structure
- ✅ JavaScript files work unchanged
- ✅ Database updates on completion
- ✅ No PHP errors in logs
- ✅ No JavaScript errors in console

---

## Next Steps After Testing

Once all tests pass:

1. **Update Dashboard**
   - Load levels from database instead of hardcoded roadmap
   - Show actual completion status from user_progress table

2. **Delete Old Stage Files**
   - Backup first!
   - Remove `views/html/stage-*.php` files
   - Keep only handler controllers

3. **Create Generic Templates** (Optional Enhancement)
   - Extract view HTML from handlers into separate template files
   - `views/templates/lecture.php`
   - `views/templates/multiple_choice.php`
   - `views/templates/fill_blank.php`
   - `views/templates/code_editor.php`

4. **Add More Levels**
   - Just add SQL INSERT for new level
   - Create corresponding content file
   - No code changes needed!

---

## Troubleshooting Common Issues

### Issue: "Level not found"
**Cause:** Database not migrated  
**Fix:** Run `data_migration.sql`

### Issue: "Failed to load lecture content"
**Cause:** Content file missing  
**Fix:** Verify all 7 files in `data/html/` exist

### Issue: Hearts not updating
**Cause:** Session not persisting  
**Fix:** Check session_start() is called, verify cookies enabled

### Issue: Redirect loops
**Cause:** Next level calculation error  
**Fix:** Check parent_level_id relationships in database

### Issue: JavaScript errors "activityConfig undefined"
**Cause:** JSON encoding failed  
**Fix:** Check PHP json_encode() output, verify no syntax errors

### Issue: XP not awarded
**Cause:** StatsModel::addXP() not called or failed  
**Fix:** Check PHP error log, verify user_stats table exists

---

## Automated Testing (Future)

Consider adding PHPUnit tests:

```php
// tests/LevelModelTest.php
public function testGetLevelById()
{
    $level = LevelModel::getLevelById($this->pdo, 1);
    $this->assertIsArray($level);
    $this->assertEquals('lecture', $level['level_type']);
}

public function testReadContentFile()
{
    $html = LevelModel::readContentFile(1, 'lecture');
    $this->assertStringContainsString('<html>', $html);
}
```

---

**Happy Testing! 🚀**
