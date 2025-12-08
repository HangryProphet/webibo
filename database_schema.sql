-- Webibo Database Schema
-- Run this in phpMyAdmin or MySQL command line

CREATE DATABASE IF NOT EXISTS webibo;
USE webibo;

-- Users Table
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

-- User Stats Table
CREATE TABLE `user_stats` (
    `user_id` INT UNSIGNED NOT NULL PRIMARY KEY,
    `xp_points` INT UNSIGNED NOT NULL DEFAULT 0,
    `current_streak` INT UNSIGNED NOT NULL DEFAULT 0,
    `longest_streak` INT UNSIGNED NOT NULL DEFAULT 0,
    `last_login_date` DATE NULL
) ENGINE=InnoDB;

-- Courses Table
CREATE TABLE `courses` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT NULL
) ENGINE=InnoDB;

-- Insert courses
INSERT INTO courses (title, description) VALUES
('HTML Basics', 'Learn the fundamentals of HTML'),
('CSS Styling', 'Master CSS for beautiful web designs'),
('JavaScript Fundamentals', 'Learn JavaScript programming basics');

-- Levels Table
CREATE TABLE `levels` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT UNSIGNED NOT NULL,
    `level_type` ENUM('lecture', 'multiple-choice', 'fill-blank', 'code-editor') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `xp_reward` INT UNSIGNED NOT NULL DEFAULT 10,
    `order_in_course` INT NOT NULL,
    `parent_level_id` INT UNSIGNED NULL
) ENGINE=InnoDB;

-- User Progress Table
CREATE TABLE `user_progress` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `level_id` INT UNSIGNED NOT NULL,
    `completed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_user_level` (`user_id`, `level_id`)
) ENGINE=InnoDB;

-- Achievements Table
CREATE TABLE `achievements` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) NULL,
    `icon_path` VARCHAR(255) NULL
) ENGINE=InnoDB;

-- User Achievements Table
CREATE TABLE `user_achievements` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `achievement_id` INT UNSIGNED NOT NULL,
    `earned_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Email Verifications Table
CREATE TABLE `email_verifications` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `token_hash` VARCHAR(255) NOT NULL,
    `expires_at` TIMESTAMP NOT NULL
) ENGINE=InnoDB;

-- Password Resets Table
CREATE TABLE `password_resets` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `token_hash` VARCHAR(255) NOT NULL,
    `expires_at` TIMESTAMP NOT NULL
) ENGINE=InnoDB;

-- Foreign Key Constraints
ALTER TABLE `user_stats` ADD CONSTRAINT `fk_stats_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `levels` ADD CONSTRAINT `fk_level_course` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`);
ALTER TABLE `levels` ADD CONSTRAINT `fk_level_parent` FOREIGN KEY (`parent_level_id`) REFERENCES `levels`(`id`) ON DELETE SET NULL;
ALTER TABLE `user_progress` ADD CONSTRAINT `fk_progress_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `user_progress` ADD CONSTRAINT `fk_progress_level` FOREIGN KEY (`level_id`) REFERENCES `levels`(`id`);
ALTER TABLE `user_achievements` ADD CONSTRAINT `fk_userach_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `user_achievements` ADD CONSTRAINT `fk_userach_achievement` FOREIGN KEY (`achievement_id`) REFERENCES `achievements`(`id`);
ALTER TABLE `email_verifications` ADD CONSTRAINT `fk_verify_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
ALTER TABLE `password_resets` ADD CONSTRAINT `fk_reset_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE;
