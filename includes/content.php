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
