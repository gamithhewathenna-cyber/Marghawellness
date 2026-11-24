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
    city          VARCHAR(80)  NULL,
    journey_stage VARCHAR(200) NULL,
    travel_month  VARCHAR(40)  NULL,
    dates_flexible TINYINT(1)  NOT NULL DEFAULT 0,
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
    role           ENUM('admin','viewer') NOT NULL DEFAULT 'admin',
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

-- ===============================================================
-- Wellness Packages + Package Categories (same as database/wellness-packages.sql)
-- ===============================================================
-- 1. Packages table (created here only if it does not exist yet)
CREATE TABLE IF NOT EXISTS wellness_programmes (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug         VARCHAR(140) NOT NULL,
    title        VARCHAR(160) NOT NULL,
    depth        VARCHAR(60)  NOT NULL DEFAULT 'begin',
    path         VARCHAR(60)  NOT NULL DEFAULT 'rest',
    audience     VARCHAR(255) NOT NULL DEFAULT 'solo',
    nights       VARCHAR(40)  NOT NULL DEFAULT '',
    image        VARCHAR(255) NOT NULL DEFAULT '',
    summary      VARCHAR(300) NOT NULL DEFAULT '',
    description  MEDIUMTEXT NULL,
    is_active    TINYINT(1) NOT NULL DEFAULT 1,
    sort_order   SMALLINT NOT NULL DEFAULT 0,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_wellness_programmes_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Package page content + categories that can be added in the admin
ALTER TABLE wellness_programmes
    MODIFY depth    VARCHAR(60)  NOT NULL DEFAULT 'begin',
    MODIFY path     VARCHAR(60)  NOT NULL DEFAULT 'rest',
    MODIFY audience VARCHAR(255) NOT NULL DEFAULT 'solo',
    ADD COLUMN hero_image      VARCHAR(255) NULL AFTER image,
    ADD COLUMN headline        VARCHAR(200) NULL AFTER summary,
    ADD COLUMN fit_for         TEXT NULL AFTER description,
    ADD COLUMN fit_not         TEXT NULL AFTER fit_for,
    ADD COLUMN week            TEXT NULL AFTER fit_not,
    ADD COLUMN sample_day      TEXT NULL AFTER week,
    ADD COLUMN included        TEXT NULL AFTER sample_day,
    ADD COLUMN not_included    TEXT NULL AFTER included,
    ADD COLUMN addons          TEXT NULL AFTER not_included,
    ADD COLUMN price_from      VARCHAR(80)  NULL AFTER addons,
    ADD COLUMN price_basis     VARCHAR(120) NULL AFTER price_from,
    ADD COLUMN price_details   TEXT NULL AFTER price_basis,
    ADD COLUMN reading         TEXT NULL AFTER price_details,
    ADD COLUMN seo_title       VARCHAR(160) NULL AFTER reading,
    ADD COLUMN seo_description VARCHAR(300) NULL AFTER seo_title;

-- 3. Starter packages (skipped if they already exist)
INSERT IGNORE INTO wellness_programmes (slug, title, depth, path, audience, nights, image, summary, sort_order) VALUES
('heal',             'Heal',               'begin',   'healing', 'solo,women,couples', '3-5', 'retreat-ocean-breath.jpg', 'Full programme details coming soon.', 1),
('uplift',           'Uplift',             'begin',   'soul',    'solo,women',         '3-5', 'journal-2.jpg',            'Full programme details coming soon.', 2),
('unplug-reconnect', 'Unplug & Reconnect', 'begin',   'rest',    'solo,couples,teams', '3-5', 'retreat-dark-sky.jpg',     'Full programme details coming soon.', 3),
('arogya',           'Arogya',             'deepen',  'healing', 'solo,couples',       '7',   'journey-south-coast.jpg',  'Full programme details coming soon.', 4),
('revive',           'Revive',             'immerse', 'rest',    'solo,women',         '14',  'journey-tree-of-life.jpg', 'Full programme details coming soon.', 5),
('longevity',        'Longevity',          'immerse', 'healing', 'solo,couples',       '21',  'journal-1.jpg',            'Full programme details coming soon.', 6);

-- 4. Arogya's full package page (only fills it in if it has not been written yet)
UPDATE wellness_programmes SET
    summary      = 'The Ayurvedic week. A full programme under a partner physician’s consultation, adjusted day by day.',
    headline     = 'A week to actually feel the difference.',
    description  = 'Arogya is for the person who has tried quick fixes and is ready for a proper programme. It starts with a consultation, not a menu. Your partner physician decides what the week contains, then adjusts it as your body responds.',
    fit_for      = 'You are burnt out, sleeping badly or running on stress\nA weekend away has not been enough\nYou want treatment set for you, not chosen by you',
    fit_not      = 'You want a spa break with a pool and no consultation\nYou have an acute medical condition (speak to your doctor first)\nYou would rather move through the island (see Journeys)',
    week         = 'Day 1 | Arrive | Private transfer, a quiet room, a first conversation with your partner physician. Nothing is scheduled that you did not agree to.\nDay 2 | The programme is set | Your physician sets the programme from the consultation. Gentle movement, food built for you, early sleep.\nDays 3 to 5 | Treatment builds | Daily bodywork and therapies, adjusted each morning according to how you responded.\nDay 6 | Stillness | A lighter day: breathwork, meditation, silence in the garden. The empty hour that feels harder than it sounds.\nDay 7 | Plan the return | A final consultation. You leave with a practice for home and a way to reach your practitioner afterwards.',
    sample_day   = '06:00 | Light arrives before the alarm. There is no alarm.\n07:00 | Gentle yoga and breathwork, adjusted to your body that morning.\n09:00 | Treatment as set by your physician.\n11:00 | An empty hour. Deliberately.\n13:00 | Food chosen for your constitution.\n16:00 | Short check-in. Small adjustments.\n20:30 | Early dark, early sleep.',
    included     = 'Seven nights at [property TBC]\nInitial and final physician consultations\nDaily treatments as prescribed [count: VERIFY]\nDaily yoga, breathwork and meditation\nAll meals built around your programme\nPrivate airport transfers\nOne MĀRGHĀ contact throughout\nFollow-up after you return',
    not_included = 'International flights\nTravel insurance\nOptional add-ons\nPersonal expenses',
    addons       = 'Private yoga or meditation session\nExtra bodywork\nAn excursion day\nExtend your stay',
    price_from   = 'USD ———',
    price_basis  = 'per person, twin-share',
    price_details= 'Single supplement [TBC]\nDeposit [25–30%: TBC] secures your dates',
    reading      = 'Before | What an Ayurvedic consultation actually involves.\nDuring | Four minutes of breathwork for the middle of a hard day.\nAfter | How to keep a programme going at home.'
WHERE slug = 'arogya' AND (headline IS NULL OR headline = '');

-- 5. Package Categories (Admin › Package Categories)
CREATE TABLE IF NOT EXISTS wellness_categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type        ENUM('depth','path','audience') NOT NULL,
    slug        VARCHAR(60) NOT NULL,
    name        VARCHAR(80) NOT NULL,
    sort_order  SMALLINT NOT NULL DEFAULT 0,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_wellness_categories_type_slug (type, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO wellness_categories (type, slug, name, sort_order) VALUES
('depth', 'begin', 'Begin', 1), ('depth', 'deepen', 'Deepen', 2), ('depth', 'immerse', 'Immerse', 3),
('path', 'rest', 'Rest', 1), ('path', 'soul', 'Soul', 2), ('path', 'healing', 'Healing', 3),
('audience', 'solo', 'Solo', 1), ('audience', 'women', 'Women', 2), ('audience', 'couples', 'Couples', 3), ('audience', 'teams', 'Teams', 4);

-- Wellness Package page, new layout (same as database/wellness-packages-2.sql)
ALTER TABLE wellness_programmes
    ADD COLUMN facts             TEXT NULL AFTER summary,
    ADD COLUMN at_a_glance       TEXT NULL AFTER facts,
    ADD COLUMN faq               TEXT NULL AFTER reading,
    ADD COLUMN related           VARCHAR(255) NULL AFTER faq,
    ADD COLUMN practitioner_show TINYINT(1) NOT NULL DEFAULT 0 AFTER related,
    ADD COLUMN practitioner      VARCHAR(300) NULL AFTER practitioner_show;

-- Arogya: first package, with the new page content (only fills empty fields)
UPDATE wellness_programmes SET
    sort_order  = 0,
    facts       = IF(facts IS NULL OR facts = '', 'Depth | Deepen\nNights | 7\nPath | Healing\nShape | You stay, one place\nLed by | Partner Ayurvedic physician\nSeason | [VERIFY]', facts),
    at_a_glance = IF(at_a_glance IS NULL OR at_a_glance = '', 'Nights | 7\nGroup | Private, 1–2 guests\nDates | Rolling · [TBC]\nWhere | [Partner property]\nTreatments by | Partner physician', at_a_glance),
    faq         = IF(faq IS NULL OR faq = '', 'Is this suitable if I’m new to wellness travel? | Yes. Arogya begins with a consultation, so no prior knowledge is needed. If a shorter first step suits you better, we may suggest a Begin programme such as Heal.\nWhat if I have a health condition or take medication? | Tell us in the first conversation. Your health form goes to the practitioner, who may adjust or decline treatments.\nCan I travel solo? | Yes. Arogya is private, and you are never expected to join a group.\nWhat happens if I need to change dates? | Our change policy is being finalised. Please ask us in your first conversation.', faq),
    related     = IF(related IS NULL OR related = '', 'heal, revive', related),
    week        = IF(week IS NULL OR week = '' OR week LIKE 'Day 1 | Arrive | Private transfer%',
                     '1 | Arrive | Arrive and be received | Private transfer, a quiet room, a first conversation with your partner physician. Nothing is scheduled that you did not agree to. | Consultation · assessment\n2 | Settle | The programme is set | Your physician sets the programme from the consultation. Gentle movement, food built for you, early sleep. | Practitioner-set\n3–5 | Treat | Treatment builds | Daily bodywork and therapies, adjusted each morning according to how you responded. | Adjusted daily\n6 | Deepen | Stillness | A lighter day: breathwork, meditation, silence in the garden. The empty hour that feels harder than it sounds. | Breathwork · silence\n7 | Return | Plan the return | A final consultation. You leave with a practice for home and a way to reach your practitioner afterwards. | Follow-up booked',
                     week)
WHERE slug = 'arogya';

-- Journeys page journey cards (same as database/journeys.sql)
ALTER TABLE journeys
    ADD COLUMN depth            VARCHAR(40)  NOT NULL DEFAULT 'first-steps' AFTER title,
    ADD COLUMN style            VARCHAR(160) NULL AFTER path,
    ADD COLUMN tagline          VARCHAR(160) NULL AFTER style,
    ADD COLUMN route            VARCHAR(200) NULL AFTER tagline,
    ADD COLUMN nights           VARCHAR(40)  NULL AFTER route,
    ADD COLUMN effort           VARCHAR(20)  NOT NULL DEFAULT 'gentle' AFTER nights,
    ADD COLUMN group_size       VARCHAR(80)  NULL AFTER effort,
    ADD COLUMN wellness         VARCHAR(160) NULL AFTER group_size,
    ADD COLUMN price_pp         VARCHAR(80)  NULL AFTER price_from,
    ADD COLUMN on_journeys_page TINYINT(1) NOT NULL DEFAULT 1 AFTER is_featured;

-- 2. Keep the old lorem-ipsum placeholder journeys off the new Journeys page (they stay in the database)
UPDATE journeys SET on_journeys_page = 0 WHERE summary LIKE 'Lorem ipsum%';

-- 3. Starter journeys from the approved design (skipped if they already exist). Replace the [VERIFY] values in the admin.
INSERT IGNORE INTO journeys (slug, title, depth, path, style, tagline, route, nights, effort, group_size, wellness, price_from, summary, image, is_featured, on_journeys_page, sort_order) VALUES
('womens-circle-getaway', 'The Women’s Circle Getaway', 'first-steps',     'rest',    'womens-group',         'Five nights, six women',        'Negombo → Kitulgala → Galle / Ahangama',    '5',            'gentle',    'Women’s group of six',   'Breathwork · water · bodywork',        0, 'The pilot. A small group moving from coast to river forest to the south coast, with breathwork, water and unhurried company.', 'retreat-couples.jpg',      0, 1, 10),
('the-mountain-path',     'The Mountain Path',          'finding-footing', 'soul',    'solo,couples,friends', 'Tea country on foot',           'Kandy → Pekoe Trail → Riverston',           '7 · [VERIFY]', 'active',    'Small group',            'Breathwork at altitude · silence',     0, 'Walking days through tea country, a night above the cloud line, and silence at dawn. Built on the Pekoe Trail.',              'journey-tree-of-life.jpg', 0, 1, 11),
('the-dark-sky-path',     'The Dark-Sky Path',          'finding-footing', 'soul',    'solo,couples,friends', 'No light for forty kilometres', 'Highlands → Dark-sky camp',                 '6 · [VERIFY]', 'active',    'Small group',            'Stillness · stargazing · fire',        0, 'A night camp where the sky isn’t washed out. Designed around the idea that awe is a kind of medicine.',                       'retreat-dark-sky.jpg',     0, 1, 12),
('the-forest-path',       'The Forest Path',            'going-deeper',    'healing', 'solo,couples,friends', 'Rainforest, slowly',            'Deniyaya forest retreat → Sinharaja edge',   '8 · [VERIFY]', 'demanding', 'Small group',            'Forest therapy · practitioner finale', 0, 'Days inside the forest with local naturalists, forest therapy walks and village meals, ending with bodywork.',              'journey-south-coast.jpg',  0, 1, 13),
('the-soul-path',         'The Soul Path',              'going-deeper',    'soul',    'solo,couples,friends', 'Culture, ritual, silence',      'Cultural heartland → hill temples → coast', '9 · [VERIFY]', 'active',    'Small group or private', 'Meditation · teacher-led practice',    0, 'For those drawn to the spiritual side: temple mornings, teachers, meditation and long quiet stretches.',                  'destination-sigiriya.jpg', 0, 1, 14),
('the-ocean-path',        'The Ocean Path',             'going-deeper',    'rest',    'solo,couples,friends', 'Water, sunrise, surf',          'South coast → Mirissa → Galle',             '7 · [VERIFY]', 'active',    'Small group or private', 'Sunrise yoga · bodywork',              0, 'Sunrise yoga by the waves, mindful surfing and Ayurveda bodywork at the end of the day.',                                  'journey-maldives.jpg',     0, 1, 15);
