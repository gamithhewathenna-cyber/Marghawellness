<?php
declare(strict_types=1);

/**
 * Content repository for the homepage.
 * Each getter reads from MySQL; if the DB is not configured or the table
 * is empty it falls back to the default content below (matches the design).
 */

function get_hero_slides(): array
{
    $rows = Database::fetchAll(
        'SELECT eyebrow, title_html, subtitle, image, cta_label, cta_url
           FROM hero_slides WHERE is_active = 1 ORDER BY sort_order, id'
    );
    if ($rows) {
        return $rows;
    }
    return [[
        'eyebrow'    => 'Mārghā Wellness · Sri Lanka & the Maldives',
        'title_html' => 'Travel is not just <em>an escape</em>,<br>It is the <em>shortest</em> route<br>back to <em>yourself</em>.',
        'subtitle'   => 'Thoughtfully curated wellness holidays  in Sri Lanka, for deep rest and meaningful transformation,designed so you come back different.',
        'image'      => 'hero-meditation.jpg',
        'cta_label'  => 'Explore Journeys',
        'cta_url'    => 'journeys',
    ]];
}

function get_retreats(): array
{
    $rows = Database::fetchAll(
        'SELECT slug, title, duration, summary, image
           FROM retreats WHERE is_featured = 1 ORDER BY sort_order, id LIMIT 8'
    );
    if ($rows) {
        return $rows;
    }
    return [
        ['slug' => 'dark-sky-retreat',     'title' => 'Dark Sky Retreat',     'duration' => '5 Days / 4 Nights', 'summary' => 'Sri Lanka — stillness, starry nights and deep rest.',  'image' => 'retreat-dark-sky.jpg'],
        ['slug' => 'ocean-breath-retreat', 'title' => 'Ocean breath retreat', 'duration' => '5 Days / 4 Nights', 'summary' => 'Sri Lanka — ayurvedic rhythm and ocean air.',           'image' => 'retreat-ocean-breath.jpg'],
        ['slug' => 'couples-retreat',      'title' => 'Couples retreat',      'duration' => '5 Days / 4 Nights', 'summary' => 'Sri Lanka — reconnection, ritual and rest.',            'image' => 'retreat-couples.jpg'],
    ];
}

function get_journey_paths(): array
{
    return [
        'rest'    => 'The rest path',
        'soul'    => 'The soul path',
        'healing' => 'The healing path',
    ];
}

function get_journeys(): array
{
    $rows = Database::fetchAll(
        'SELECT slug, title, path, price_from, summary, image
           FROM journeys WHERE is_featured = 1 ORDER BY sort_order, id LIMIT 6'
    );
    if ($rows) {
        return $rows;
    }
    $lorem = 'Lorem ipsum dolor sit amet consectetur. Eget mus sit donec mi velit diam id rhoncus vitae. Blandit.';
    return [
        ['slug' => 'tree-of-life-nature-resort',  'title' => 'Tree of life nature resort',  'path' => 'rest',    'price_from' => 210, 'summary' => $lorem, 'image' => 'journey-tree-of-life.jpg'],
        ['slug' => 'south-coast-ayurveda-partner','title' => 'South coast ayurveda partner','path' => 'healing', 'price_from' => 170, 'summary' => $lorem, 'image' => 'journey-south-coast.jpg'],
        ['slug' => 'tree-of-life-nature-resort-2','title' => 'Tree of life nature resort',  'path' => 'soul',    'price_from' => 210, 'summary' => $lorem, 'image' => 'journey-maldives.jpg'],
    ];
}

function get_posts(): array
{
    $rows = Database::fetchAll(
        'SELECT slug, category, title, excerpt, image
           FROM posts WHERE status = "published" ORDER BY published_at DESC, id DESC LIMIT 9'
    );
    if ($rows) {
        return $rows;
    }
    $p = ['category' => 'Ayurveda & Education', 'title' => 'Ayurveda vs. Hela wedakama — what\'s the difference', 'excerpt' => 'A grounded look at Sri Lanka\'s two healing traditions'];
    return [
        $p + ['slug' => 'ayurveda-vs-hela-wedakama',   'image' => 'journal-1.jpg'],
        $p + ['slug' => 'ayurveda-vs-hela-wedakama-2', 'image' => 'journal-2.jpg'],
        $p + ['slug' => 'ayurveda-vs-hela-wedakama-3', 'image' => 'journal-3.jpg'],
    ];
}

