-- ============================================
-- THE GREAT MIGRATION - SQL INSERT STATEMENTS
-- ============================================
-- This file contains all game content extracted from hardcoded PHP/JS files
-- Run this after creating the database schema

USE webibo;

-- ============================================
-- 1. INSERT COURSE
-- ============================================

INSERT INTO courses (id, title, description) VALUES
(1, 'HTML Basics', 'Learn the fundamentals of HTML, the foundation of every webpage');

-- ============================================
-- 2. INSERT LEVELS (7 total levels)
-- ============================================

-- Level 1: Lecture - Introduction
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(1, 1, 'lecture', 'Welcome to HTML!', 10, 1, NULL);

-- Level 2: Multiple Choice - H1 Tag
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(2, 1, 'multiple-choice', 'Which tag is used for the largest heading?', 25, 2, 1);

-- Level 3: Lecture - HTML Basics Reference
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(3, 1, 'lecture', 'HTML Basics Reference', 10, 3, 2);

-- Level 4: Fill-in-Blank - Media Query
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(4, 1, 'fill-blank', 'Fill in the blank for media query', 25, 4, 3);

-- Level 5: Lecture - Advanced Concepts (placeholder)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(5, 1, 'lecture', 'HTML Best Practices', 10, 5, 4);

-- Level 6: Code Editor - First Heading
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(6, 1, 'code-editor', 'Write your first HTML heading', 30, 6, 5);

-- Level 7: Lecture - Final Summary (placeholder)
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(7, 1, 'lecture', 'Congratulations!', 10, 7, 6);

-- ============================================
-- VERIFICATION QUERY
-- ============================================
-- Run this to verify the data was inserted correctly:
-- SELECT l.id, l.order_in_course, l.level_type, l.title, l.xp_reward, c.title as course_title
-- FROM levels l
-- JOIN courses c ON l.course_id = c.id
-- ORDER BY l.order_in_course;
