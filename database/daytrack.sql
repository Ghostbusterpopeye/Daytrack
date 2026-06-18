-- ============================================================
--  DayTrack – Database Schema & Seed Data
--  Engine: MySQL 5.7+ / MariaDB 10.3+
--  Charset: utf8mb4
-- ============================================================

SET SQL_MODE   = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone  = "+00:00";
SET NAMES utf8mb4;

-- ── Drop & Create Database ──────────────────────────────────
DROP DATABASE IF EXISTS `daytrack`;
CREATE DATABASE `daytrack`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `daytrack`;

-- ── Users ───────────────────────────────────────────────────
CREATE TABLE `users` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(120)    NOT NULL,
  `email`        VARCHAR(180)    NOT NULL UNIQUE,
  `password`     VARCHAR(255)    NOT NULL,
  `role`         VARCHAR(60)     NOT NULL DEFAULT 'Team Member',
  `bio`          TEXT                    DEFAULT NULL,
  `avatar`       VARCHAR(255)            DEFAULT NULL,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Projects ────────────────────────────────────────────────
CREATE TABLE `projects` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`      INT UNSIGNED    NOT NULL,
  `name`         VARCHAR(120)    NOT NULL,
  `color`        VARCHAR(30)     NOT NULL DEFAULT 'primary',
  `icon`         VARCHAR(60)     NOT NULL DEFAULT 'bi-briefcase',
  `description`  TEXT                    DEFAULT NULL,
  `members`      SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `progress`     TINYINT UNSIGNED  NOT NULL DEFAULT 0,
  `archived`     TINYINT(1)      NOT NULL DEFAULT 0,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_proj_user` (`user_id`),
  CONSTRAINT `fk_proj_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Tasks ───────────────────────────────────────────────────
CREATE TABLE `tasks` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`      INT UNSIGNED    NOT NULL,
  `project_id`   INT UNSIGNED            DEFAULT NULL,
  `project_name` VARCHAR(120)    NOT NULL DEFAULT 'General',
  `title`        VARCHAR(255)    NOT NULL,
  `done`         TINYINT(1)      NOT NULL DEFAULT 0,
  `priority`     ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  `due_date`     DATE                    DEFAULT NULL,
  `notes`        TEXT                    DEFAULT NULL,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_task_user` (`user_id`),
  CONSTRAINT `fk_task_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Meetings ────────────────────────────────────────────────
CREATE TABLE `meetings` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`      INT UNSIGNED    NOT NULL,
  `title`        VARCHAR(180)    NOT NULL,
  `meet_time`    TIME            NOT NULL DEFAULT '09:00:00',
  `duration`     SMALLINT UNSIGNED NOT NULL DEFAULT 30,
  `members`      SMALLINT UNSIGNED NOT NULL DEFAULT 2,
  `type`         VARCHAR(30)     NOT NULL DEFAULT 'standup',
  `link`         VARCHAR(500)            DEFAULT '#',
  `notes`        TEXT                    DEFAULT NULL,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_meet_user` (`user_id`),
  CONSTRAINT `fk_meet_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Chat Messages ───────────────────────────────────────────
CREATE TABLE `messages` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`      INT UNSIGNED    NOT NULL,
  `sender_name`  VARCHAR(120)    NOT NULL,
  `body`         TEXT            NOT NULL,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_msg_user` (`user_id`),
  CONSTRAINT `fk_msg_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  SEED DATA
-- ============================================================

-- Demo users (password = "password123" bcrypt-hashed)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `bio`) VALUES
('Rian Pratama',  'rian@demo.com',  '$2y$12$FJU7VGGLGCXSuFmrO3d6BOdT3YHFJcNRYHPRj36YcoxI82QKOL7vy', 'Team Lead',    'Full-stack developer and project lead at DayTrack team.'),
('Demo User',     'demo@demo.com',  '$2y$12$FJU7VGGLGCXSuFmrO3d6BOdT3YHFJcNRYHPRj36YcoxI82QKOL7vy', 'Team Member',  'Demo account for testing DayTrack features.');

-- Projects for user 1
INSERT INTO `projects` (`user_id`, `name`, `color`, `icon`, `description`, `members`, `progress`, `archived`) VALUES
(1, 'Design System',  'warning', 'bi-layers',        'UI component library and design tokens.',    5, 80, 0),
(1, 'Marketing Web',  'danger',  'bi-globe',          'Company marketing site redesign.',           3, 25, 0),
(1, 'User Analytics', 'primary', 'bi-bar-chart-line', 'Analytics dashboard (archived).',           2, 100, 1);

-- Tasks for user 1
INSERT INTO `tasks` (`user_id`, `project_name`, `title`, `done`, `priority`, `due_date`, `notes`) VALUES
(1, 'Marketing Web',   'Refactor Landing Page Hero',  0, 'high',   NULL, ''),
(1, 'Management',      'Sprint Planning Document',    0, 'medium', NULL, ''),
(1, 'Design System',   'Team Avatar Review',          1, 'low',    NULL, ''),
(1, 'General',         'Write API Documentation',     0, 'medium', NULL, ''),
(1, 'Design System',   'Update onboarding flow',      0, 'high',   NULL, '');

-- Meetings for user 1
INSERT INTO `meetings` (`user_id`, `title`, `meet_time`, `duration`, `members`, `type`, `link`, `notes`) VALUES
(1, 'Weekly Sprint Sync',  '09:00:00', 30, 6, 'standup',   '#', 'Review sprint goals.'),
(1, 'Design Review',       '11:30:00', 45, 4, 'review',    '#', 'Check Figma files.'),
(1, 'Stakeholder Update',  '14:00:00', 60, 8, 'update',    '#', 'Q3 progress update.'),
(1, '1-on-1 with Manager', '16:30:00', 30, 2, 'one-on-one','#', 'Career growth discussion.');

-- Chat seed messages
INSERT INTO `messages` (`user_id`, `sender_name`, `body`) VALUES
(1, 'Rian Pratama', 'Hey team! Sprint review is tomorrow at 2PM 🚀'),
(1, 'Rian Pratama', 'Don\'t forget to update your task statuses before the meeting.');