/* ==========================================================================
   Wellness Packages (Admin › Wellness Packages) and their categories
   (Admin › Package Categories). Each getter falls back to built-in starter
   content until database/wellness-packages.sql has been run.
   ========================================================================== */

/** Category types: key => [admin label, plural label for filters]. */
const WELLNESS_CATEGORY_TYPES = [
    'depth'    => ['Depth', 'All depths'],
    'path'     => ['Path', 'All paths'],
    'audience' => ['Suitable for', 'Everyone'],
];

/** Categories of one type as slug => name, in display order. */
function get_wellness_categories(string $type): array
{
    static $all = null;
    if ($all === null) {
        $all  = [];
        $rows = Database::fetchAll('SELECT type, slug, name FROM wellness_categories ORDER BY sort_order, id');
        if ($rows) {
            foreach ($rows as $r) {
                $all[$r['type']][$r['slug']] = $r['name'];
            }
        } else {
            $all = [
                'depth'    => ['begin' => 'Begin', 'deepen' => 'Deepen', 'immerse' => 'Immerse'],
                'path'     => ['rest' => 'Rest', 'soul' => 'Soul', 'healing' => 'Healing'],
                'audience' => ['solo' => 'Solo', 'women' => 'Women', 'couples' => 'Couples', 'teams' => 'Teams'],
            ];
        }
    }
    return $all[$type] ?? [];
}

/** Display name of a category (falls back to the slug, nicely cased). */
function wellness_category_name(string $type, string $slug): string
{
    return get_wellness_categories($type)[$slug] ?? ucwords(str_replace('-', ' ', $slug));
}

/** Link to a package page. Works without server rewrite rules. */
function package_url(string $slug): string
{
    return url('wellness/package.php?p=' . rawurlencode($slug));
}

