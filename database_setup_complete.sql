-- ==================================================
-- WEBIBO COMPLETE DATABASE SETUP
-- Run this ONCE to set up everything from scratch
-- ==================================================

-- Drop and recreate database (fresh start)
DROP DATABASE IF EXISTS webibo;
CREATE DATABASE webibo;
USE webibo;

-- ==================================================
-- CREATE TABLES
-- ==================================================

CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) NULL,
    `last_name` VARCHAR(100) NULL,
    `avatar_path` VARCHAR(255) NOT NULL DEFAULT '/assets/img/avatars/default.png',
    `is_verified` BOOLEAN NOT NULL DEFAULT FALSE,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `user_stats` (
    `user_id` INT UNSIGNED NOT NULL PRIMARY KEY,
    `xp_points` INT UNSIGNED NOT NULL DEFAULT 0,
    `current_streak` INT UNSIGNED NOT NULL DEFAULT 0,
    `longest_streak` INT UNSIGNED NOT NULL DEFAULT 0,
    `last_login_date` DATE NULL
) ENGINE=InnoDB;

CREATE TABLE `courses` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT NULL
) ENGINE=InnoDB;

CREATE TABLE `levels` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT UNSIGNED NOT NULL,
    `level_type` ENUM('lecture', 'multiple-choice', 'fill-blank', 'code-editor') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `xp_reward` INT UNSIGNED NOT NULL DEFAULT 10,
    `order_in_course` INT NOT NULL,
    `parent_level_id` INT UNSIGNED NULL
) ENGINE=InnoDB;

CREATE TABLE `user_progress` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `level_id` INT UNSIGNED NOT NULL,
    `completed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_level` (`user_id`, `level_id`)
) ENGINE=InnoDB;

