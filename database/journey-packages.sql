-- MĀRGHĀ Wellness — Journey Packages (one page per journey)
-- Run ONCE, AFTER database/journeys.sql. No data is deleted.
-- If you see a "Duplicate column name" message, this update has already been applied.

SET NAMES utf8mb4;

-- 1. Journey page fields
ALTER TABLE journeys
    ADD COLUMN hero_image      VARCHAR(255) NULL AFTER image,
    ADD COLUMN badge           VARCHAR(120) NULL AFTER hero_image,
    ADD COLUMN hero_text       VARCHAR(300) NULL AFTER badge,
    ADD COLUMN facts           TEXT NULL AFTER hero_text,
    ADD COLUMN at_a_glance     TEXT NULL AFTER facts,
    ADD COLUMN headline        VARCHAR(200) NULL AFTER at_a_glance,
    ADD COLUMN fit_for         TEXT NULL AFTER description,
    ADD COLUMN fit_not         TEXT NULL AFTER fit_for,
    ADD COLUMN route_stops     TEXT NULL AFTER fit_not,
    ADD COLUMN week            TEXT NULL AFTER route_stops,
    ADD COLUMN included        TEXT NULL AFTER week,
    ADD COLUMN not_included    TEXT NULL AFTER included,
    ADD COLUMN good_to_know    TEXT NULL AFTER not_included,
    ADD COLUMN price_basis     VARCHAR(120) NULL AFTER price_pp,
    ADD COLUMN price_details   TEXT NULL AFTER price_basis,
    ADD COLUMN places          VARCHAR(160) NULL AFTER price_details,
    ADD COLUMN places_show     TINYINT(1) NOT NULL DEFAULT 0 AFTER places,
    ADD COLUMN faq             TEXT NULL AFTER places_show,
    ADD COLUMN related         VARCHAR(255) NULL AFTER faq,
    ADD COLUMN reading         TEXT NULL AFTER related,
    ADD COLUMN seo_title       VARCHAR(160) NULL AFTER reading,
    ADD COLUMN seo_description VARCHAR(300) NULL AFTER seo_title;

-- 2. The Women’s Circle Getaway: first journey, with its full page (only fills it in if not written yet)
UPDATE journeys SET
    sort_order    = 0,
    badge         = 'First steps · 5 nights · Group of six',
    hero_text     = 'Five nights, six women, coast to river forest to the south coast. A gentle way into moving through the island together.',
    headline      = 'A small group, a real route.',
    description   = 'Not a resort. A route, shared with five other women. Local guides, water, a forest and the sea, with wellness threaded through the days rather than bolted on.',
    facts         = 'Depth | First steps\nNights | 5\nEffort | Gentle\nShape | You move: 3 stops\nGroup | 6 guests\nDates | [TBC]',
    at_a_glance   = 'Nights | 5\nGroup | 6 women\nEffort | Gentle\nRoute | Negombo → Kitulgala → Galle / Ahangama\nDeparture | [TBC]',
    fit_for       = 'You want company, not a solo retreat\nYou are new to slow travel, or want to try it with others\nYou would like movement and water more than treatments',
    fit_not       = 'You want a long, treatment-led stay (see Wellness)\nYou prefer total privacy\nYou need a high-intensity trek (see Mountain Path)',
    route_stops   = 'Negombo | 1 night\nKitulgala | 2 nights\nGalle / Ahangama | 2 nights',
    week          = '1 | Negombo | Arrive and exhale | Meet the group and your guide at the coast. Breathwork at sunset, a long dinner and nothing to prove. | Coast · gentle\n2 | Kitulgala | Into the river forest | Drive inland to the rainforest edge. A guided forest walk and a first swim in the river. | Forest · water\n3 | Kitulgala | A day of moving | A longer walk with a local naturalist, lunch cooked by a village family, silence at dusk. This is the hard part, and the good part. | Active · the “good hard part”\n4 | Galle / Ahangama | Down to the sea | Travel south. Bodywork in the afternoon, sunset at the fort. | Coast · bodywork\n5 | Ahangama | Return, gently | Sunrise yoga by the waves, a closing circle and a practice to take home. | Closing circle',
    included      = 'Five nights [properties TBC]\nPrivate vehicle with driver\nLocal guide and naturalist-led walks\nDaily breathwork, yoga or stillness practice\nMeals as itinerary [VERIFY]\nOne bodywork session [VERIFY]\nOne MĀRGHĀ host with the group\nA follow-up circle after you return',
    not_included  = 'International flights\nTravel insurance\nOptional extra treatments\nPersonal expenses',
    good_to_know  = 'Walking days of two to four hours, easy to moderate\nMinimum group of [VERIFY] to depart\nSingle-room supplement [TBC]',
    price_basis   = 'per person, twin-share',
    price_details = 'Deposit [25–30%: TBC]\nDeparture [TBC]',
    places        = 'Six places. Places left show once dates are live.',
    places_show   = 0,
    faq           = 'How fit do I need to be? | This is a Gentle journey: walking days of two to four hours, easy to moderate. Tell us your honest level and we will match it.\nWhat if I have a health condition or take medication? | Tell us in the first conversation. Your health form goes to the practitioner, who may adjust or decline treatments.\nCan I travel solo? | Yes. Most guests join on their own, and the group is hosted by a MĀRGHĀ team member.\nWhat happens if I need to change dates? | Our change policy is being finalised. Please ask us in your first conversation.',
    related       = 'the-mountain-path, the-dark-sky-path, wellness:heal',
    reading       = 'Before | Your first wellness trip: what to pack and what to expect.\nDuring | When did you last see the Milky Way?\nAfter | Why the first group is six.'
WHERE slug = 'womens-circle-getaway' AND (headline IS NULL OR headline = '');