/** Starter packages, used until the database table exists. Arogya includes its full page. */
function wellness_fallback_packages(): array
{
    $soon = 'Full programme details coming soon.';
    $base = fn(array $p) => $p + ['hero_image' => '', 'headline' => '', 'description' => '', 'facts' => '', 'at_a_glance' => '', 'fit_for' => '', 'fit_not' => '',
        'week' => '', 'sample_day' => '', 'included' => '', 'not_included' => '', 'addons' => '', 'price_from' => '', 'price_basis' => '',
        'price_details' => '', 'faq' => '', 'related' => '', 'practitioner' => '', 'practitioner_show' => 0, 'reading' => '',
        'seo_title' => '', 'seo_description' => '', 'is_active' => 1];
    return array_map($base, [
        // Arogya — the first fully written package (shown first)
        ['slug' => 'arogya', 'title' => 'Arogya', 'depth' => 'deepen', 'path' => 'healing', 'audience' => 'solo,couples', 'nights' => '7', 'image' => 'journey-south-coast.jpg',
         'summary'      => 'The Ayurvedic week. A full programme under a partner physician’s consultation, adjusted day by day.',
         'headline'     => 'A week to actually feel the difference.',
         'description'  => 'Arogya is for the person who has tried quick fixes and is ready for a proper programme. It starts with a consultation, not a menu. Your partner physician decides what the week contains, then adjusts it as your body responds.',
         'facts'        => "Depth | Deepen\nNights | 7\nPath | Healing\nShape | You stay, one place\nLed by | Partner Ayurvedic physician\nSeason | [VERIFY]",
         'at_a_glance'  => "Nights | 7\nGroup | Private, 1–2 guests\nDates | Rolling · [TBC]\nWhere | [Partner property]\nTreatments by | Partner physician",
         'fit_for'      => "You are burnt out, sleeping badly or running on stress\nA weekend away has not been enough\nYou want treatment set for you, not chosen by you",
         'fit_not'      => "You want a spa break with a pool and no consultation\nYou have an acute medical condition (speak to your doctor first)\nYou would rather move through the island (see Journeys)",
         'week'         => "1 | Arrive | Arrive and be received | Private transfer, a quiet room, a first conversation with your partner physician. Nothing is scheduled that you did not agree to. | Consultation · assessment\n2 | Settle | The programme is set | Your physician sets the programme from the consultation. Gentle movement, food built for you, early sleep. | Practitioner-set\n3–5 | Treat | Treatment builds | Daily bodywork and therapies, adjusted each morning according to how you responded. | Adjusted daily\n6 | Deepen | Stillness | A lighter day: breathwork, meditation, silence in the garden. The empty hour that feels harder than it sounds. | Breathwork · silence\n7 | Return | Plan the return | A final consultation. You leave with a practice for home and a way to reach your practitioner afterwards. | Follow-up booked",
         'sample_day'   => "06:00 | Light arrives before the alarm. There is no alarm.\n07:00 | Gentle yoga and breathwork, adjusted to your body that morning.\n09:00 | Treatment as set by your physician.\n11:00 | An empty hour. Deliberately.\n13:00 | Food chosen for your constitution.\n16:00 | Short check-in. Small adjustments.\n20:30 | Early dark, early sleep.",
         'included'     => "Seven nights at [property TBC]\nInitial and final physician consultations\nDaily treatments as prescribed [count: VERIFY]\nDaily yoga, breathwork and meditation\nAll meals built around your programme\nPrivate airport transfers\nOne MĀRGHĀ contact throughout\nFollow-up after you return",
         'not_included' => "International flights\nTravel insurance\nOptional add-ons\nPersonal expenses",
         'addons'       => "Private yoga or meditation session\nExtra bodywork\nAn excursion day\nExtend your stay",
         'price_from'   => 'USD ———',
         'price_basis'  => 'per person, twin-share',
         'price_details'=> "Single supplement [TBC]\nDeposit [25–30%: TBC] secures your dates",
         'faq'          => "Is this suitable if I’m new to wellness travel? | Yes. Arogya begins with a consultation, so no prior knowledge is needed. If a shorter first step suits you better, we may suggest a Begin programme such as Heal.\nWhat if I have a health condition or take medication? | Tell us in the first conversation. Your health form goes to the practitioner, who may adjust or decline treatments.\nCan I travel solo? | Yes. Arogya is private, and you are never expected to join a group.\nWhat happens if I need to change dates? | Our change policy is being finalised. Please ask us in your first conversation.",
         'related'      => 'heal, revive',
         'reading'      => "Before | What an Ayurvedic consultation actually involves.\nDuring | Four minutes of breathwork for the middle of a hard day.\nAfter | How to keep a programme going at home.",
         'sort_order'   => 0],
        ['slug' => 'heal',             'title' => 'Heal',               'depth' => 'begin',   'path' => 'healing', 'audience' => 'solo,women,couples', 'nights' => '3-5', 'image' => 'retreat-ocean-breath.jpg', 'summary' => $soon, 'sort_order' => 1],
        ['slug' => 'uplift',           'title' => 'Uplift',             'depth' => 'begin',   'path' => 'soul',    'audience' => 'solo,women',         'nights' => '3-5', 'image' => 'journal-2.jpg',            'summary' => $soon, 'sort_order' => 2],
        ['slug' => 'unplug-reconnect', 'title' => 'Unplug & Reconnect', 'depth' => 'begin',   'path' => 'rest',    'audience' => 'solo,couples,teams', 'nights' => '3-5', 'image' => 'retreat-dark-sky.jpg',     'summary' => $soon, 'sort_order' => 3],
        ['slug' => 'revive',           'title' => 'Revive',             'depth' => 'immerse', 'path' => 'rest',    'audience' => 'solo,women',         'nights' => '14',  'image' => 'journey-tree-of-life.jpg', 'summary' => $soon, 'sort_order' => 5],
        ['slug' => 'longevity',        'title' => 'Longevity',          'depth' => 'immerse', 'path' => 'healing', 'audience' => 'solo,couples',       'nights' => '21',  'image' => 'journal-1.jpg',            'summary' => $soon, 'sort_order' => 6],
    ]);
}

