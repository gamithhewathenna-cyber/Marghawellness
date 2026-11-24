<?php
declare(strict_types=1);

defined('ADMIN') || exit;

/**
 * Content types managed in the admin. Each entry maps to a database table and
 * drives the list + edit screens in admin/manage.php.
 *
 * Field keys = column names. Options:
 *   type      text | rich | textarea | slug | image | int | decimal | bool | select | datetime | email | readonly
 *   required  must not be empty
 *   max       maximum characters (matches the column size)
 *   options   for select: value => label
 *   from      for slug: the field used to generate it
 *   default   value for new records
 *
 * Entity options:
 *   list        columns shown in the list (first image column shows a thumbnail)
 *   order       SQL ORDER BY for the list
 *   search      columns searched by the list search box
 *   can_create  false for records that only arrive from the website (enquiries, subscribers)
 *   export      offer a CSV download
 */
function admin_entities(): array
{
    static $entities = null;
    if ($entities !== null) {
        return $entities;
    }

    $sort = ['label' => 'Display order', 'type' => 'int', 'default' => 0, 'help' => 'Lower numbers show first.'];

    $entities = [
        'hero_slides' => [
            'label' => 'Hero slides', 'singular' => 'hero slide', 'icon' => 'slides', 'hidden' => true, // edited in Pages › Home › Hero banner
            'intro' => 'The large banner at the top of the homepage. Add a second active slide and the slider arrows appear automatically.',
            'order' => 'sort_order, id', 'title' => 'eyebrow',
            'list'  => ['image' => 'Image', 'eyebrow' => 'Small heading', 'sort_order' => 'Order', 'is_active' => 'Shown'],
            'fields' => [
                'eyebrow'    => ['label' => 'Small heading (above the title)', 'type' => 'text', 'required' => true, 'max' => 160],
                'title_html' => ['label' => 'Title', 'type' => 'rich', 'required' => true, 'max' => 400],
                'subtitle'   => ['label' => 'Text under the title', 'type' => 'textarea', 'required' => true, 'max' => 400, 'rows' => 3],
                'image'      => ['label' => 'Background image', 'type' => 'image', 'required' => true, 'max' => 255],
                'cta_label'  => ['label' => 'Button label', 'type' => 'text', 'required' => true, 'max' => 60, 'default' => 'Explore Journeys'],
                'cta_url'    => ['label' => 'Button link', 'type' => 'text', 'required' => true, 'max' => 255, 'default' => 'journeys', 'help' => 'A page on this site such as journeys or enquire, or a full https:// address.'],
                'sort_order' => $sort,
                'is_active'  => ['label' => 'Show on the website', 'type' => 'bool', 'default' => 1],
            ],
        ],
        'retreats' => [
            'label' => 'Wellness retreats', 'singular' => 'retreat', 'icon' => 'leaf', 'hidden' => true, // replaced by Wellness Packages (table kept, not shown in the admin)
            'intro' => 'Cards in the “Wellness Holidays” section of the homepage (up to 8 featured retreats).',
            'order' => 'sort_order, id', 'title' => 'title', 'search' => ['title', 'summary'],
            'list'  => ['image' => 'Image', 'title' => 'Title', 'duration' => 'Duration', 'sort_order' => 'Order', 'is_featured' => 'On homepage'],
            'fields' => [
                'title'       => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 160],
                'slug'        => ['label' => 'URL name', 'type' => 'slug', 'required' => true, 'max' => 140, 'from' => 'title'],
                'duration'    => ['label' => 'Duration', 'type' => 'text', 'required' => true, 'max' => 60, 'default' => '5 Days / 4 Nights'],
                'summary'     => ['label' => 'Short summary (card text)', 'type' => 'textarea', 'required' => true, 'max' => 300, 'rows' => 3],
                'description' => ['label' => 'Full description (for the retreat page)', 'type' => 'paras', 'rows' => 10],
                'image'       => ['label' => 'Image (portrait)', 'type' => 'image', 'required' => true, 'max' => 255],
                'is_featured' => ['label' => 'Show on the homepage', 'type' => 'bool', 'default' => 1],
                'sort_order'  => $sort,
            ],
        ],
        'journeys' => [
            'label' => 'Journey Packages', 'singular' => 'journey', 'icon' => 'map', 'placeholder_check' => true,
            'intro' => 'Each journey has its own page (e.g. The Women’s Circle Getaway) and a card on the Journeys page. Sections of the journey page only appear once they have content. Those marked “Show on the homepage” also appear in the homepage Journeys section.',
            'order' => 'sort_order, id', 'title' => 'title', 'search' => ['title', 'summary', 'region', 'route'],
            'list'  => ['image' => 'Image', 'title' => 'Journey', 'depth' => 'Depth', 'path' => 'Path', 'nights' => 'Nights', 'sort_order' => 'Order', 'on_journeys_page' => 'Shown'],
            'view'  => fn(array $row) => journey_url((string) $row['slug']),
            'fields' => [
                '_card'        => ['label' => 'Journey & card', 'type' => 'heading', 'help' => 'Shown on the Journeys page card and at the top of the journey page.'],
                'title'        => ['label' => 'Journey name', 'type' => 'text', 'required' => true, 'max' => 160],
                'slug'         => ['label' => 'URL name', 'type' => 'slug', 'required' => true, 'max' => 140, 'from' => 'title'],
                'depth'        => ['label' => 'Depth', 'type' => 'select', 'required' => true, 'options' => JOURNEY_DEPTHS],
                'path'         => ['label' => 'Path', 'type' => 'select', 'required' => true, 'options' => get_journey_paths()],
                'style'        => ['label' => 'Style (who it suits)', 'type' => 'checks', 'options' => JOURNEY_STYLES],
                'effort'       => ['label' => 'Effort', 'type' => 'select', 'required' => true, 'options' => JOURNEY_EFFORTS],
                'nights'       => ['label' => 'Nights', 'type' => 'text', 'max' => 40, 'help' => 'E.g. 5'],
                'group_size'   => ['label' => 'Group', 'type' => 'text', 'max' => 80, 'help' => 'E.g. “Small group or private”.'],
                'tagline'      => ['label' => 'Tagline', 'type' => 'text', 'max' => 160, 'help' => 'E.g. “Five nights, six women”.'],
                'route'        => ['label' => 'Route strip (card)', 'type' => 'text', 'max' => 200, 'help' => 'E.g. “Negombo → Kitulgala → Galle / Ahangama”.'],
                'wellness'     => ['label' => 'Wellness threaded', 'type' => 'text', 'max' => 160, 'help' => 'E.g. “Breathwork · water · bodywork”.'],
                'summary'      => ['label' => 'Card description', 'type' => 'textarea', 'required' => true, 'max' => 300, 'rows' => 3],
                'image'        => ['label' => 'Card photo', 'type' => 'image', 'required' => true, 'max' => 255],
                'hero_image'   => ['label' => 'Journey page photo (optional)', 'type' => 'image', 'max' => 255, 'help' => 'Large photo behind the journey page heading, about 2400px wide. Leave empty to use the card photo.'],
                'badge'        => ['label' => 'Badge on the photo', 'type' => 'text', 'max' => 120, 'help' => 'E.g. “First steps · 5 nights · Group of six”. Leave empty for depth and nights.'],
                'hero_text'    => ['label' => 'Text under the journey name', 'type' => 'textarea', 'max' => 300, 'rows' => 2, 'help' => 'Leave empty to use the card description.'],

                '_facts'       => ['label' => 'Key facts', 'type' => 'heading', 'help' => 'The strip under the photo and the “At a glance” card beside the page.'],
                'facts'        => ['label' => 'Key facts strip (one per line: Label | value)', 'type' => 'textarea', 'rows' => 6, 'help' => 'E.g. “Shape | You move: 3 stops”. Leave empty to show Depth, Nights, Effort and Group automatically.'],
                'at_a_glance'  => ['label' => 'At a glance (one per line: Label | value)', 'type' => 'textarea', 'rows' => 5, 'help' => 'A value of Gentle, Active or Demanding shows the effort dots.'],

                '_overview'    => ['label' => 'Overview & fit', 'type' => 'heading'],
                'headline'     => ['label' => 'Headline', 'type' => 'rich', 'max' => 200, 'help' => 'E.g. “A small group, a real route.”'],
                'description'  => ['label' => 'Overview text', 'type' => 'paras', 'rows' => 4],
                'fit_for'      => ['label' => 'This is for you if… (one per line)', 'type' => 'textarea', 'rows' => 4],
                'fit_not'      => ['label' => 'Perhaps not yet if… (one per line)', 'type' => 'textarea', 'rows' => 4],

                '_route'       => ['label' => 'Route & days', 'type' => 'heading'],
                'route_stops'  => ['label' => 'Route map stops (one per line: Place | stay)', 'type' => 'textarea', 'rows' => 4, 'help' => 'E.g. “Kitulgala | 2 nights”. Draws the dotted route map.'],
                'week'         => ['label' => 'Days (one per line: Day | Place | Title | text | tags)', 'type' => 'textarea', 'rows' => 7, 'help' => 'E.g. “1 | Negombo | Arrive and exhale | Meet the group … | Coast · gentle”.'],

                '_incl'        => ['label' => 'Included / not included / good to know', 'type' => 'heading'],
                'included'     => ['label' => 'Included (one per line)', 'type' => 'textarea', 'rows' => 8],
                'not_included' => ['label' => 'Not included (one per line)', 'type' => 'textarea', 'rows' => 4],
                'good_to_know' => ['label' => 'Good to know (one per line)', 'type' => 'textarea', 'rows' => 4],

                '_price'       => ['label' => 'Price & places', 'type' => 'heading'],
                'price_pp'     => ['label' => 'Price from (per person)', 'type' => 'text', 'max' => 80, 'help' => 'E.g. “USD 1,850”. Leave empty to show “[USD ——]”.'],
                'price_basis'  => ['label' => 'Price basis', 'type' => 'text', 'max' => 120, 'help' => 'E.g. “per person, twin-share”.'],
                'price_details'=> ['label' => 'Price notes (one per line)', 'type' => 'textarea', 'rows' => 3, 'help' => 'E.g. “Deposit 30%” and “Departure 12 March 2027”.'],
                'places_show'  => ['label' => 'Show the places card', 'type' => 'bool', 'default' => 0, 'help' => 'Switch on once departure dates are live.'],
                'places'       => ['label' => 'Places text', 'type' => 'text', 'max' => 160, 'help' => 'E.g. “6 places · 2 left”.'],

                '_faq'         => ['label' => 'Questions, reading & where next', 'type' => 'heading'],
                'faq'          => ['label' => 'Questions (one per line: Question | answer)', 'type' => 'textarea', 'rows' => 6],
                'reading'      => ['label' => 'Read before you go (one per line: Label | Title | link — link optional)', 'type' => 'textarea', 'rows' => 4],
                'related'      => ['label' => '“Where next?” cards', 'type' => 'text', 'max' => 255, 'help' => 'URL names separated by commas. Add “wellness:” before a Wellness Package, e.g. “the-mountain-path, wellness:heal”. Leave empty to show the next journeys.'],

                '_settings'    => ['label' => 'Settings, homepage & SEO', 'type' => 'heading'],
                'on_journeys_page' => ['label' => 'Show on the website (Journeys page and journey page)', 'type' => 'bool', 'default' => 1, 'help' => 'Hidden journeys can still be previewed while you are signed in.'],
                'is_featured'  => ['label' => 'Also show in the homepage Journeys section', 'type' => 'bool', 'default' => 0],
                'price_from'   => ['label' => 'Homepage price from (USD per night)', 'type' => 'decimal', 'default' => 0],
                'region'       => ['label' => 'Region', 'type' => 'text', 'max' => 80],
                'seo_title'    => ['label' => 'Page title for Google (optional)', 'type' => 'text', 'max' => 160],
                'seo_description' => ['label' => 'Description for Google (optional)', 'type' => 'textarea', 'max' => 300, 'rows' => 2],
                'sort_order'   => $sort,
            ],
        ],
        'wellness_programmes' => [
            'label' => 'Wellness Packages', 'singular' => 'package', 'icon' => 'leaf', 'placeholder_check' => true,
            'tabs'  => ['wellness_programmes' => 'Packages', 'wellness_categories' => 'Categories'],   // inner tabs on this screen
            'intro' => 'Each package has its own page (e.g. Arogya) and a card on the Wellness page. Sections of the package page only appear once they have content. Depths, paths and “suitable for” options are managed in the Categories tab.',
            'order' => 'sort_order, id', 'title' => 'title', 'search' => ['title', 'summary'],
            'list'  => ['image' => 'Image', 'title' => 'Package', 'depth' => 'Depth', 'path' => 'Path', 'nights' => 'Nights', 'sort_order' => 'Order', 'is_active' => 'Shown'],
            'view'  => fn(array $row) => package_url((string) $row['slug']),
            'fields' => [
                '_basics'       => ['label' => 'Package & card', 'type' => 'heading', 'help' => 'Shown on the Wellness page card and at the top of the package page.'],
                'title'         => ['label' => 'Package name', 'type' => 'text', 'required' => true, 'max' => 160],
                'slug'          => ['label' => 'URL name', 'type' => 'slug', 'required' => true, 'max' => 140, 'from' => 'title'],
                'depth'         => ['label' => 'Depth', 'type' => 'select', 'required' => true, 'options' => get_wellness_categories('depth')],
                'path'          => ['label' => 'Path', 'type' => 'select', 'required' => true, 'options' => get_wellness_categories('path')],
                'audience'      => ['label' => 'Suitable for', 'type' => 'checks', 'required' => true, 'options' => get_wellness_categories('audience'), 'default' => 'solo'],
                'nights'        => ['label' => 'Nights', 'type' => 'text', 'required' => true, 'max' => 40, 'help' => 'A number or a range, e.g. 7 or 3-5. Used in the page heading and the Nights filter.'],
                'image'         => ['label' => 'Card photo', 'type' => 'image', 'required' => true, 'max' => 255, 'help' => 'Landscape photo (4:3), about 1200px wide.'],
                'hero_image'    => ['label' => 'Package page photo (optional)', 'type' => 'image', 'max' => 255, 'help' => 'Large photo behind the package page heading, about 2400px wide. Leave empty to use the card photo.'],
                'summary'       => ['label' => 'Short description', 'type' => 'textarea', 'required' => true, 'max' => 300, 'rows' => 3, 'help' => 'Card text, and the line under the package name. E.g. “The Ayurvedic week. A full programme under a partner physician’s consultation, adjusted day by day.”'],

                '_facts'        => ['label' => 'Key facts', 'type' => 'heading', 'help' => 'The strip under the photo and the “At a glance” card beside the page.'],
                'facts'         => ['label' => 'Key facts strip (one per line: Label | value)', 'type' => 'textarea', 'rows' => 6, 'help' => 'E.g. “Shape | You stay, one place”. Leave empty to show Depth, Nights and Path automatically.'],
                'at_a_glance'   => ['label' => 'At a glance (one per line: Label | value)', 'type' => 'textarea', 'rows' => 5, 'help' => 'E.g. “Group | Private, 1–2 guests”.'],

                '_intro'        => ['label' => 'Introduction', 'type' => 'heading'],
                'headline'      => ['label' => 'Headline', 'type' => 'rich', 'max' => 200, 'help' => 'E.g. “A week to actually feel the difference.” Use <em>…</em> for a rust italic word.'],
                'description'   => ['label' => 'Introduction text', 'type' => 'paras', 'rows' => 5],

                '_fit'          => ['label' => 'Fit', 'type' => 'heading'],
                'fit_for'       => ['label' => 'This is for you if… (one per line)', 'type' => 'textarea', 'rows' => 4],
                'fit_not'       => ['label' => 'Perhaps not yet if… (one per line)', 'type' => 'textarea', 'rows' => 4],

                '_week'         => ['label' => 'Your week', 'type' => 'heading'],
                'week'          => ['label' => 'Days (one per line: Day | Label | Title | text | tags)', 'type' => 'textarea', 'rows' => 7, 'help' => 'E.g. “1 | Arrive | Arrive and be received | Private transfer, a quiet room … | Consultation · assessment”. Day can be a range such as 3–5. Label and tags are optional.'],
                'sample_day'    => ['label' => 'Sample day (one per line: Time | text)', 'type' => 'textarea', 'rows' => 7, 'help' => 'E.g. “07:00 | Gentle yoga and breathwork …”'],

                '_incl'         => ['label' => 'Included / not included', 'type' => 'heading'],
                'included'      => ['label' => 'Included (one per line)', 'type' => 'textarea', 'rows' => 8],
                'not_included'  => ['label' => 'Not included (one per line)', 'type' => 'textarea', 'rows' => 4],
                'addons'        => ['label' => 'Add-ons (one per line)', 'type' => 'textarea', 'rows' => 4],

                '_price'        => ['label' => 'Price panel', 'type' => 'heading'],
                'price_from'    => ['label' => 'Price from', 'type' => 'text', 'max' => 80, 'help' => 'E.g. “USD 2,450”. Leave empty to show “Price on request”.'],
                'price_basis'   => ['label' => 'Price basis', 'type' => 'text', 'max' => 120, 'help' => 'E.g. “per person, twin-share”.'],
                'price_details' => ['label' => 'Price notes (one per line)', 'type' => 'textarea', 'rows' => 3, 'help' => 'E.g. “Single supplement USD 600” and “A 30% deposit secures your dates”.'],

                '_faq'          => ['label' => 'Questions & related packages', 'type' => 'heading'],
                'faq'           => ['label' => 'Questions (one per line: Question | answer)', 'type' => 'textarea', 'rows' => 6],
                'related'       => ['label' => '“Step up, or step down” packages', 'type' => 'text', 'max' => 255, 'help' => 'URL names of other packages, separated by commas, e.g. “heal, revive”. Leave empty to suggest packages at the next depth up and down automatically.'],

                '_people'       => ['label' => 'Practitioner card', 'type' => 'heading', 'help' => 'Keep hidden until partner agreements are signed.'],
                'practitioner_show' => ['label' => 'Show the practitioner card', 'type' => 'bool', 'default' => 0],
                'practitioner'  => ['label' => 'Practitioner (Name | qualification | one line about them)', 'type' => 'text', 'max' => 300],

                '_read'         => ['label' => 'Read before you go', 'type' => 'heading'],
                'reading'       => ['label' => 'Articles (one per line: Label | Title | link — link optional)', 'type' => 'textarea', 'rows' => 4, 'help' => 'E.g. “Before | What an Ayurvedic consultation actually involves. | journal”. The link can be a page on this site or a full https:// address.'],

                '_seo'          => ['label' => 'Settings & SEO', 'type' => 'heading'],
                'seo_title'     => ['label' => 'Page title for Google (optional)', 'type' => 'text', 'max' => 160],
                'seo_description' => ['label' => 'Description for Google (optional)', 'type' => 'textarea', 'max' => 300, 'rows' => 2, 'help' => 'Leave empty to use the short description.'],
                'is_active'     => ['label' => 'Show on the website', 'type' => 'bool', 'default' => 1, 'help' => 'Hidden packages can still be previewed while you are signed in.'],
                'sort_order'    => $sort,
            ],
        ],
        'wellness_categories' => [
            'label' => 'Package Categories', 'singular' => 'category', 'icon' => 'grid',
            'hidden' => true, 'nav' => 'wellness_programmes',                                           // shown as a tab inside Wellness Packages
            'tabs'  => ['wellness_programmes' => 'Packages', 'wellness_categories' => 'Categories'],
            'intro' => 'The depths, paths and “suitable for” options used by Wellness Packages and by the filters on the Wellness page. New categories appear in both straight away.',
            'order' => "FIELD(type, 'depth', 'path', 'audience'), sort_order, id", 'title' => 'name',
            'list'  => ['type' => 'Type', 'name' => 'Name', 'slug' => 'URL name', 'sort_order' => 'Order'],
            'fields' => [
                'type'       => ['label' => 'Category type', 'type' => 'select', 'required' => true, 'options' => array_map(fn($t) => $t[0], WELLNESS_CATEGORY_TYPES)],
                'name'       => ['label' => 'Name', 'type' => 'text', 'required' => true, 'max' => 80, 'help' => 'Shown on cards, package pages and filters, e.g. “Healing” or “Families”.'],
                'slug'       => ['label' => 'URL name', 'type' => 'slug', 'required' => true, 'max' => 60, 'from' => 'name', 'help' => 'Leave empty to create it from the name. Changing it later disconnects packages that use the old one.'],
                'sort_order' => $sort,
            ],
        ],
        'posts' => [
            'label' => 'Journal & news', 'singular' => 'post', 'icon' => 'book',
            'intro' => 'Published posts appear in “From the journal” on the homepage, newest first (up to 9).',
            'order' => 'COALESCE(published_at, created_at) DESC, id DESC', 'title' => 'title', 'search' => ['title', 'category', 'excerpt'],
            'list'  => ['image' => 'Image', 'title' => 'Title', 'type' => 'Type', 'category' => 'Category', 'status' => 'Status', 'published_at' => 'Published'],
            'fields' => [
                'title'        => ['label' => 'Title', 'type' => 'text', 'required' => true, 'max' => 200],
                'slug'         => ['label' => 'URL name', 'type' => 'slug', 'required' => true, 'max' => 160, 'from' => 'title'],
                'type'         => ['label' => 'Type', 'type' => 'select', 'required' => true, 'options' => ['journal' => 'Journal', 'news' => 'News'], 'default' => 'journal'],
                'category'     => ['label' => 'Category', 'type' => 'text', 'required' => true, 'max' => 80, 'default' => 'Ayurveda & Education'],
                'excerpt'      => ['label' => 'Excerpt (card text)', 'type' => 'textarea', 'required' => true, 'max' => 300, 'rows' => 3],
                'body'         => ['label' => 'Article', 'type' => 'paras', 'rows' => 16],
                'image'        => ['label' => 'Image (portrait)', 'type' => 'image', 'required' => true, 'max' => 255],
                'status'       => ['label' => 'Status', 'type' => 'select', 'required' => true, 'options' => ['draft' => 'Draft — hidden', 'published' => 'Published — visible'], 'default' => 'draft'],
                'published_at' => ['label' => 'Publish date', 'type' => 'datetime', 'help' => 'Leave empty to use the moment it is first published.'],
            ],
        ],
        'enquiries' => [
            'label' => 'Enquiries', 'singular' => 'enquiry', 'icon' => 'mail', 'can_create' => false, 'export' => true,
            'intro' => 'Enquiries sent from the website. Update the status as you follow up.',
            'order' => 'created_at DESC, id DESC', 'title' => 'full_name', 'search' => ['full_name', 'email', 'country', 'message'],
            'list'  => ['created_at' => 'Received', 'full_name' => 'Name', 'email' => 'Email', 'interest' => 'Looking for', 'travel_month' => 'Travel', 'status' => 'Status'],
            // Same order as the "Begin your journey" form (partials/enquire.php)
            'fields' => [
                'status'         => ['label' => 'Status', 'type' => 'select', 'required' => true, 'options' => ['new' => 'New', 'contacted' => 'Contacted', 'closed' => 'Closed']],
                'full_name'      => ['label' => 'Full name', 'type' => 'readonly'],
                'email'          => ['label' => 'Email address', 'type' => 'readonly'],
                'country'        => ['label' => 'Country', 'type' => 'readonly'],
                'city'           => ['label' => 'City', 'type' => 'readonly'],
                'journey_stage'  => ['label' => 'Where are they on their wellness journey?', 'type' => 'readonly'],
                'interest'       => ['label' => 'What are they looking for?', 'type' => 'readonly'],
                'travel_month'   => ['label' => 'When would they like to travel?', 'type' => 'readonly'],
                'dates_flexible' => ['label' => 'Dates flexible', 'type' => 'readonly', 'format' => 'yesno'],
                'message'        => ['label' => 'Other details', 'type' => 'readonly'],
                'created_at'     => ['label' => 'Received', 'type' => 'readonly'],
            ],
        ],
        'subscribers' => [
            'label' => 'Subscribers', 'singular' => 'subscriber', 'icon' => 'users', 'can_create' => false, 'export' => true,
            'intro' => 'Newsletter sign-ups from the website footer.',
            'order' => 'created_at DESC, id DESC', 'title' => 'email', 'search' => ['email'],
            'list'  => ['email' => 'Email', 'status' => 'Status', 'source' => 'Source', 'created_at' => 'Signed up'],
            'fields' => [
                'email'      => ['label' => 'Email', 'type' => 'readonly'],
                'status'     => ['label' => 'Status', 'type' => 'select', 'required' => true, 'options' => ['subscribed' => 'Subscribed', 'unsubscribed' => 'Unsubscribed']],
                'source'     => ['label' => 'Source', 'type' => 'readonly'],
                'created_at' => ['label' => 'Signed up', 'type' => 'readonly'],
            ],
        ],
    ];
    return $entities;
}

function admin_entity(string $key): ?array
{
    $all = admin_entities();
    return isset($all[$key]) ? $all[$key] + ['key' => $key, 'table' => $key] : null;
}
