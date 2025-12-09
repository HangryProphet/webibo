# 🧪 Achievement System - Quick Test Guide

## Prerequisites
- Database seeded with achievements: `php scripts/seed_achievements.php`
- Fresh user account for testing

---

## Test 1: Registration Achievement ✅

**Steps:**
1. Open browser to `http://localhost/Webibo/views/signup.php`
2. Register new user:
   - First Name: Test
   - Last Name: User
   - Email: test@example.com
   - Username: testuser
   - Password: test123
3. Submit form

**Expected Result:**
- User redirected to login page (dev mode skips email verification)
- Database check:
  ```sql
  SELECT * FROM user_achievements 
  WHERE user_id = (SELECT id FROM users WHERE username = 'testuser');
  ```
- Should see: `achievement_id = 1` (Hello, World!)
- Should see: `achievement_id = 2` (Verified! - auto in dev mode)

---

## Test 2: Email Verification Achievement ✅

**Note:** In development mode, this is automatically awarded with registration.

**Manual Test (Production Mode):**
1. Set `APP_ENV=production` in `.env`
2. Register user
3. Click verification link from email
4. Check database for `achievement_id = 2`

---

## Test 3: First Level Completion ✅

**Steps:**
1. Login as test user
2. Navigate to dashboard
3. Click "HTML Basics" course
4. Complete Level 1 (Welcome lecture)
5. Click "Continue"
6. Complete Level 2 (Multiple choice quiz)

**Expected Results:**
After Level 1:
```sql
-- No new achievements (lectures don't award achievements yet)
```

After Level 2:
```sql
SELECT * FROM user_achievements WHERE user_id = [your_id];
-- Should see:
-- achievement_id = 3 (First Commit - first level completion)
-- achievement_id = 4 (The Architect - level 2 completion)
```

---

## Test 4: Fill-Blank Achievement ✅

**Steps:**
1. Continue from Level 3 (lecture)
2. Complete Level 4 (Fill-in-the-blank)

**Expected Result:**
```sql
-- New achievement:
-- achievement_id = 5 (Blank Slate - first fill-blank)
```

---

## Test 5: Code Editor Achievement ✅

**Steps:**
1. Continue through Level 5 (lecture)
2. Complete Level 6 (Code editor)
   - Type: `<h1>Hello World!</h1>`
   - Click CHECK
   - Click CONTINUE

**Expected Result:**
```sql
-- New achievements:
-- achievement_id = 6 (Syntax Seal of Approval - first code-editor)
-- achievement_id = 7 (Heading in the Right Direction - level 6/7)
```

---

## Test 6: HTML Master Achievement ✅

**Steps:**
1. Complete Level 7 (Final lecture)

**Expected Result:**
```sql
-- New achievement:
-- achievement_id = 11 (HTML Foundation Master - all 7 levels)
```

**Verification Query:**
```sql
SELECT 
    a.id, 
    a.title, 
    ua.earned_at 
FROM achievements a
LEFT JOIN user_achievements ua ON a.id = ua.achievement_id 
    AND ua.user_id = (SELECT id FROM users WHERE username = 'testuser')
ORDER BY a.id;
```

**Expected Count:** 7 achievements earned (1, 2, 3, 4, 5, 6, 7, 11)

---

## Test 7: Achievements Page Display ✅

**Steps:**
1. Navigate to `http://localhost/Webibo/views/achievements.php`

**Expected Display:**

**Header:**
- Title: "All Achievements"
- Stats: "7 / 11" (or current count)
- Percentage: "64% Complete" (7/11)

**Achievement Cards:**

**Earned Achievements (Green Border, Checkmark):**
- ✓ Hello, World! (with earned date)
- ✓ Verified! (with earned date)
- ✓ First Commit (with earned date)
- ✓ The Architect (with earned date)
- ✓ Blank Slate (with earned date)
- ✓ Syntax Seal of Approval (with earned date)
- ✓ Heading in the Right Direction (with earned date)

