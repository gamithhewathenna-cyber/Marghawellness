-- MĀRGHĀ Wellness — Wellness page programmes (Admin › Listings › Wellness programmes)
-- Run in phpMyAdmin › (select database) › SQL. Safe to run again: nothing is duplicated or overwritten.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS wellness_programmes (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug         VARCHAR(140) NOT NULL,
    title        VARCHAR(160) NOT NULL,
    depth        ENUM('begin','deepen','immerse') NOT NULL DEFAULT 'begin',
    path         ENUM('rest','soul','healing') NOT NULL DEFAULT 'rest',
    audience     VARCHAR(100) NOT NULL DEFAULT 'solo' COMMENT 'Comma list: solo, women, couples, teams',
    nights       VARCHAR(40)  NOT NULL DEFAULT '',
    image        VARCHAR(255) NOT NULL DEFAULT '',
    summary      VARCHAR(300) NOT NULL DEFAULT '',
    description  MEDIUMTEXT NULL,
    is_active    TINYINT(1) NOT NULL DEFAULT 1,
    sort_order   SMALLINT NOT NULL DEFAULT 0,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_wellness_programmes_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Starter programmes (edit them in the admin; INSERT IGNORE skips any that already exist)
INSERT IGNORE INTO wellness_programmes (slug, title, depth, path, audience, nights, image, summary, sort_order) VALUES
('heal',             'Heal',               'begin',   'healing', 'solo,women,couples', '3-5', 'retreat-ocean-breath.jpg', 'Full programme details coming soon.', 1),
('uplift',           'Uplift',             'begin',   'soul',    'solo,women',         '3-5', 'journal-2.jpg',            'Full programme details coming soon.', 2),
('unplug-reconnect', 'Unplug & Reconnect', 'begin',   'rest',    'solo,couples,teams', '3-5', 'retreat-dark-sky.jpg',     'Full programme details coming soon.', 3),
('arogya',           'Arogya',             'deepen',  'healing', 'solo,couples',       '7',   'journey-south-coast.jpg',  'Full programme details coming soon.', 4),
('revive',           'Revive',             'immerse', 'rest',    'solo,women',         '14',  'journey-tree-of-life.jpg', 'Full programme details coming soon.', 5),
('longevity',        'Longevity',          'immerse', 'healing', 'solo,couples',       '21',  'journal-1.jpg',            'Full programme details coming soon.', 6);
