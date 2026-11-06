<?php
declare(strict_types=1);

/**
 * Single source of truth for every page route.
 * slug => [label, title]. Any slug listed here resolves via page.php
 * (clean URL /slug) — pages without a template in /pages show the
 * "coming soon" layout until they are built.
 */
const PAGES = [
    'journeys'       => ['Journeys',            'Journeys'],
    'retreats'       => ['Retreats',            'Wellness Retreats'],
    'journal'        => ['Journal',             'The Journal'],
    'news'           => ['News',                'News'],
    'our-story'      => ['Our Story',           'Our Story'],
    'enquire'        => ['Enquire Now',         'Enquire'],
    'wellness-holidays' => ['Wellness Holidays', 'Wellness Holidays'],
    'sri-lanka'      => ['Sri Lanka',           'Sri Lanka: Your Destination'],
    'our-approach'   => ['Our approach',        'Our Approach'],
    'faqs'           => ['FAQs',                'Frequently Asked Questions'],
    'sustainability' => ['Sustainability',      'Sustainability'],
    'terms'          => ['Terms and Conditions','Terms and Conditions'],
    'privacy'        => ['Privacy Policy',      'Privacy Policy'],
];

/** Main header navigation (order as in the design). */
const MAIN_NAV = ['journeys', 'retreats', 'journal', 'news', 'our-story'];

/**
 * Header menu items shown as plain text (not clickable) until their page is built.
 * Remove a slug from this list to make that menu item a link again.
 */
const NAV_NOT_CLICKABLE = ['journeys', 'retreats', 'journal', 'news', 'our-story'];

/** Footer link columns: heading => [slug => label]. */
const FOOTER_NAV = [
    'COMPANY'  => ['journeys' => 'Journeys', 'retreats' => 'Retreats', 'journal' => 'Journal', 'news' => 'News', 'our-story' => 'Our Story'],
    'Platform' => ['our-approach' => 'Our approach', 'our-story' => 'Our story', 'faqs' => 'FAQs', 'sustainability' => 'Sustainability'],
];