**Locked Achievements (Dimmed, No Checkmark):**
- 🔒 Chain Link
- 🔒 A Pretty Picture
- 🔒 List-o-mania
- ✓ HTML Foundation Master (should be earned if all 7 completed)

**Visual Checks:**
- Earned cards have green (`#58cc02`) left border
- Locked cards are semi-transparent (opacity: 0.6)
- Each earned card shows earned date below description
- Icons display correctly (Font Awesome)

---

## Test 8: Duplicate Prevention ✅

**Steps:**
1. Complete level 2 again (or any previously completed level)

**Expected Result:**
- No duplicate achievements in database
- Query should still show only one entry for each achievement_id:
  ```sql
  SELECT achievement_id, COUNT(*) as count
  FROM user_achievements
  WHERE user_id = [your_id]
  GROUP BY achievement_id
  HAVING count > 1;
  -- Should return 0 rows (no duplicates)
  ```

---

## Test 9: Progress Tracking ✅

**Steps:**
1. Create second test user
2. Complete only levels 1-3
3. Check achievements page

**Expected Display:**
- Stats: "3 / 11"
- Percentage: "27% Complete"
- Earned: Hello World, Verified, First Commit
- Locked: All others (including The Architect)

---

## Test 10: Console Error Check ✅

**Steps:**
1. Open browser DevTools (F12)
2. Navigate to achievements page
3. Check Console tab

**Expected Result:**
- No JavaScript errors
- No 404 errors for missing resources
- No SQL errors in PHP error log

---

## Common Issues & Solutions

### Issue: Achievements not appearing
**Solution:**
```bash
# Re-run seeder
php scripts/seed_achievements.php

# Verify database
mysql -u root -p
USE webibo;
SELECT COUNT(*) FROM achievements;  # Should be 11
```

### Issue: Duplicate achievements
**Solution:**
```sql
-- Clean up duplicates (if any)
DELETE t1 FROM user_achievements t1
INNER JOIN user_achievements t2 
WHERE t1.id > t2.id 
  AND t1.user_id = t2.user_id 
  AND t1.achievement_id = t2.achievement_id;
```

### Issue: Achievements page blank
**Solution:**
- Check PHP error log: `tail -f /xampp/apache/logs/error.log`
- Verify session: User must be logged in
- Check database connection in `achievements.php` controller

### Issue: Icons not showing
**Solution:**
- Verify Font Awesome CDN: `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css`
- Check icon names in achievements.json (must start with `fa-`)

---

## Quick SQL Checks

```sql
-- See all achievements
SELECT * FROM achievements;

-- See user's achievements
SELECT 
    u.username,
    a.title,
    ua.earned_at
FROM user_achievements ua
JOIN users u ON ua.user_id = u.id
JOIN achievements a ON ua.achievement_id = a.id
ORDER BY ua.earned_at DESC;

-- Count achievements per user
SELECT 
    u.username,
    COUNT(ua.achievement_id) as earned_count,
    ROUND(COUNT(ua.achievement_id) / 11 * 100, 0) as percentage
FROM users u
LEFT JOIN user_achievements ua ON u.id = ua.user_id
GROUP BY u.id;

-- Find users with specific achievement
SELECT u.username
FROM users u
JOIN user_achievements ua ON u.id = ua.user_id
WHERE ua.achievement_id = 11;  -- HTML Foundation Master
```

---

## Success Checklist

- [ ] All 11 achievements seeded
- [ ] Registration awards achievement #1
- [ ] Email verification awards achievement #2 (or auto in dev mode)
- [ ] First level completion awards achievement #3
- [ ] Level 2 completion awards achievement #4
- [ ] Level 4 completion awards achievement #5
- [ ] Level 6 completion awards achievements #6 and #7
- [ ] All 7 levels completion awards achievement #11
- [ ] No duplicate achievements in database
- [ ] Achievements page displays correctly
- [ ] Earned achievements show green border
- [ ] Locked achievements appear dimmed
- [ ] Progress stats accurate
- [ ] No console errors

---

**Test Duration:** ~15 minutes for full test suite  
**Last Updated:** December 10, 2025