CREATE TABLE `achievements` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) NULL,
    `icon_path` VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE `user_achievements` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `achievement_id` INT UNSIGNED NOT NULL,
    `earned_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `email_verifications` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `token_hash` VARCHAR(255) NOT NULL,
    `expires_at` TIMESTAMP NOT NULL
) ENGINE=InnoDB;

CREATE TABLE `password_resets` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `token_hash` VARCHAR(255) NOT NULL,
    `expires_at` TIMESTAMP NOT NULL
) ENGINE=InnoDB;

-- ==================================================
-- CREATE FOREIGN KEYS
-- ==================================================
ALTER TABLE `user_stats` ADD CONSTRAINT `fk_stats_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `levels` ADD CONSTRAINT `fk_level_course` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`);
ALTER TABLE `levels` ADD CONSTRAINT `fk_level_parent` FOREIGN KEY (`parent_level_id`) REFERENCES `levels`(`id`) ON DELETE SET NULL;
ALTER TABLE `user_progress` ADD CONSTRAINT `fk_progress_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `user_progress` ADD CONSTRAINT `fk_progress_level` FOREIGN KEY (`level_id`) REFERENCES `levels`(`id`);
ALTER TABLE `user_achievements` ADD CONSTRAINT `fk_userach_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `user_achievements` ADD CONSTRAINT `fk_userach_achievement` FOREIGN KEY (`achievement_id`) REFERENCES `achievements`(`id`);
ALTER TABLE `email_verifications` ADD CONSTRAINT `fk_verify_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `password_resets` ADD CONSTRAINT `fk_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;

-- ==================================================
-- INSERT COURSES
-- ==================================================
INSERT INTO courses (id, title, description) VALUES
(1, 'HTML Foundations', 'Master the building blocks of the web. Learn to structure content with HTML.'),
(2, 'CSS Foundations', 'Style your web pages beautifully. Learn colors, fonts, and layouts.'),
(3, 'JavaScript Foundations', 'Make your websites interactive. Learn programming fundamentals.');

-- ==================================================
-- INSERT ACHIEVEMENTS
-- ==================================================
INSERT INTO achievements (id, title, description, icon_path) VALUES
-- Onboarding Achievements
(1, 'Hello, World!', 'Create your account and start your coding adventure', 'fa-user-plus'),
(2, 'Verified!', 'Verify your email address and secure your account', 'fa-check-circle'),
(3, 'First Commit', 'Complete your very first level', 'fa-flag-checkered'),
(4, 'Picture Perfect', 'Upload a custom profile picture', 'fa-camera'),

-- HTML Course Achievements
(5, 'The Architect', 'Master the essential HTML document structure', 'fa-file-code'),
(6, 'Multiple Victor', 'Ace your first Multiple-Choice quiz', 'fa-tasks'),
(7, 'Blank Slate', 'Conquer a Fill-in-the-Blanks challenge', 'fa-pen'),
(8, 'Syntax Seal of Approval', 'Complete your first live Coding Challenge', 'fa-code-branch'),
(9, 'Heading in the Right Direction', 'Master the hierarchy of headings and paragraphs', 'fa-heading'),
(10, 'Chain Link', 'Learn how to connect the web with anchor tags', 'fa-link'),
(11, 'A Pretty Picture', 'Master the img tag and the importance of alt text', 'fa-image'),
(12, 'List-o-mania', 'Become a pro at organizing content with lists', 'fa-list'),
(13, 'Putting It All Together', 'Complete the final portfolio project', 'fa-briefcase'),
(14, 'HTML Foundation Master', 'Complete the entire HTML Learning Path', 'fa-trophy'),

-- CSS Course Achievements
(15, 'First Splash of Color', 'Begin your journey into CSS', 'fa-palette'),
(16, 'Selector Selector', 'Master element, class, and ID selectors', 'fa-crosshairs'),
(17, 'Hue Hero', 'Learn the different ways to define colors', 'fa-paint-brush'),
(18, 'Box Model Boxer', 'Understand the fundamentals of the CSS Box Model', 'fa-box'),
(19, 'CSS Styling Apprentice', 'Complete the entire CSS Learning Path', 'fa-trophy'),

-- JavaScript Course Achievements
(20, 'The Spark of Interactivity', 'Write your first line of JavaScript', 'fa-bolt'),
(21, 'Variable Virtuoso', 'Master variables, constants, and data types', 'fa-database'),
(22, 'Operator Operator', 'Conquer operators and build your first functions', 'fa-calculator'),
(23, 'DOM Dominator', 'Learn how to manipulate HTML elements with JavaScript', 'fa-cogs'),
(24, 'JavaScript Interactivity Master', 'Complete the entire JavaScript Learning Path', 'fa-trophy'),

-- Streak Achievements
(25, 'Warming Up', 'Maintain a 3-day learning streak', 'fa-fire'),
(26, 'Weekly Warrior', 'Maintain a 7-day learning streak', 'fa-fire-alt'),

-- Performance Achievements
(27, 'Perfect Score', 'Complete a multi-question quiz level without any wrong answers', 'fa-star'),
(28, 'Speed Demon', 'Complete 5 levels in a single day', 'fa-rocket'),
(29, 'Triple Threat', 'Complete at least one level in HTML, CSS, and JavaScript', 'fa-layer-group'),
(30, 'Knowledge Seeker', 'Complete all lectures across all three courses', 'fa-book-open');

-- ==================================================
-- HTML COURSE LEVELS (20 Levels)
-- ==================================================

-- MODULE 1: HTML FOUNDATIONS
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(1, 1, 'lecture', 'The Very Beginning', 10, 1, NULL),
(2, 1, 'multiple-choice', 'The Very Beginning - Quiz', 25, 2, 1),
(3, 1, 'fill-blank', 'The Very Beginning - Practice', 25, 3, 2),
(4, 1, 'code-editor', 'The Very Beginning - Challenge', 30, 4, 3);

-- MODULE 2: ADDING CONTENT
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(5, 1, 'lecture', 'Headings and Paragraphs', 10, 5, 4),
(6, 1, 'multiple-choice', 'Headings and Paragraphs - Quiz', 25, 6, 5),
(7, 1, 'fill-blank', 'Headings and Paragraphs - Practice', 25, 7, 6),
(8, 1, 'code-editor', 'Headings and Paragraphs - Challenge', 30, 8, 7);

-- MODULE 3: LINKS AND IMAGES
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(9, 1, 'lecture', 'Connecting the Web with Links', 10, 9, 8),
(10, 1, 'multiple-choice', 'Links - Quiz', 25, 10, 9),
(11, 1, 'fill-blank', 'Links - Practice', 25, 11, 10),
(12, 1, 'code-editor', 'Links - Challenge', 30, 12, 11);

-- MODULE 4: ADDING VISUALS
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(13, 1, 'lecture', 'Adding Images', 10, 13, 12),
(14, 1, 'multiple-choice', 'Images - Quiz', 25, 14, 13),
(15, 1, 'fill-blank', 'Images - Practice', 25, 15, 14),
(16, 1, 'code-editor', 'Images - Challenge', 30, 16, 15);

-- MODULE 5: ORGANIZING CONTENT
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(17, 1, 'lecture', 'Lists', 10, 17, 16),
(18, 1, 'multiple-choice', 'Lists - Quiz', 25, 18, 17),
(19, 1, 'fill-blank', 'Lists - Practice', 25, 19, 18),
(20, 1, 'code-editor', 'Lists - Challenge', 30, 20, 19);

-- ==================================================
-- CSS COURSE LEVELS (12 Levels)
-- ==================================================

-- MODULE 1: CSS BASICS
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(21, 2, 'lecture', 'Getting Started with CSS', 10, 1, NULL),
(22, 2, 'multiple-choice', 'Getting Started with CSS - Quiz', 25, 2, 21),
(23, 2, 'fill-blank', 'Getting Started with CSS - Practice', 25, 3, 22),
(24, 2, 'code-editor', 'Getting Started with CSS - Challenge', 30, 4, 23);

-- MODULE 2: STYLING TEXT
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(25, 2, 'lecture', 'Working with Colors and Text', 10, 5, 24),
(26, 2, 'multiple-choice', 'Colors and Text - Quiz', 25, 6, 25),
(27, 2, 'fill-blank', 'Colors and Text - Practice', 25, 7, 26),
(28, 2, 'code-editor', 'Colors and Text - Challenge', 30, 8, 27);

-- MODULE 3: BOX MODEL
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(29, 2, 'lecture', 'Understanding the Box Model', 10, 9, 28),
(30, 2, 'multiple-choice', 'Box Model and Layout - Quiz', 25, 10, 29),
(31, 2, 'fill-blank', 'Box Model and Layout - Practice', 25, 11, 30),
(32, 2, 'code-editor', 'Box Model and Layout - Challenge', 30, 12, 31);

-- ==================================================
-- JAVASCRIPT COURSE LEVELS (12 Levels)
-- ==================================================

-- MODULE 1: JAVASCRIPT BASICS
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(33, 3, 'lecture', 'Getting Started with JavaScript', 10, 1, NULL),
(34, 3, 'multiple-choice', 'Getting Started with JavaScript - Quiz', 25, 2, 33),
(35, 3, 'fill-blank', 'Getting Started with JavaScript - Practice', 25, 3, 34),
(36, 3, 'code-editor', 'Getting Started with JavaScript - Challenge', 30, 4, 35);

-- MODULE 2: FUNCTIONS AND OPERATORS
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(37, 3, 'lecture', 'Operators and Functions', 10, 5, 36),
(38, 3, 'multiple-choice', 'Operators and Functions - Quiz', 25, 6, 37),
(39, 3, 'fill-blank', 'Operators and Functions - Practice', 25, 7, 38),
(40, 3, 'code-editor', 'Operators and Functions - Challenge', 30, 8, 39);

-- MODULE 3: DOM MANIPULATION
INSERT INTO levels (id, course_id, level_type, title, xp_reward, order_in_course, parent_level_id) VALUES
(41, 3, 'lecture', 'Working with HTML Elements', 10, 9, 40),
(42, 3, 'multiple-choice', 'DOM Manipulation - Quiz', 25, 10, 41),
(43, 3, 'fill-blank', 'DOM Manipulation - Practice', 25, 11, 42),
(44, 3, 'code-editor', 'DOM Manipulation - Challenge', 30, 12, 43);

-- ==================================================
-- VERIFY DATA
-- ==================================================
SELECT 'Database setup complete!' AS status;
SELECT 'Courses inserted:' AS info, COUNT(*) AS count FROM courses;
SELECT 'Achievements inserted:' AS info, COUNT(*) AS count FROM achievements;
SELECT 'Levels inserted:' AS info, COUNT(*) AS count FROM levels;
SELECT 'HTML levels:' AS info, COUNT(*) AS count FROM levels WHERE course_id = 1;
SELECT 'CSS levels:' AS info, COUNT(*) AS count FROM levels WHERE course_id = 2;
SELECT 'JavaScript levels:' AS info, COUNT(*) AS count FROM levels WHERE course_id = 3;

-- ==================================================
-- SETUP COMPLETE
-- ==================================================
-- Total Courses: 3
-- Total Levels: 44
--   HTML: 20 levels (5 modules × 4 levels each)
--   CSS: 12 levels (3 modules × 4 levels each)
--   JS: 12 levels (3 modules × 4 levels each)
-- ==================================================