/**
 * "Step up, or step down" packages for a package page: the ones named in its
 * Related field, otherwise the nearest packages at the depth above and below.
 */
function get_related_packages(array $pkg, int $limit = 3): array
{
    $all = array_values(array_filter(get_wellness_programmes(), fn($p) => $p['slug'] !== $pkg['slug']));
    $wanted = array_filter(array_map('trim', explode(',', (string) ($pkg['related'] ?? ''))));
    if ($wanted) {
        $bySlug = array_column($all, null, 'slug');
        return array_slice(array_values(array_filter(array_map(fn($s) => $bySlug[$s] ?? null, $wanted))), 0, $limit);
    }
    $order = array_keys(get_wellness_categories('depth'));
    $here  = array_search($pkg['depth'], $order, true);
    $pick  = [];
    foreach ([$here - 1, $here + 1] as $i) {                 // one step down, one step up
        foreach ($all as $p) {
            if ($here !== false && isset($order[$i]) && $p['depth'] === $order[$i]) { $pick[] = $p; break; }
        }
    }
    foreach ($all as $p) {                                    // fill up with others
        if (count($pick) >= $limit) break;
        if (!in_array($p, $pick, true)) $pick[] = $p;
    }
    return array_slice($pick, 0, $limit);
}

/**
 * Week lines → [day, label, title, text, tags]. Accepts the full format
 * "1 | Arrive | Arrive and be received | text | tags" and the short one "Day 1 | Arrive | text".
 */
function package_week(string $text): array
{
    return array_map(function (array $parts) {
        if (count(array_filter($parts, 'strlen')) <= 3 && ($parts[3] ?? '') === '') {
            [$day, $title, $body] = array_pad($parts, 3, '');
            return [trim((string) preg_replace('/^days?\s*/i', '', $day)), '', $title, $body, ''];
        }
        return array_pad($parts, 5, '');
    }, text_items($text, 5));
}

/** Packages shown on the Wellness page (active only, in display order). */
function get_wellness_programmes(): array
{
    $rows = Database::fetchAll('SELECT * FROM wellness_programmes WHERE is_active = 1 ORDER BY sort_order, id');
    return $rows ?? wellness_fallback_packages();   // table exists → exactly what the admin has, even if empty
}

/** One package by its URL name, or null. Hidden packages are visible only to signed-in admins (preview). */
function get_wellness_package(string $slug): ?array
{
    if (!preg_match('/^[a-z0-9-]{1,140}$/', $slug)) {
        return null;
    }
    $rows = Database::fetchAll('SELECT * FROM wellness_programmes WHERE slug = ? LIMIT 1', [$slug]);
    if ($rows === null) {
        foreach (wellness_fallback_packages() as $p) {
            if ($p['slug'] === $slug) {
                return $p;
            }
        }
        return null;
    }
    $p = $rows[0] ?? null;
    if ($p && !$p['is_active'] && !is_admin_session()) {
        return null;
    }
    return $p;
}

/* ==========================================================================
   Journeys page (/journeys) — journey routes come from the `journeys` table
   (Admin › Listings › Journeys). Filter options are fixed below.
   ========================================================================== */
const JOURNEY_DEPTHS  = ['first-steps' => 'First steps', 'finding-footing' => 'Finding footing', 'going-deeper' => 'Going deeper'];
const JOURNEY_STYLES  = ['womens-group' => 'Women’s group', 'solo' => 'Solo', 'couples' => 'Couples', 'friends' => 'Friends'];
const JOURNEY_EFFORTS = ['gentle' => 'Gentle', 'active' => 'Active', 'demanding' => 'Demanding'];

