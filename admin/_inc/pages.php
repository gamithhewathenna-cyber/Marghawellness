<?php
declare(strict_types=1);

defined('ADMIN') || exit;

/**
 * The admin "Pages" menu: one entry per website page, each split into its
 * sections in the order they appear on the site.
 *
 * A section can have:
 *   settings  – a group key from settings_schema() (text, headings, images)
 *   list      – an entity key from admin_entities() (cards shown in that section)
 *   list_note – how the website picks items from that list
 *   anchor    – element id on the page, for the "View on website" link
 *
 * To add a page later (e.g. Journeys), add an entry here; its settings
 * groups go in includes/settings.php.
 */
function admin_pages(): array
{
    return [
        'home' => [
            'label' => 'Home',
            'url'   => '',
            'intro' => 'Every section of the homepage, from top to bottom.',
            'sections' => [
                'hero'        => ['label' => 'Hero banner', 'list' => 'hero_slides', 'anchor' => 'main',
                                  'intro' => 'The large image and headline at the very top of the page.',
                                  'list_note' => 'Active slides are shown in display order. Add a second active slide and the slider arrows appear automatically.'],
                'margha_way'  => ['label' => 'The Mārghā Way', 'settings' => 'margha_way', 'anchor' => 'mw-heading',
                                  'intro' => 'Introduction and the four pillars with icons.'],
                'wellness'    => ['label' => 'Wellness holidays', 'settings' => 'wellness', 'list' => 'retreats', 'anchor' => 'wellness-heading',
                                  'intro' => 'Text on the left and the scrolling retreat cards on the right.',
                                  'list_note' => 'Shows up to 8 retreats marked “Show on the homepage”, in display order.'],
                'process'     => ['label' => 'How it comes together', 'settings' => 'process', 'anchor' => 'process-heading',
                                  'intro' => 'The dark green section with the four steps.'],
                'journeys'    => ['label' => 'Journeys', 'settings' => 'journeys', 'list' => 'journeys', 'anchor' => 'journeys-heading',
                                  'intro' => 'Introduction, path filters and journey cards.',
                                  'list_note' => 'Shows up to 6 journeys marked “Show on the homepage”, in display order. The filter buttons use each journey’s path.'],
                'destination' => ['label' => 'Destination', 'settings' => 'destination', 'anchor' => 'destination-heading',
                                  'intro' => 'Sri Lanka text with the large photo.'],
                'journal'     => ['label' => 'From the journal', 'settings' => 'journal', 'list' => 'posts', 'anchor' => 'journal-heading',
                                  'intro' => 'Heading and the carousel of journal posts.',
                                  'list_note' => 'Shows the 9 newest posts with status “Published”.'],
                'founders'    => ['label' => 'Founders', 'settings' => 'founders',
                                  'intro' => 'The two founder stories with photos.'],
                'cta'         => ['label' => 'Call to action', 'settings' => 'cta', 'anchor' => 'cta-heading',
                                  'intro' => 'The “Your Wellness Journey begins here” band near the bottom.'],
                'seo'         => ['label' => 'SEO (Google)', 'settings' => 'seo_home',
                                  'intro' => 'How the homepage appears in the browser tab and in Google results.'],
            ],
        ],
        'site' => [
            'label' => 'Header & footer',
            'url'   => '',
            'intro' => 'Content that appears on every page of the website.',
            'sections' => [
                'footer'  => ['label' => 'Footer newsletter', 'settings' => 'footer', 'anchor' => 'newsletter',
                              'intro' => 'The two newsletter columns at the top of the footer.'],
                'general' => ['label' => 'Contact & social links', 'settings' => 'general',
                              'intro' => 'Email, social media links and the footer tagline.'],
                'seo'     => ['label' => 'SEO (Google)', 'settings' => 'seo_site',
                              'intro' => 'Fallback description for pages that don’t have their own.'],
            ],
        ],
    ];
}

/** Find the page + section that edits a settings group or a list (for breadcrumbs / redirects). */
function admin_page_for(string $kind, string $key): ?array
{
    foreach (admin_pages() as $pageKey => $page) {
        foreach ($page['sections'] as $sectionKey => $section) {
            if (($section[$kind] ?? null) === $key) {
                return ['page' => $pageKey, 'section' => $sectionKey, 'label' => $page['label'] . ' › ' . $section['label']];
            }
        }
    }
    return null;
}
