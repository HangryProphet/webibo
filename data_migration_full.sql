-- ==================================================
-- WEBIBO COMPLETE CURRICULUM MIGRATION
-- Generated from WEBIBO STAGES.txt
-- 3 Courses: HTML, CSS, JavaScript
-- 44 Levels Total
-- ==================================================

USE webibo;

-- Clear existing data (in correct order due to foreign keys)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE user_progress;
TRUNCATE TABLE levels;
TRUNCATE TABLE courses;
SET FOREIGN_KEY_CHECKS = 1;

-- ==================================================
-- INSERT COURSES
-- ==================================================
INSERT INTO courses (id, title, description) VALUES
(1, 'HTML Foundations', 'Master the building blocks of the web. Learn to structure content with HTML.'),
(2, 'CSS Foundations', 'Style your web pages beautifully. Learn colors, fonts, and layouts.'),
(3, 'JavaScript Foundations', 'Make your websites interactive. Learn programming fundamentals.');

-- ==================================================
-- HTML COURSE LEVELS (20 Levels)
-- ==================================================

-- MODULE 1: HTML FOUNDATIONS
-- Level 1: THE VERY BEGINNING (Lecture)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(1, 1, 'lecture', 'The Very Beginning', 10, 1, NULL);

-- Level 2: THE VERY BEGINNING (Multiple Choice)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(2, 1, 'multiple-choice', 'The Very Beginning - Quiz', 25, 2, 1);

-- Level 3: THE VERY BEGINNING (Fill in the Blanks)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(3, 1, 'fill-blank', 'The Very Beginning - Practice', 25, 3, 2);

-- Level 4: THE VERY BEGINNING (Coding Challenge)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(4, 1, 'code-editor', 'The Very Beginning - Challenge', 30, 4, 3);

-- MODULE 2: ADDING CONTENT
-- Level 5: HEADINGS AND PARAGRAPHS (Lecture)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(5, 1, 'lecture', 'Headings and Paragraphs', 10, 5, 4);

-- Level 6: HEADINGS AND PARAGRAPHS (Multiple Choice)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(6, 1, 'multiple-choice', 'Headings and Paragraphs - Quiz', 25, 6, 5);

-- Level 7: HEADINGS AND PARAGRAPHS (Fill in the Blanks)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(7, 1, 'fill-blank', 'Headings and Paragraphs - Practice', 25, 7, 6);

-- Level 8: HEADINGS AND PARAGRAPHS (Coding Challenge)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(8, 1, 'code-editor', 'Headings and Paragraphs - Challenge', 30, 8, 7);

-- MODULE 3: LINKS AND IMAGES  
-- Level 9: CONNECTING THE WEB WITH LINKS (Lecture)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(9, 1, 'lecture', 'Connecting the Web with Links', 10, 9, 8);

-- Level 10: LINKS (Multiple Choice)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(10, 1, 'multiple-choice', 'Links - Quiz', 25, 10, 9);

-- Level 11: LINKS (Fill in the Blanks)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(11, 1, 'fill-blank', 'Links - Practice', 25, 11, 10);

-- Level 12: LINKS (Coding Challenge)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(12, 1, 'code-editor', 'Links - Challenge', 30, 12, 11);

-- MODULE 4: ADDING VISUALS
-- Level 13: ADDING IMAGES (Lecture)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(13, 1, 'lecture', 'Adding Images', 10, 13, 12);

-- Level 14: IMAGES (Multiple Choice)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(14, 1, 'multiple-choice', 'Images - Quiz', 25, 14, 13);

-- Level 15: IMAGES (Fill in the Blanks)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(15, 1, 'fill-blank', 'Images - Practice', 25, 15, 14);

-- Level 16: IMAGES (Coding Challenge)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(16, 1, 'code-editor', 'Images - Challenge', 30, 16, 15);

-- MODULE 5: ORGANIZING CONTENT
-- Level 17: LISTS (Lecture)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(17, 1, 'lecture', 'Lists', 10, 17, 16);

-- Level 18: LISTS (Multiple Choice)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(18, 1, 'multiple-choice', 'Lists - Quiz', 25, 18, 17);

-- Level 19: LISTS (Fill in the Blanks)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(19, 1, 'fill-blank', 'Lists - Practice', 25, 19, 18);

-- Level 20: LISTS (Coding Challenge)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(20, 1, 'code-editor', 'Lists - Challenge', 30, 20, 19);

-- ==================================================
-- CSS COURSE LEVELS (12 Levels)
-- ==================================================

-- MODULE 1: CSS BASICS
-- Level 21 (CSS Level 1): GETTING STARTED WITH CSS (Lecture)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(21, 2, 'lecture', 'Getting Started with CSS', 10, 1, NULL);

-- Level 22 (CSS Level 2): GETTING STARTED WITH CSS (Multiple Choice)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(22, 2, 'multiple-choice', 'Getting Started with CSS - Quiz', 25, 2, 21);

-- Level 23 (CSS Level 3): GETTING STARTED WITH CSS (Fill in the Blanks)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(23, 2, 'fill-blank', 'Getting Started with CSS - Practice', 25, 3, 22);

-- Level 24 (CSS Level 4): GETTING STARTED WITH CSS (Coding Challenge)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(24, 2, 'code-editor', 'Getting Started with CSS - Challenge', 30, 4, 23);

-- MODULE 2: STYLING TEXT
-- Level 25 (CSS Level 5): WORKING WITH COLORS AND TEXT (Lecture)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(25, 2, 'lecture', 'Working with Colors and Text', 10, 5, 24);

