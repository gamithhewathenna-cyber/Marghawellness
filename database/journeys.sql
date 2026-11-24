-- MĀRGHĀ Wellness — Journeys page (journey cards)
-- Run ONCE in phpMyAdmin › (select database) › SQL. No data is deleted.
-- If you see "Duplicate column name 'depth'", this update has already been applied.

SET NAMES utf8mb4;

-- 1. Journey card fields on the existing journeys table
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
