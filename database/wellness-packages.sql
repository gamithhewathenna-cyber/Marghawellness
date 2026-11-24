-- MĀRGHĀ Wellness — Wellness Packages (package pages) + Package Categories
-- Run ONCE in phpMyAdmin › (select database) › SQL.
-- Works whether or not database/wellness.sql was run before. No existing data is deleted.
-- If you see "Duplicate column name 'hero_image'", this update has already been applied.

SET NAMES utf8mb4;

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