-- Level 26 (CSS Level 6): COLORS AND TEXT (Multiple Choice)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(26, 2, 'multiple-choice', 'Colors and Text - Quiz', 25, 6, 25);

-- Level 27 (CSS Level 7): COLORS AND TEXT (Fill in the Blanks)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(27, 2, 'fill-blank', 'Colors and Text - Practice', 25, 7, 26);

-- Level 28 (CSS Level 8): COLORS AND TEXT (Coding Challenge)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(28, 2, 'code-editor', 'Colors and Text - Challenge', 30, 8, 27);

-- MODULE 3: BOX MODEL
-- Level 29 (CSS Level 9): UNDERSTANDING THE BOX MODEL (Lecture)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(29, 2, 'lecture', 'Understanding the Box Model', 10, 9, 28);

-- Level 30 (CSS Level 10): BOX MODEL AND LAYOUT (Multiple Choice)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(30, 2, 'multiple-choice', 'Box Model and Layout - Quiz', 25, 10, 29);

-- Level 31 (CSS Level 11): BOX MODEL AND LAYOUT (Fill in the Blanks)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(31, 2, 'fill-blank', 'Box Model and Layout - Practice', 25, 11, 30);

-- Level 32 (CSS Level 12): BOX MODEL AND LAYOUT (Coding Challenge)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(32, 2, 'code-editor', 'Box Model and Layout - Challenge', 30, 12, 31);

-- ==================================================
-- JAVASCRIPT COURSE LEVELS (12 Levels)
-- ==================================================

-- MODULE 1: JAVASCRIPT BASICS
-- Level 33 (JS Level 1): GETTING STARTED WITH JAVASCRIPT (Lecture)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(33, 3, 'lecture', 'Getting Started with JavaScript', 10, 1, NULL);

-- Level 34 (JS Level 2): GETTING STARTED WITH JAVASCRIPT (Multiple Choice)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(34, 3, 'multiple-choice', 'Getting Started with JavaScript - Quiz', 25, 2, 33);

-- Level 35 (JS Level 3): GETTING STARTED WITH JAVASCRIPT (Fill in the Blanks)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(35, 3, 'fill-blank', 'Getting Started with JavaScript - Practice', 25, 3, 34);

-- Level 36 (JS Level 4): GETTING STARTED WITH JAVASCRIPT (Coding Challenge)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(36, 3, 'code-editor', 'Getting Started with JavaScript - Challenge', 30, 4, 35);

-- MODULE 2: FUNCTIONS AND OPERATORS
-- Level 37 (JS Level 5): OPERATORS AND FUNCTIONS (Lecture)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(37, 3, 'lecture', 'Operators and Functions', 10, 5, 36);

-- Level 38 (JS Level 6): OPERATORS AND FUNCTIONS (Multiple Choice)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(38, 3, 'multiple-choice', 'Operators and Functions - Quiz', 25, 6, 37);

-- Level 39 (JS Level 7): OPERATORS AND FUNCTIONS (Fill in the Blanks)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(39, 3, 'fill-blank', 'Operators and Functions - Practice', 25, 7, 38);

-- Level 40 (JS Level 8): OPERATORS AND FUNCTIONS (Coding Challenge)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(40, 3, 'code-editor', 'Operators and Functions - Challenge', 30, 8, 39);

-- MODULE 3: DOM MANIPULATION
-- Level 41 (JS Level 9): WORKING WITH HTML ELEMENTS (Lecture)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(41, 3, 'lecture', 'Working with HTML Elements', 10, 9, 40);

-- Level 42 (JS Level 10): DOM MANIPULATION (Multiple Choice)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(42, 3, 'multiple-choice', 'DOM Manipulation - Quiz', 25, 10, 41);

-- Level 43 (JS Level 11): DOM MANIPULATION (Fill in the Blanks)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(43, 3, 'fill-blank', 'DOM Manipulation - Practice', 25, 11, 42);

-- Level 44 (JS Level 12): DOM MANIPULATION (Coding Challenge)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(44, 3, 'code-editor', 'DOM Manipulation - Challenge', 30, 12, 43);

-- ==================================================
-- VERIFY DATA
-- ==================================================
SELECT 'Courses inserted:' AS info, COUNT(*) AS count FROM courses;
SELECT 'Levels inserted:' AS info, COUNT(*) AS count FROM levels;
SELECT 'HTML levels:' AS info, COUNT(*) AS count FROM levels WHERE course_id = 1;
SELECT 'CSS levels:' AS info, COUNT(*) AS count FROM levels WHERE course_id = 2;
SELECT 'JavaScript levels:' AS info, COUNT(*) AS count FROM levels WHERE course_id = 3;

-- ==================================================
-- SUMMARY
-- ==================================================
-- Total Courses: 3
-- Total Levels: 44
--   HTML: 20 levels (5 modules × 4 levels each)
--   CSS: 12 levels (3 modules × 4 levels each)
--   JS: 12 levels (3 modules × 4 levels each)
--
-- Level Types Per Course:
--   - lecture: Introduction to topic (10 XP)
--   - multiple-choice: Quiz with 8-12 questions (25 XP)
--   - fill-blank: Fill-in-the-blank exercises (25 XP)
--   - code-editor: Coding challenge (30 XP)
--
-- Content Files:
--   HTML: /data/html/1.html to 20.json
--   CSS: /data/css/21.html to 32.json
--   JS: /data/js/33.html to 44.json
-- ==================================================