/** Starter routes from the approved design — used until database/journeys.sql has been run. */
function journey_fallback_routes(): array
{
    $r = fn(array $j) => $j + ['price_from' => 0, 'region' => '', 'description' => '', 'is_featured' => 0];
    return array_map($r, [
        ['slug' => 'womens-circle-getaway', 'title' => 'The Women’s Circle Getaway', 'depth' => 'first-steps',     'path' => 'rest',    'tagline' => 'Five nights, six women',        'route' => 'Negombo → Kitulgala → Galle / Ahangama',      'summary' => 'The pilot. A small group moving from coast to river forest to the south coast, with breathwork, water and unhurried company.', 'nights' => '5',          'effort' => 'gentle',    'group_size' => 'Women’s group of six',  'wellness' => 'Breathwork · water · bodywork',        'style' => 'womens-group',          'image' => 'retreat-couples.jpg'],
        ['slug' => 'the-mountain-path',     'title' => 'The Mountain Path',          'depth' => 'finding-footing', 'path' => 'soul',    'tagline' => 'Tea country on foot',           'route' => 'Kandy → Pekoe Trail → Riverston',             'summary' => 'Walking days through tea country, a night above the cloud line, and silence at dawn. Built on the Pekoe Trail.',              'nights' => '7 · [VERIFY]', 'effort' => 'active',    'group_size' => 'Small group',           'wellness' => 'Breathwork at altitude · silence',     'style' => 'solo,couples,friends',  'image' => 'journey-tree-of-life.jpg'],
        ['slug' => 'the-dark-sky-path',     'title' => 'The Dark-Sky Path',          'depth' => 'finding-footing', 'path' => 'soul',    'tagline' => 'No light for forty kilometres', 'route' => 'Highlands → Dark-sky camp',                   'summary' => 'A night camp where the sky isn’t washed out. Designed around the idea that awe is a kind of medicine.',                       'nights' => '6 · [VERIFY]', 'effort' => 'active',    'group_size' => 'Small group',           'wellness' => 'Stillness · stargazing · fire',        'style' => 'solo,couples,friends',  'image' => 'retreat-dark-sky.jpg'],
        ['slug' => 'the-forest-path',       'title' => 'The Forest Path',            'depth' => 'going-deeper',    'path' => 'healing', 'tagline' => 'Rainforest, slowly',            'route' => 'Deniyaya forest retreat → Sinharaja edge',     'summary' => 'Days inside the forest with local naturalists, forest therapy walks and village meals, ending with bodywork.',              'nights' => '8 · [VERIFY]', 'effort' => 'demanding', 'group_size' => 'Small group',           'wellness' => 'Forest therapy · practitioner finale', 'style' => 'solo,couples,friends',  'image' => 'journey-south-coast.jpg'],
        ['slug' => 'the-soul-path',         'title' => 'The Soul Path',              'depth' => 'going-deeper',    'path' => 'soul',    'tagline' => 'Culture, ritual, silence',      'route' => 'Cultural heartland → hill temples → coast',   'summary' => 'For those drawn to the spiritual side: temple mornings, teachers, meditation and long quiet stretches.',                  'nights' => '9 · [VERIFY]', 'effort' => 'active',    'group_size' => 'Small group or private', 'wellness' => 'Meditation · teacher-led practice',   'style' => 'solo,couples,friends',  'image' => 'destination-sigiriya.jpg'],
        ['slug' => 'the-ocean-path',        'title' => 'The Ocean Path',             'depth' => 'going-deeper',    'path' => 'rest',    'tagline' => 'Water, sunrise, surf',          'route' => 'South coast → Mirissa → Galle',               'summary' => 'Sunrise yoga by the waves, mindful surfing and Ayurveda bodywork at the end of the day.',                                  'nights' => '7 · [VERIFY]', 'effort' => 'active',    'group_size' => 'Small group or private', 'wellness' => 'Sunrise yoga · bodywork',              'style' => 'solo,couples,friends',  'image' => 'journey-maldives.jpg'],
    ]);
}

/** Journey routes for the Journeys page, in display order. */
function get_journey_routes(): array
{
    $rows = Database::fetchAll('SELECT * FROM journeys WHERE on_journeys_page = 1 ORDER BY sort_order, id');
    // Before database/journeys.sql the column doesn't exist (query fails) → show the starter routes.
    if ($rows === null) {
        return journey_fallback_routes();
    }
    return $rows;
}
