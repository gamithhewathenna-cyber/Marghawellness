-- MĀRGHĀ Wellness — MySQL schema + seed data
-- Import via cPanel › phpMyAdmin › (select your database) › Import.
-- Compatible with MySQL 5.7+ / MariaDB 10.3+.

SET NAMES utf8mb4;
SET time_zone = '+05:30';

-- ---------------------------------------------------------------
-- Newsletter subscribers
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS subscribers (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(190) NOT NULL,
    source      VARCHAR(60)  NOT NULL DEFAULT 'website',
    ip_address  VARCHAR(45)  NULL,
    status      ENUM('subscribed','unsubscribed') NOT NULL DEFAULT 'subscribed',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME NULL,
    UNIQUE KEY uq_subscribers_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Enquiries (for the upcoming "Enquire Now" page)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS enquiries (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name     VARCHAR(120) NOT NULL,
    email         VARCHAR(190) NOT NULL,
    phone         VARCHAR(40)  NULL,
    country       VARCHAR(80)  NULL,
    travel_month  VARCHAR(40)  NULL,
    nights        TINYINT UNSIGNED NULL,
    budget        VARCHAR(60)  NULL,
    interest      VARCHAR(120) NULL,
    message       TEXT NULL,
    status        ENUM('new','contacted','closed') NOT NULL DEFAULT 'new',
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_enquiries_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Homepage hero slides
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS hero_slides (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    eyebrow     VARCHAR(160) NOT NULL,
    title_html  VARCHAR(400) NOT NULL COMMENT 'Allowed tags: <em>, <br>',
    subtitle    VARCHAR(400) NOT NULL,
    image       VARCHAR(255) NOT NULL,
    cta_label   VARCHAR(60)  NOT NULL DEFAULT 'Explore Journeys',
    cta_url     VARCHAR(255) NOT NULL DEFAULT 'journeys',
    sort_order  SMALLINT NOT NULL DEFAULT 0,
    is_active   TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Wellness retreats
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS retreats (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug         VARCHAR(140) NOT NULL,
    title        VARCHAR(160) NOT NULL,
    duration     VARCHAR(60)  NOT NULL,
    summary      VARCHAR(300) NOT NULL,
    description  MEDIUMTEXT NULL,
    image        VARCHAR(255) NOT NULL,
    is_featured  TINYINT(1) NOT NULL DEFAULT 1,
    sort_order   SMALLINT NOT NULL DEFAULT 0,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_retreats_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Journeys / partner stays
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS journeys (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug         VARCHAR(140) NOT NULL,
    title        VARCHAR(160) NOT NULL,
    path         ENUM('rest','soul','healing') NOT NULL,
    region       VARCHAR(80) NULL,
    price_from   DECIMAL(10,2) NOT NULL DEFAULT 0,
    summary      VARCHAR(300) NOT NULL,
    description  MEDIUMTEXT NULL,
    image        VARCHAR(255) NOT NULL,
    is_featured  TINYINT(1) NOT NULL DEFAULT 1,
    sort_order   SMALLINT NOT NULL DEFAULT 0,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_journeys_slug (slug),
    KEY idx_journeys_path (path)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------
-- Journal / news posts
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS posts (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug          VARCHAR(160) NOT NULL,
    type          ENUM('journal','news') NOT NULL DEFAULT 'journal',
    category      VARCHAR(80)  NOT NULL,
    title         VARCHAR(200) NOT NULL,
    excerpt       VARCHAR(300) NOT NULL,
    body          MEDIUMTEXT NULL,
    image         VARCHAR(255) NOT NULL,
    status        ENUM('draft','published') NOT NULL DEFAULT 'draft',
    published_at  DATETIME NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_posts_slug (slug),
    KEY idx_posts_status (status, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===============================================================
-- Seed content (mirrors the approved homepage design)
-- ===============================================================
INSERT INTO hero_slides (eyebrow, title_html, subtitle, image, cta_label, cta_url, sort_order) VALUES
('Mārghā Wellness · Sri Lanka & the Maldives',
 'Travel is not just <em>an escape</em>,<br>It is the <em>shortest</em> route<br>back to <em>yourself</em>.',
 'Thoughtfully curated wellness holidays  in Sri Lanka, for deep rest and meaningful transformation,designed so you come back different.',
 'hero-meditation.jpg', 'Explore Journeys', 'journeys', 1);

INSERT INTO retreats (slug, title, duration, summary, image, sort_order) VALUES
('dark-sky-retreat',     'Dark Sky Retreat',     '5 Days / 4 Nights', 'Sri Lanka — stillness, starry nights and deep rest.', 'retreat-dark-sky.jpg', 1),
('ocean-breath-retreat', 'Ocean breath retreat', '5 Days / 4 Nights', 'Sri Lanka — ayurvedic rhythm and ocean air.',          'retreat-ocean-breath.jpg', 2),
('couples-retreat',      'Couples retreat',      '5 Days / 4 Nights', 'Sri Lanka — reconnection, ritual and rest.',           'retreat-couples.jpg', 3);

INSERT INTO journeys (slug, title, path, price_from, summary, image, sort_order) VALUES
('tree-of-life-nature-resort',   'Tree of life nature resort',   'rest',    210, 'Lorem ipsum dolor sit amet consectetur. Eget mus sit donec mi velit diam id rhoncus vitae. Blandit.', 'journey-tree-of-life.jpg', 1),
('south-coast-ayurveda-partner', 'South coast ayurveda partner', 'healing', 170, 'Lorem ipsum dolor sit amet consectetur. Eget mus sit donec mi velit diam id rhoncus vitae. Blandit.', 'journey-south-coast.jpg', 2),
('tree-of-life-nature-resort-2', 'Tree of life nature resort',   'soul',    210, 'Lorem ipsum dolor sit amet consectetur. Eget mus sit donec mi velit diam id rhoncus vitae. Blandit.', 'journey-maldives.jpg', 3);

INSERT INTO posts (slug, type, category, title, excerpt, image, status, published_at) VALUES
('ayurveda-vs-hela-wedakama',   'journal', 'Ayurveda & Education', 'Ayurveda vs. Hela wedakama — what''s the difference', 'A grounded look at Sri Lanka''s two healing traditions', 'journal-1.jpg', 'published', '2026-11-03 09:00:00'),
('ayurveda-vs-hela-wedakama-2', 'journal', 'Ayurveda & Education', 'Ayurveda vs. Hela wedakama — what''s the difference', 'A grounded look at Sri Lanka''s two healing traditions', 'journal-2.jpg', 'published', '2026-11-02 09:00:00'),
('ayurveda-vs-hela-wedakama-3', 'journal', 'Ayurveda & Education', 'Ayurveda vs. Hela wedakama — what''s the difference', 'A grounded look at Sri Lanka''s two healing traditions', 'journal-3.jpg', 'published', '2026-11-01 09:00:00');

-- ===============================================================
-- Admin panel tables (same as database/admin.sql)
-- ===============================================================
-- Admin accounts. The first account is created at /admin/setup.php.
CREATE TABLE IF NOT EXISTS admin_users (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username       VARCHAR(60)  NOT NULL,
    name           VARCHAR(120) NOT NULL,
    email          VARCHAR(190) NULL,
    password_hash  VARCHAR(255) NOT NULL,
    last_login_at  DATETIME NULL,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_admin_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Failed login attempts, used to slow down password guessing.
CREATE TABLE IF NOT EXISTS admin_login_attempts (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip_address    VARCHAR(45) NOT NULL,
    username      VARCHAR(60) NULL,
    attempted_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_login_attempts_ip (ip_address, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Editable page text (Admin › Page text). Missing keys use the defaults in includes/settings.php.
CREATE TABLE IF NOT EXISTS settings (
    setting_key    VARCHAR(100) NOT NULL PRIMARY KEY,
    setting_value  MEDIUMTEXT NULL,
    updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
